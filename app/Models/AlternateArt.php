<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlternateArt extends Model
{
    protected $table = 'alternate_arts';

    protected $fillable = [
        'extended_name',
        'card_id',
        'artist_id',
        'in_packs',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'unlock', 'user_unlocks')
            ->where('enabled', true);
    }

    public function rewardText(): string
    {
        return 'You have unlocked alternate art for ' . ($this->extended_name ?? $this->card->name);
    }
}
