<?php

namespace App\Livewire\Library;

use App\Models\Card;
use App\Models\Set;
use Livewire\Component;

class Index extends Component
{
    public null | int $setId = 1;

    public null | Card $highlighted = null;

    public function setHighlighted(null | int $card)
    {
        $this->highlighted = Card::find($card);
    }

    public function render()
    {
        return view('livewire.library.index', [
            'cards' => Set::find($this->setId)?->cards()->noTokens()->get() ?? collect(),
            'sets' => Set::latest()
                ->where('artifacts_set', false)
                ->where('boss_cards', false)
                ->get()
                ->sortBy([
                    ['beta', 'asc'],
                    ['created_at', 'asc'],
                ]),
        ]);
    }
}
