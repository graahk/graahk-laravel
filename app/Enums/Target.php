<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Support\Contracts\HasLabel;

enum Target: string implements HasLabel
{
    use HasList;

    case PLAYER = 'player';
    case OPPONENT = 'opponent';

    case TARGET_DUDE = 'target_dude';
    case TARGET_ANYTHING = 'target_anything';
    case TARGET_HAND = 'target_hand';
    case TARGET_PLAYER = 'target_player';

    case ALL = 'all';
    case ALL_DUDES = 'all_dudes';
    case ALL_PLAYERS = 'all_players';
    case ALL_HAND = 'all_hand';
    case ALL_HAND_DECK = 'all_hand_deck';

    case OPPONENT_LEFT_MOST_DUDE = 'opponent_left_most_dude';
    case OPPONENT_RIGHT_MOST_DUDE = 'opponent_right_most_dude';

    case PLAYER_LEFT_MOST_DUDE = 'player_left_most_dude';
    case PLAYER_RIGHT_MOST_DUDE = 'player_right_most_dude';

    case PLAYER_LEFT_MOST_HAND = 'player_left_most_hand';
    case PLAYER_RIGHT_MOST_HAND = 'player_right_most_hand';

    case SOURCE = 'source';
    case ATTACKER = 'attacker';

    case ITSELF = 'itself';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PLAYER => 'Player - you',
            self::OPPONENT => 'Player - opponent',

            self::TARGET_DUDE => 'Target - dude',
            self::TARGET_ANYTHING => 'Target - player or dude',
            self::TARGET_HAND => 'Target - card in hand',
            self::TARGET_PLAYER => 'Target - player',

            self::ALL => 'All - players + dudes',
            self::ALL_DUDES => 'All - dudes',
            self::ALL_PLAYERS => 'All - players',
            self::ALL_HAND => 'All - hand',
            self::ALL_HAND_DECK => 'All - hand and deck',

            self::OPPONENT_LEFT_MOST_DUDE => 'Dudes opponent - Left most dude',
            self::OPPONENT_RIGHT_MOST_DUDE => 'Dudes opponent - Right most dude',

            self::PLAYER_RIGHT_MOST_DUDE => 'Dudes player - Right most dude',
            self::PLAYER_LEFT_MOST_DUDE => 'Dudes player - Left most dude',

            self::PLAYER_RIGHT_MOST_HAND => 'Hand player - Right most card in hand',
            self::PLAYER_LEFT_MOST_HAND => 'Hand player - Left most card in hand',

            self::SOURCE => 'Source (the card that was targeted)',
            self::ATTACKER => 'Attacker (the card that is attacking)',

            self::ITSELF => 'Itself',
        };
    }

    public function toText(array $parameters): ?string
    {
        $extra = collect($parameters['conditions'] ?? [])
            ->map(fn ($value) => TargetCondition::tryFrom($value['condition'])?->toText($value) ?? '')
            ->join(' ');

        return trim(match ($this) {
            self::PLAYER => 'you',
            self::OPPONENT => 'your opponent',

            self::TARGET_DUDE => 'target dude',
            self::TARGET_ANYTHING => 'target dude or player',
            self::TARGET_HAND => 'target card in your hand',
            self::TARGET_PLAYER => 'target player',

            self::ALL => 'everything',
            self::ALL_PLAYERS => 'all players',
            self::ALL_DUDES => 'all dudes',
            self::ALL_HAND => 'all cards in your hand',
            self::ALL_HAND_DECK => 'all dudes in your hand and deck',

            self::OPPONENT_LEFT_MOST_DUDE => 'the left most dude your opponent controls',
            self::OPPONENT_RIGHT_MOST_DUDE => 'the right most dude your opponent controls',

            self::PLAYER_RIGHT_MOST_DUDE => 'the right most dude you control',
            self::PLAYER_LEFT_MOST_DUDE => 'the left most dude you control',

            self::PLAYER_RIGHT_MOST_HAND => 'the right most card in your hand',
            self::PLAYER_LEFT_MOST_HAND => 'the left most card in your hand',

            self::SOURCE => 'that dude',
            self::ATTACKER => 'the attacking dude',

            self::ITSELF => 'this',
        } . (filled($extra) ? " {$extra}" : ''));
    }

    public function schema(): array
    {
        return match ($this) {
            self::TARGET_DUDE,
            self::TARGET_ANYTHING,
            self::TARGET_HAND,
            self::ALL,
            self::ALL_DUDES,
            self::ALL_PLAYERS,
            self::ALL_HAND_DECK,
            self::ALL_HAND => [
                Repeater::make('conditions')
                    ->reactive()
                    ->schema([
                        Select::make('condition')
                            ->hiddenLabel()
                            ->reactive()
                            ->options(TargetCondition::class)
                            ->required(),

                        Grid::make(1)->schema(fn (Get $get) =>
                            TargetCondition::tryFrom($get('condition'))?->schema() ?? []
                        ),
                    ]),
            ],
            default => [],
        };
    }
}
