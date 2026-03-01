<?php

namespace App\Models;

use App\Enums\Format;

class BossGame extends Game
{
    protected $fillable = [
        'uuid',
        'user_id',
        'boss_id',
        'data',
        'finished_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boss()
    {
        return $this->belongsTo(Boss::class, 'boss_id');
    }

    public function route(): string
    {
        return route('game.play-boss', $this);
    }

    public function getUser2Attribute(): string
    {
        return 10000 + $this->boss->id;
    }

    public function format(): Format
    {
        return Format::BOSS;
    }
}
