<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Contracts\HasLabel;

enum Animation: string implements HasLabel
{
    use HasList;

    case CIRCLE_EXPLOSION = 'circle_explosion';
    case CTHULHULHULHU = 'cthulhulhulhu';
    case GREAT_DEVOURING = 'great_devouring';
    case ENERGY_PULSE = 'energy_pulse';
    case FIRE_EXPLOSION = 'fire_explosion';
    case GROUND_BURST = 'ground_burst';
    case PROJECTILE = 'projectile';
    case UNNAMED_ONE = 'unnamed_one';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CIRCLE_EXPLOSION => 'Circle explosion',
            self::CTHULHULHULHU => 'Cthulhulhulhu',
            self::GREAT_DEVOURING => 'Great devouring',
            self::ENERGY_PULSE => 'Energy pulse',
            self::FIRE_EXPLOSION => 'Fire explosion',
            self::GROUND_BURST => 'Ground burst (entrance effect)',
            self::PROJECTILE => 'Projectile',
            self::UNNAMED_ONE => 'Blessed arrival (unnamed one effect)',
        };
    }

    public function schema(string $prefix = ''): array
    {
        return match ($this) {
            self::CIRCLE_EXPLOSION => [
                Select::make("{$prefix}color")->required()->options([
                    'blue' => 'Blue',
                    'gray' => 'Gray',
                    'purple' => 'Purple',
                    'red' => 'Red',
                    'yellow' => 'Yellow',
                ]),
            ],
            self::ENERGY_PULSE => [
                Select::make("{$prefix}color")->required()->options([
                    'blue' => 'Blue',
                    'red' => 'Red',
                    'yellow' => 'Yellow',
                ]),
            ],
            self::PROJECTILE => [
                Select::make("{$prefix}type")->required()->options([
                    'acid' => 'Acid',
                    'bomb' => 'Bomb',
                    'broken_bottle' => 'Broken bottle',
                    'food' => 'Food',
                    'red_laser' => 'Red laser',
                    'knife' => 'Knife',
                    'ice' => 'Ice block',
                    'needle' => 'Needle',
                    'web' => 'Webbing',
                    'withering' => 'Withering',
                ]),

                TextInput::make("{$prefix}size")
                    ->helperText('A normal size is around 200')
                    ->required(),

                Checkbox::make("{$prefix}thrown"),
            ],
            default => [],
        };
    }
}
