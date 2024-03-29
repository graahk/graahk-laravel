<?php

namespace App\Enums;

use App\Enums\Traits\HasList;
use Filament\Support\Contracts\HasLabel;

enum Trigger: string implements HasLabel
{
    use HasList;

    case ENTER_FIELD = 'enter_field';
    case LEAVE_FIELD = 'leave_field';
    case CAST_RUSE = 'cast_ruse';

    case START_TURN = 'start_turn';
    case END_TURN = 'end_turn';

    case GAIN_ENERGY = 'gain_energy';

    case PLAY_DUDE = 'play_dude';
    case PLAYER_PLAY_DUDE = 'player_play_dude';
    case OPPONENT_PLAY_DUDE = 'opponent_play_dude';

    case ATTACK = 'attack';
    case AFTER_ATTACK = 'after_attack';
    case SURVIVE_DAMAGE = 'survive_damage';
    case TOOK_DAMAGE = 'took_damage';
    case KILLING_BLOW = 'killing_blow';

    case DUDE_DIES = 'dude_dies';
    case PLAYER_DUDE_DIES = 'player_dude_dies';
    case OPPONENT_DUDE_DIES = 'opponent_dude_dies';

    case DRAW_CARD = 'draw_card';
    case DRAW_SECOND_CARD = 'draw_second_card';

    case DUDE_HEALS_ANOTHER = 'dude_heals_another';
    case HEALED = 'healed';
    case DUDE_FULLY_HEALED = 'dude_fully_healed';

    case HEALING_REVERSED = 'healing_reversed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'Enters the field',
            self::LEAVE_FIELD => 'Leaves the field',
            self::CAST_RUSE => 'Cast ruse',
            self::START_TURN => 'Start of turn',
            self::END_TURN => 'End of turn',
            self::GAIN_ENERGY => 'Gain energy',
            self::PLAY_DUDE => 'Anyone plays dude',
            self::PLAYER_PLAY_DUDE => 'You play dude',
            self::OPPONENT_PLAY_DUDE => 'Opponent plays dude',
            self::AFTER_ATTACK => 'After attacking',
            self::SURVIVE_DAMAGE => 'Survives damage',
            self::TOOK_DAMAGE => 'Took damage',
            self::ATTACK => 'Attacks',
            self::KILLING_BLOW => 'After a killing blow',
            self::DUDE_DIES => 'Dude dies',
            self::PLAYER_DUDE_DIES => 'Dude you control dies',
            self::OPPONENT_DUDE_DIES => 'Dude your opponent controls dies',
            self::DRAW_CARD => 'After drawing a card',
            self::DRAW_SECOND_CARD => 'After drawing a card after your first',
            self::DUDE_HEALS_ANOTHER => 'Dude heals another',
            self::HEALED => 'Healed',
            self::HEALING_REVERSED => 'Healing reversed (has no effect dropdown)',
            self::DUDE_FULLY_HEALED => 'Healed to full',
        };
    }

    public function toText(): ?string
    {
        return match ($this) {
            self::ENTER_FIELD => 'When this dude enters the field,',
            self::LEAVE_FIELD => 'When this dude dies,',
            self::CAST_RUSE => 'When played,',
            self::START_TURN => 'At the start of your turn,',
            self::END_TURN => 'At the end of your turn,',
            self::GAIN_ENERGY => 'When you gain energy,',
            self::PLAY_DUDE => 'Whenever anyone plays a dude,',
            self::PLAYER_PLAY_DUDE => 'When you play a dude,',
            self::OPPONENT_PLAY_DUDE => 'When your opponent plays a dude,',
            self::AFTER_ATTACK => 'After this dude attacks,',
            self::SURVIVE_DAMAGE => 'When this dude survives damage,',
            self::TOOK_DAMAGE => 'When this dude takes damage,',
            self::ATTACK => 'When this dude attacks,',
            self::KILLING_BLOW => 'When this dude kills another dude,',
            self::DUDE_DIES => 'When a dude dies,',
            self::PLAYER_DUDE_DIES => 'When a dude you control dies,',
            self::OPPONENT_DUDE_DIES => 'When a dude your opponent controls dies,',
            self::DRAW_CARD => 'After you draw a card,',
            self::DRAW_SECOND_CARD => 'After you draw a card after your first,',
            self::DUDE_HEALS_ANOTHER => 'Whenever a dude is healed,',
            self::HEALED => 'When this dude is healed,',
            self::HEALING_REVERSED => 'Whenever something would heal, it deals that much damage instead',
            self::DUDE_FULLY_HEALED => 'When this dude is fully healed,',
        };
    }

    public function hasEffectDropdown(): bool
    {
        return match ($this) {
            self::HEALING_REVERSED => false,
            default => true,
        };
    }
}
