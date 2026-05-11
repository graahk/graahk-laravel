<?php

namespace App\Livewire\Collection;

use App\Models\Card;
use App\Models\Collection;
use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        app('site')->title('Collection');
    }

    public function render()
    {
        $cards = Collection::where('user_id', auth()->id())
            ->where('collectible', 'LIKE', 'card:%')
            ->get()
            ->map(function (Collection $collection) {
                $parts = collect(explode('/', $collection->collectible))
                    ->mapWithKeys(function (string $part) {
                        $split = explode(':', $part, 2);

                        return [$split[0] => $split[1]];
                    });

                return Card::find($parts['card'])
                    ->setLevel($parts['level'] ?? 1)
                    ->setAmount($collection->amount)
                    ->setAlternateArt($parts['alternate'] ?? null);
            })
            ->sortBy('level')
            ->sortBy('name');

        return view('livewire.collection.index', [
            'cards' => $cards,
        ]);
    }
}
