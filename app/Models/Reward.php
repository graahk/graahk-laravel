<?php

namespace App\Models;

use App\Enums\BossType;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'boss_type',
        'reward_type',
        'reward_id',
    ];

    protected $casts = [
        'boss_type' => BossType::class,
    ];
}
