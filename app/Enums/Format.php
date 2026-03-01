<?php

namespace App\Enums;

use Carbon\Carbon;
use Illuminate\Support\Collection;

enum Format: string
{
    case WEEKLY = 'weekly';
    case WEEKLY_ENDED = 'weekly_ended';
    case STANDARD = 'standard';
    case CHAOS = 'chaos';
    case BOSS = 'boss';

    public function name(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'Weekly',
            self::WEEKLY_ENDED => 'Weekly (Ended)',
            self::STANDARD => 'Standard',
            self::CHAOS => 'Chaos',
            self::BOSS => 'Boss fight',
        };
    }

    public function description(): ?string
    {
        $weeklyTime = Carbon::now()->startOfWeek()->addDays(4)->hour(17)->diffForHumans();

        return match ($this) {
            self::WEEKLY, self::WEEKLY_ENDED => 'A set of 50 randomized cards that changes every week on friday (new set ' . $weeklyTime . ')',
            self::STANDARD => 'Standard format, includes all official cards in the game',
            self::CHAOS => 'You\'ll have access to all cards in the game, even the cards still in development, go nuts!<br>
                Beware that these cards may get changed/deleted over time.',
        };
    }

    public function icon(): ?string
    {
        return match ($this) {
            self::WEEKLY => 'gmdi-hourglass-empty-r',
            self::WEEKLY_ENDED => 'gmdi-hourglass-disabled-r',
            self::STANDARD => 'rpg-dragon',
            self::CHAOS => 'rpg-crown-of-thorns',
            self::BOSS => 'rpg-skull',
        };
    }

    public function style(): ?string
    {
        return match ($this) {
            self::WEEKLY, self::WEEKLY_ENDED => 'background-color: #FFD700; color: #181818;',
            self::STANDARD => 'background-color: #911BDE; color: #FFF;',
            self::CHAOS => 'background-color: #AC1616; color: #FFF;',
            self::BOSS => 'background-color: #FF4500; color: #000;',
        };
    }

    public function crossedOut(): bool
    {
        return match ($this) {
            self::WEEKLY_ENDED => true,
            default => false,
        };
    }

    public function isRecommended(): bool
    {
        return match ($this) {
            self::STANDARD => true,
            default => false,
        };
    }

    public function isBoss(): bool
    {
        return match ($this) {
            self::BOSS => true,
            default => false,
        };
    }

    public function isWeekly(): bool
    {
        return match ($this) {
            self::WEEKLY_ENDED, self::WEEKLY => true,
            default => false,
        };
    }

    public static function options(): Collection
    {
        return collect([
            self::STANDARD,
            self::WEEKLY,
            self::CHAOS,
        ]);
    }
}
