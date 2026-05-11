<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\CardCache;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    protected $fillable = [
        'name',
        'code',
        'attachment_id',
        'icon_id',
        'beta',
        'artifacts_set',
        'boss_cards',
    ];

    protected $casts = [
        'beta' => 'boolean',
        'artifacts_set' => 'boolean',
        'boss_cards' => 'boolean',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function icon()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class);
    }

    public static function booted()
    {
        static::saved(function () {
            CardCache::flush();
        });

        static::addGlobalScope(function ($query) {
            if (Filament::isServing()) {
                return;
            }

            if (in_array(auth()->user()?->id ?? null, [4, 12])) {
                return;
            }

            $query->where('code', '!=', 'SHR');
        });
    }
}
