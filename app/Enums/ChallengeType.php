<?php

namespace App\Enums;

use App\Models\Boss;
use App\Models\Card;
use App\Models\Challenge;

enum ChallengeType: string
{
    case PlayCardXTimes = 'play_card_x_times';
    // case DealXDamage = 'deal_x_damage';
    // case HealXDamage = 'heal_x_damage';
    // case BuffDudesXTimes = 'buff_dudes_x_times';
    // case PlayXCardsInOneTurn = 'play_x_cards_in_one_turn';
    case DefeatBossXTimes = 'defeat_boss_x_times';
    // case EmoteXTimes = 'emote_x_times';

    public static function generate()
    {
        $type = collect(self::cases())->random();

        return new Challenge([
            'type' => $type->value,
            'meta' => match ($type) {
                self::PlayCardXTimes => (function () {
                    $card = Card::query()->format(Format::STANDARD)->get()->random();
                    $count = rand(5, 20);

                    return [
                        'count' => $count,
                        'card' => $card->id,
                        'rewards' => [
                            [
                                'type' => ChallengeRewards::CardExperience->value,
                                'amount' => (100* $card->cost) * $count,
                            ],
                        ],
                    ];
                })(),
                self::DefeatBossXTimes => (function () {
                    $boss = Boss::get()->random();
                    $count = 1;

                    return [
                        'boss' => $boss->id,
                        'count' => $count,
                        'rewards' => [['type' => ChallengeRewards::FoilCard->value]],
                    ];
                })(),
            },
        ]);
    }
}
