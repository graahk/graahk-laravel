<?php

namespace App\Models;

use App\Enums\Format;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Game extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',
        'name',
        'user_id_1',
        'user_id_2',
        'data',
        'finished_at',
    ];

    protected $casts = [
        'data' => 'array',
        'finished_at' => 'datetime',
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function scopeOngoing($query)
    {
        return $query->where(fn ($q) => $q->whereNull('finished_at'));
    }

    public function opponentId(string $playerId): string
    {
        return $this->user_id_1 == $playerId
            ? $this->user_id_2
            : $this->user_id_1;
    }

    public function route(): string
    {
        return route('game.play', $this);
    }

    public function format(): Format
    {
        return Deck::find(Arr::first($this->data['decks']))->format;
    }
}
