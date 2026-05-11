<?php

namespace App\Livewire\Packs;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Set;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $set = Set::find(collect([
            1,  // Base
            // 6,  // Siege on El Dorado
            // 10, // Ominous Atlantis
        ])->random());
        $cards = $set->cards()->noTokens()->get()->shuffle()->take(7);

        $cards->transform(function (Card $card, int $key) {
            if ($card->alternateArts->isNotEmpty() && rand(0, 3) === 0) {
                $card->alternateArt = $card->alternateArts->random();
            }

            if (in_array($key, [0, 1, 2, 3])) {
                return $card->setLevel(1);
            }

            if (in_array($key, [4, 5])) {
                return $card->setLevel(collect([3])->random());
            }

            $card->setLevel(collect([3, 3, 4])->random());

            return $card;
        });

        $cards->each(function (Card $card) {
            Collection::add($card);
        });

        return view('livewire.packs.index', [
            'set' => $set,
            'cards' => $cards,
        ]);
    }
}
