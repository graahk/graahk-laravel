<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;

enum BossType: string implements HasLabel
{
    use HasList;

    case TWO_FACED_DEVOURER = 'two_faced_devourer';
    case EL_DORADO_DEFENSES = 'el_dorado_defenses';
    case BLOOD_SERPENT_GOD = 'blood_serpent_god';
    case PELVIS_RESLEY = 'pelvis_resley';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TWO_FACED_DEVOURER => 'Two-Faced Devourer',
            self::EL_DORADO_DEFENSES => 'El Dorado Defenses',
            self::BLOOD_SERPENT_GOD => 'Blood Serpent God',
            self::PELVIS_RESLEY => 'Pelvis Resley',
        };
    }

    public function victoryLabel(): ?string
    {
        return match ($this) {
            self::TWO_FACED_DEVOURER => 'souls devoured',
            self::EL_DORADO_DEFENSES => 'invaders repelled',
            self::BLOOD_SERPENT_GOD => 'serpents fed',
            self::PELVIS_RESLEY => 'encores performed',
        };
    }

    public function defeatLabel(): ?string
    {
        return match ($this) {
            self::TWO_FACED_DEVOURER => 'defeats',
            self::EL_DORADO_DEFENSES => 'defenses breached',
            self::BLOOD_SERPENT_GOD => 'serpents subdued',
            self::PELVIS_RESLEY => 'shows cut short',
        };
    }
}
