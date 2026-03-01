<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'meta',
        'rewards',
        'completed',
    ];

    protected $casts = [
        'type' => ChallengeType::class,
        'meta' => 'array',
        'rewards' => 'array',
        'completed' => 'boolean',
    ];
}
