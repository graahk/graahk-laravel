<?php

namespace App\Livewire\Modal;

use App\Models\Deck;

class DuplicateDeck extends Modal
{
    public Deck $deck;

    public string $newDeckName;

    public function mount(array $params = [])
    {
        $this->deck = Deck::find($params['id'] ?? null);
        $this->newDeckName = "{$this->deck->name} (Copy)";
    }

    public function render()
    {
        return view('livewire.modal.duplicate-deck');
    }

    public function confirm()
    {
        $newDeck = $this->deck->replicate();
        $newDeck->name = $this->newDeckName;
        $newDeck->save();

        return redirect()->to(route('deck.edit', $newDeck));
    }
}
