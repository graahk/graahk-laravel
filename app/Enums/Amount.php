<?php

namespace App\Enums;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Support\Contracts\HasLabel;

enum Amount: string implements HasLabel
{
    case FOR_EACH_DUDE_PLAYER = 'for_each_dude_player';
    case FOR_EACH_DUDE_PLAYER_EXCEPT_SELF = 'for_each_dude_player_except_self';
    case FOR_EACH_DUDE_OPPONENT = 'for_each_dude_opponent';
    case FOR_EACH_DUDE = 'for_each_dude';

    case FOR_EACH_ENERGY_PLAYER = 'for_each_energy_player';
    case FOR_EACH_ENERGY_OPPONENT = 'for_each_energy_opponent';

    case FOR_EACH_Y_POWER = 'for_each_y_power';

    case FOR_EACH_ARTIFACT_CHARGE = 'for_each_artifact_charge';

    case FOR_EACH_CARD_IN_HAND = 'for_each_card_in_hand';
    case FOR_EACH_CARD_IN_OPPONENT_HAND = 'for_each_card_in_opponent_hand';

    case FOR_EACH_Y_DUDE_IN_OPPONENT_DECK = 'for_each_y_dude_in_opponent_deck';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FOR_EACH_DUDE_PLAYER => 'For each dude you control',
            self::FOR_EACH_DUDE_PLAYER_EXCEPT_SELF => 'For each other dude you control',
            self::FOR_EACH_DUDE_OPPONENT => 'For each dude your opponent controls',
            self::FOR_EACH_DUDE => 'For each dude on the field',

            self::FOR_EACH_ENERGY_PLAYER => 'For each energy you control',
            self::FOR_EACH_ENERGY_OPPONENT => 'For each energy your opponent controls',

            self::FOR_EACH_Y_POWER => 'For each Y power',

            self::FOR_EACH_ARTIFACT_CHARGE => 'For each artifact charge',

            self::FOR_EACH_CARD_IN_HAND => 'For each card in hand',
            self::FOR_EACH_CARD_IN_OPPONENT_HAND => 'For each card in opponent hand',

            self::FOR_EACH_Y_DUDE_IN_OPPONENT_DECK => 'For each Y dude in opponent deck',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::FOR_EACH_DUDE_PLAYER => 'for each dude you control',
            self::FOR_EACH_DUDE_PLAYER_EXCEPT_SELF => 'for each other dude you control',
            self::FOR_EACH_DUDE_OPPONENT => 'for each dude your opponent controls',
            self::FOR_EACH_DUDE => 'for each dude on the field',

            self::FOR_EACH_ENERGY_PLAYER => 'for each energy you control',
            self::FOR_EACH_ENERGY_OPPONENT => 'for each energy your opponent controls',

            self::FOR_EACH_Y_POWER => 'for each {Y} power this dude has',

            self::FOR_EACH_ARTIFACT_CHARGE => 'for each charge on this artifact',

            self::FOR_EACH_CARD_IN_HAND => 'for each card in your hand',
            self::FOR_EACH_CARD_IN_OPPONENT_HAND => 'for each card in opponent hand',

            self::FOR_EACH_Y_DUDE_IN_OPPONENT_DECK => 'for each <i>{Y}</i> in your opponents deck',
        };
    }

    public function hasYField(): bool
    {
        return in_array($this, [
            self::FOR_EACH_Y_POWER,
            self::FOR_EACH_Y_DUDE_IN_OPPONENT_DECK,
        ]);
    }

    public static function fields(): array
    {
        return [
            Grid::make(3)->schema([
                TextInput::make('amount')
                    ->helperText('Put "X" to make the amount reactive')
                    ->reactive()
                    ->required(),

                Select::make('amount_special')
                    ->hidden(fn (Get $get) => $get('amount') !== 'X')
                    ->options(static::class)
                    ->reactive()
                    ->required(),

                TextInput::make('amount_y')
                    ->label('Amount/name (Y)')
                    ->hidden(fn (Get $get) => $get('amount') !== 'X' || ! self::tryFrom($get('amount_special'))?->hasYField())  
                    ->required(),

                TextInput::make('amount_multiplier')
                    ->hidden(fn (Get $get) => $get('amount') !== 'X')
                    ->reactive()
                    ->required(),
            ]),
        ];
    }
}
