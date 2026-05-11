<?php

namespace App\Livewire\Packs;

use App\Models\Card;
use App\Models\Set;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $set = Set::find(collect([1, 6, 10])->random());
        $cards = $set->cards()->noTokens()->get()->shuffle()->take(7);

        $cards->each(function (Card $card, int $key) {
            if (in_array($key, [0, 1, 2, 3])) {
                return $card->setLevel(1);
            }

            if (in_array($key, [4, 5])) {
                return $card->setLevel(collect([3])->random());
            }

            // $card->setLevel(collect([2, 2, 2, 2, 3, 3, 3, 4])->random());
            $card->setLevel(collect([4])->random());
        });

        return view('livewire.packs.index', [
            'set' => $set,
            'cards' => $cards,
        ]);
    }
}
