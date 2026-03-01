<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\Enums\BossType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Boss extends Model
{
    protected $fillable = [
        'boss_type',
        'attachment_id',
        'artist_id',
        'power',
        'energy_gain',
        'victories',
        'defeats',
    ];

    protected $casts = [
        'boss_type' => BossType::class,
        'power' => 'integer',
        'energy_gain' => 'integer',
        'victories' => 'integer',
        'defeats' => 'integer',
    ];

    public $with = [
        'attachment',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function games()
    {
        return $this->hasMany(BossGame::class, 'boss_id');
    }

    public function rewardAlternateArt()
    {
        return $this->morphToMany(AlternateArt::class, 'reward');
    }

    public function artifacts()
    {
        return $this->belongsToMany(Card::class, 'boss_artifacts', 'boss_id', 'card_id');
    }

    public function getNameAttribute(): string
    {
        return $this->boss_type->getLabel();
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->attachment?->path() ?? asset('/images/icon.jpg');
    }
}
