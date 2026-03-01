<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;

enum CardType: string implements HasLabel
{
    use HasList;

    case DUDE = 'dude';
    case TOKEN = 'token';
    case RUSE = 'ruse';
    case ARTIFACT = 'artifact';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DUDE => 'Dude',
            self::TOKEN => 'Token',
            self::RUSE => 'Ruse',
            self::ARTIFACT => 'Artifact',
        };
    }

    public static function filterOptions(): Collection
    {
        return collect(self::cases())
            ->reject(fn (self $type) => in_array($type, [self::TOKEN, self::ARTIFACT]))
            ->mapWithKeys(fn (self $type) => [$type->value => $type->getLabel()]);
    }
}
