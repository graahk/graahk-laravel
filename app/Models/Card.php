<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\CardCache;
use App\Enums\CardType;
use App\Enums\Effect;
use App\Enums\Format;
use App\Enums\Keyword;
use App\Enums\Tribe;
use App\Enums\Trigger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Card extends Model
{
    protected $fillable = [
        'name',
        'attachment_id',
        'artist_id',
        'type',
        'cost',
        'power',
        'tribes',
        'effects',
        'keywords',
        'masked_text',
        'enter_speed',
        'entrance_animation',
    ];

    protected $casts = [
        'type' => CardType::class,
        'tribes' => 'array',
        'effects' => 'array',
        'keywords' => 'array',
        'enter_speed' => 'integer',
        'entrance_animation' => 'array',
    ];

    public $with = [
        'attachment',
        'artist',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function sets()
    {
        return $this->belongsToMany(Set::class);
    }

    public function experience()
    {
        return $this->belongsToMany(User::class, 'experience')
            ->withPivot('experience');
    }

    public function scopeDudes($query)
    {
        return $query->where('type', CardType::DUDE);
    }

    public function scopeRuses($query)
    {
        return $query->where('type', CardType::RUSE);
    }

    public function scopeTokens($query)
    {
        return $query->where('type', CardType::TOKEN);
    }

    public function scopeNoTokens($query)
    {
        return $query->where('type', '!=', CardType::TOKEN);
    }

    public function scopeFormat($query, Format $format)
    {
        return $query->whereHas('sets', function ($query) use ($format) {
            $query->where('beta', match ($format) {
                Format::STANDARD => false,
                default => true,
            });
        });
    }

    public function getTribes(): Collection
    {
        return Collection::wrap($this->tribes)->map(function (string $tribe) {
            return Tribe::from($tribe)->toText();
        });
    }

    public function toText(): string
    {
        $keywords = Collection::wrap($this->keywords)->map(function (string $keyword) {
            return '<strong>' . Keyword::from($keyword)->toText() . '.</strong>';
        });

        if ($this->masked_text) {
            return ($keywords->count() ? $keywords->join(' ') . ' ' : '')
                . $this->masked_text;
        }

        $effects = Collection::wrap($this->effects);
        $text = '';

        for ($i = 0; $i < $effects->count(); $i++) {
            $trigger = null;
            $effect = $effects[$i];

            if ($effect['trigger'] === ($effects[$i - 1]['trigger'] ?? false)) {
                $trigger = ', then';
            }

            $text .= trim(implode(' ', [
                $trigger ?? Trigger::tryFrom($effect['trigger'])?->toText($effect),
                Effect::tryFrom($effect['effect'])?->toText($effect),
            ]));

            if ($effect['trigger'] !== ($effects[$i + 1]['trigger'] ?? false)) {
                $text .= '. ';
            }
        }

        return trim(
            collect([$text])
                ->prepend($keywords)
                ->flatten()
                ->join(' ')
        );
    }

    public function toJavaScript(null | User $user = null): array
    {
        $user ??= auth()->user();

        $card = [
            'id' => $this->id,
            'uuid' => (string) Str::uuid(),
            'name' => $this->name,
            'image' => $this->getMedia()->path(),
            'cost' => $this->cost,
            'power' => $this->power,
            'tribes' => $this->tribes,
            'tribesText' => $this->getTribes()->join(', '),
            'text' => $this->toText(),
            'keywords' => $this->keywords,
            'effects' => $this->effects,
            'type' => $this->type?->value,
            'ready' => false,
            'enterSpeed' => $this->enter_speed,
            'entranceAnimation' => $this->entrance_animation,
            'level' => $this->getLevel($user),
            'sets' => $this->sets->pluck('id')->toArray(),
            'artist' => $this->artist?->slug,
        ];

        return [
            ...$card,
            'original' => $card,
        ];
    }

    public function getLevel(null | User $user = null): int
    {
        // return 1;
        // return 2;
        // return 3;
        // return 4;

        $user ??= auth()->user();

        return Cache::rememberForever("cards-level-{$this->id}-{$user?->id}", function () use ($user) {
            $user = $user?->id ?? auth()->id();

            $experience = $this->experience->where('id', $user)->first()
                ?->pivot->experience;

            if ($experience >= 4000) return 4;
            elseif ($experience >= 1500) return 3;
            elseif ($experience >= 500) return 2;
            else return 1;
        });
    }

    public  function getMedia(): Attachment
    {
            return $this->attachment;
        $alternateArt = AlternateArt::where('card_id', $this->id)
            ->whereHas('users', fn ($query) => $query->where('user_id', auth()->id()))
            ->first();

        if (! $alternateArt) {
            // Return the normal art
            // return collect([[ 'depth' => 0, 'path' => $this->attachment->path() ]]);
            return $this->attachment;
        }

        // Return the alternate art
        return Attachment::find($alternateArt->attachments[0]['attachment']);
        // return collect($alternateArt->attachments)->map(fn (array $attachment) => [
        //     'depth' => (int) ($attachment['depth'] ?? 0),
        //     'path' => Attachment::find($attachment['attachment'])->path(),
        // ])->sortBy('depth');
    }

    public static function getName(int $id): string
    {
        return Cache::rememberForever("card-name-{$id}", function () use ($id) {
            return static::find($id)->name;
        });
    }

    public static function booted()
    {
        static::addGlobalScope('sorted', function ($query) {
            $query->orderBy('cost')->orderBy('name');
        });

        static::addGlobalScope('hasAttachment', function ($query) {
            $query->has('attachment');
        });

        static::saving(function (Card $card) {
            Cache::forget("card-name-{$card->id}");
            Cache::forget("{$card->id}-tooltip");

            if ($card->type === CardType::RUSE) {
                $card->power = null;

                if (! in_array(Tribe::RUSE->value, $card->tribes)) {
                    $tribes = [Tribe::RUSE->value, ...$card->tribes];
                    $card->tribes = $tribes;
                }
            }
        });

        static::saved(function () {
            CardCache::flush();
        });
    }
}
