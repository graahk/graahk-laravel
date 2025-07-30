<?php

namespace App\Livewire\DeckHelper;

use App\Enums\Format;
use App\Models\Deck;
use App\Models\DeckHelper;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $helpers;
    public null | Collection $previousHelpers = null;

    public null | DeckHelper $chosenHelper = null;

    public array $fields = [];

    public int $step = 0;

    public function mount()
    {
        $this->newHelpers();

        if (session()->has('deck-helper')) {
            $this->step = session('deck-helper')['step'];
            $this->fields = session('deck-helper')['fields'];

            if ($this->fields['helper'] ?? null) {
                $this->chosenHelper = DeckHelper::find($this->fields['helper']);
            }
        }

        if ($this->step === 4) {
            return $this->finish();
        }
    }

    public function render()
    {
        return view('livewire.deck-helper.index');
    }

    public function finish()
    {
        session()->forget('deck-helper');

        $deck = Deck::create([
            'name' => $this->chosenHelper->name,
            'user_id' => auth()->id(),
            'main_card_id' => $this->chosenHelper->main_card_id,
            'format' => Format::STANDARD,
            'cards' => $this->chosenHelper->cards
                ->push($this->chosenHelper->mainCard)
                ->mapWithKeys(fn ($card) => [$card->id => 4])
                ->toArray(),
        ]);

        return redirect()->route('deck.edit', $deck);
    }

    public function next()
    {
        $this->step = min(4, $this->step + 1);

        session(['deck-helper' => [
            'step' => $this->step,
            'fields' => $this->fields,
        ]]);

        if ($this->step === 4) {
            return $this->finish();
        }
    }

    public function back()
    {
        $this->step = max(0, $this->step - 1);
        session(['deck-helper' => [
            'step' => $this->step,
            'fields' => $this->fields,
        ]]);
    }

    public function newHelpers()
    {
        $this->helpers = DeckHelper::inRandomOrder()
            ->whereNotIn('id', $this->previousHelpers ?? [])
            ->limit(3)
            ->get();

        $this->previousHelpers = $this->helpers->pluck('id');
    }

    public function selectHelper(int $id)
    {
        $this->fields['helper'] = $id;
        $this->chosenHelper = DeckHelper::find($id);
        $this->next();
    }
}
