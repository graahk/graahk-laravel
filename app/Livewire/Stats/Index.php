<?php

namespace App\Livewire\Stats;

use App\Enums\CardType;
use App\Models\Card;
use App\Models\Deck;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $deckCount = Deck::count();
        $popularCards = $this->countCards(Deck::get());

        return view('livewire.stats.index', [
            'popularCardCounts' => $popularCards->mapWithKeys(fn ($count, $cardId) => [$cardId => ceil(($count * 100) / $deckCount)]),
            'popularCards' => Card::withoutGlobalScopes()
                ->with('sets')
                ->whereHas('sets', fn ($q) => $q->where('boss_cards', false))
                ->whereHas('sets', fn ($q) => $q->where('artifacts_set', false))
                ->where('type', '!=', CardType::TOKEN)
                ->get()
                ->sortByDesc(fn ($card) => $popularCards[$card->id] ?? 0),
        ]);
    }

    public function checkCommonCombos(Card $card)
    {
        $decks = Deck::query()
            ->where('cards', 'LIKE', "%\"{$card->id}\"%")
            ->get();

        $similarCards = $this->countCards($decks)
            ->filter(fn ($_, $cardId) => $cardId !== $card->id)
            ->sortDesc()
            ->take(10);

        dd(
            Card::withoutGlobalScopes()
                ->find($similarCards->keys())
                ->sortByDesc(fn ($card) => $similarCards[$card->id] ?? 0)
                ->pluck('name')
                ->toArray()
        );
    }

    private function countCards(Collection $decks): Collection
    {
        return collect($decks->pluck('cards')->reduce(function ($carry, $cards) {
            foreach (array_keys($cards ?? []) as $card) {
                $carry[$card] ??= 0;
                $carry[$card]++;
            }

            return $carry;
        }, []));
    }
}
