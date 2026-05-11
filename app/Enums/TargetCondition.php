<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use App\Models\Card;
use Filament\Forms\Components\Select;
use Filament\Support\Contracts\HasLabel;

enum TargetCondition: string implements HasLabel
{
    use HasList;

    case OWNER = 'owner';
    case NOT_SELF = 'not_self';
    case TRIBE = 'tribe';
    case NOT_TRIBE = 'not_tribe';
    case SPECIFIC_CARD = 'specific_card';
    case HAS_KEYWORD = 'has_keyword';
    case IS_DAMAGED = 'is_damaged';
    case IS_UNCHANGED = 'is_unchanged';
    case IS_BUFFED = 'is_buffed';
    case DAMAGED_THIS_TURN = 'was_damaged_this_turn';
    case IS_STUNNED = 'is_stunned';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OWNER => 'Owner is',
            self::NOT_SELF => 'Except itself',
            self::TRIBE => 'Has tribe',
            self::NOT_TRIBE => 'Does not have tribe',
            self::SPECIFIC_CARD => 'Is a specific card',
            self::HAS_KEYWORD => 'Has keyword',
            self::IS_DAMAGED => 'Is damaged',
            self::IS_UNCHANGED => 'Power is unchanged',
            self::IS_BUFFED => 'Is buffed',
            self::DAMAGED_THIS_TURN => 'Was damaged this turn',
            self::IS_STUNNED => 'Is stunned'
        };
    }

    public function toText(array $parameters): ?string
    {
        $tribe = Tribe::tryFrom($parameters['tribe'] ?? null)?->toText();
        $keyword = Keyword::tryFrom($parameters['keyword'] ?? null)?->toText();

        if (isset($parameters['card'])) {
            $parameters['card'] = Card::find($parameters['card'])->name ?? $parameters['card'];
        }

        return match ($this) {
            self::NOT_SELF => 'except this',
            self::TRIBE => "with tribe <i>{$tribe}</i>",
            self::NOT_TRIBE => "without tribe <i>{$tribe}</i>",
            self::OWNER => match ($parameters['owner']) {
                Target::PLAYER->value => 'you control',
                Target::OPPONENT->value => 'your opponent controls',
            },
            self::SPECIFIC_CARD => "to <i>{$parameters['card']}</i>",
            self::HAS_KEYWORD => "with <strong>{$keyword}</strong>",
            self::IS_DAMAGED => 'that is damaged',
            self::IS_UNCHANGED => 'that has its original power',
            self::IS_BUFFED => 'that is buffed',
            self::DAMAGED_THIS_TURN => 'that was damaged this turn',
            self::IS_STUNNED => 'that is stunned'
        };
    }

    public function schema(): array
    {
        return match ($this) {
            self::OWNER => [
                Select::make('owner')
                    ->required()
                    ->hiddenLabel()
                    ->options([
                        Target::PLAYER->value => 'Player',
                        Target::OPPONENT->value => 'Opponent',
                    ]),
            ],
            self::TRIBE,
            self::NOT_TRIBE => [
                Select::make('tribe')
                    ->required()
                    ->hiddenLabel()
                    ->options(Tribe::class),
            ],
            self::SPECIFIC_CARD => [
                Select::make('card')
                    ->required()
                    ->hiddenLabel()
                    ->options(fn () => Card::orderBy('name')->pluck('name', 'id')),
            ],
            self::HAS_KEYWORD => [
                Select::make('keyword')
                    ->required()
                    ->hiddenLabel()
                    ->options(Keyword::class),
            ],
            default => [],
        };
    }
}
