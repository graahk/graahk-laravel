<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;

enum Tribe: string implements HasLabel
{
    use HasList;

    case HUMAN = 'human';
    case SPOIDER = 'spoider';
    case CTHULHIAN = 'cthulhian';
    case ELDER_GOD = 'elder_god';
    case GOD = 'god';
    case WILDLIFE = 'wildlife';
    case FATED = 'fated';
    case PHANTOM = 'phantom';
    case GILLED_GUILD = 'gilled_guild';
    case DESPAIR = 'despair';
    case EL_DORADO = 'el_dorado';
    case CYCLIST = 'cyclist';
    case SHROOMS = 'shrooms';
    case LIMBO = 'limbo';

    case RUSE = 'ruse';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HUMAN => 'Human',
            self::SPOIDER => 'Spoider',
            self::CTHULHIAN => 'Cthulhian',
            self::ELDER_GOD => 'Elder God',
            self::GOD => 'God',
            self::WILDLIFE => 'Wildlife',
            self::FATED => 'Fated',
            self::PHANTOM => 'Phantom',
            self::GILLED_GUILD => 'Gilled Guild',
            self::DESPAIR => 'Despair',
            self::EL_DORADO => 'El Dorado',
            self::CYCLIST => 'Cyclist',
            self::RUSE => 'Ruse',
            self::SHROOMS => 'Shrooms',
            self::LIMBO => 'Limbo',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::HUMAN => 'Human',
            self::SPOIDER => 'Spoider',
            self::CTHULHIAN => 'Cthulhian',
            self::ELDER_GOD => 'Elder God',
            self::GOD => 'God',
            self::WILDLIFE => 'Wildlife',
            self::FATED => 'Fated',
            self::PHANTOM => 'Phantom',
            self::GILLED_GUILD => 'Gilled Guild',
            self::DESPAIR => 'Despair',
            self::EL_DORADO => 'El Dorado',
            self::CYCLIST => 'Cyclist',
            self::RUSE => 'Ruse',
            self::SHROOMS => 'Shrooms',
            self::LIMBO => 'Limbo',
        };
    }
}
