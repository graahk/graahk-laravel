<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;

enum Keyword: string implements HasLabel
{
    use HasList;

    case PROTECT = 'protect';
    case RUSH = 'rush';
    case GHOSTLY = 'ghostly';
    case SCENERY = 'scenery';
    case TIRELESS = 'tireless';
    case WITHERING = 'withering';
    case INNUMERABLE = 'innumerable';
    case SCORCHING = 'scorching';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Speedy',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
            self::WITHERING => 'Withering',
            self::INNUMERABLE => 'Innumerable',
            self::SCORCHING => 'Scorching',
        };
    }

    public function toText(array $parameters = []): ?string
    {
        return match ($this) {
            self::PROTECT => 'Protect',
            self::RUSH => 'Speedy',
            self::GHOSTLY => 'Ghostly',
            self::SCENERY => 'Scenery',
            self::TIRELESS => 'Tireless',
            self::WITHERING => 'Withering',
            self::INNUMERABLE => 'Innumerable',
            self::SCORCHING => 'Scorching',
        };
    }

    public function description(): ?string
    {
        return match ($this) {
            self::PROTECT => 'This dude must be attacked first, if able',
            self::RUSH => 'Can attack in the same turn this dude was played',
            self::GHOSTLY => 'This dude cannot be directly targeted by  special abilities on dudes or ruses',
            self::SCENERY => 'This dude cannot attack or deal damage',
            self::TIRELESS => 'This dude does not die when its power reaches 0',
            self::WITHERING => 'Any damage dealt to this dude will kill it (ignoring Tireless)',
            self::INNUMERABLE => 'You can have any number of this card in your deck',
            self::SCORCHING => 'If a target with Scorching is attacked, it deals 100 damage to the attacker',
        };
    }
}
