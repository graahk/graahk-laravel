<?php

namespace App\Http\Controllers\Api;

use App\Enums\Keyword;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\User;

class CardController extends Controller
{
    private array $extras = [];

    public function show(Card $card, null | User $user = null)
    {
        return response()->json(
            $card->toJavaScript($user)
        );
    }

    public function tooltip(Card $card)
    {
        $this->getExtras($card);

        return view('components.tooltip', [
            'data' => [
                'name' => $card->name,
                'text' => $card->toText(),
                'tribes' => $card->getTribes()->join(', '),
                'cost' => $card->cost,
                'power' => $card->power,
                'extras' => $this->extras,
            ],
        ])->render();
    }

    private function getExtras(Card $card)
    {
        foreach ($card->keywords as $keyword) {
            $keyword = Keyword::from($keyword);
            $this->extras[$keyword->value] = [
                'name' => $keyword->toText(),
                'text' => $keyword->description(),
            ];
        }

        collect($card->effects)
            ->filter(fn ($e) => in_array($e['effect'], ['silence']))
            ->each(function () {
                $this->extras['silence'] = [
                    'name' => 'Stifle',
                    'text' => 'A stifled dude will lose all effects and keywords'
                ];
            });

        collect($card->effects)
            ->filter(fn ($e) => in_array($e['effect'], ['stun']))
            ->each(function () {
                $this->extras['stun'] = [
                    'name' => 'Stun',
                    'text' => 'A stunned dude will not be able to attack next turn'
                ];
            });

        collect($card->effects)
            ->filter(fn ($e) => in_array($e['effect'], ['give_keyword']))
            ->each(function ($effect) {
                $keyword = Keyword::from($effect['keyword']);
                $this->extras[$keyword->value] = [
                    'name' => $keyword->toText(),
                    'text' => $keyword->description(),
                ];
            });

        collect($card->effects)->filter(fn ($e) => in_array($e['effect'], [
            'spawn_token',
            'spawn_dude',
        ]))->each(function ($card) {
            $card = Card::find($card['token'] ?? $card['dude']);

            if (! $card) {
                return;
            }

            $this->extras[$card->id] = [
                'name' => $card->name,
                'text' => $card->toText(),
                'tribes' => $card->getTribes()->join(', '),
                'cost' => $card->cost,
                'power' => $card->power,
            ];

            if (! isset($this->extras[$card->id])) {
                $this->getExtras($card);
            }
        });

        collect($card->effects)
            ->pluck('conditions')
            ->flatten(1)
            ->filter(fn ($e) => in_array($e['condition'] ?? null, ['has_keyword']))
            ->each(function ($effect) {
                $keyword = Keyword::from($effect['keyword']);
                $this->extras[$keyword->value] = [
                    'name' => $keyword->toText(),
                    'text' => $keyword->description(),
                ];
            });
    }
}
