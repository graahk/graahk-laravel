<?php

namespace App\Livewire\Modal;

use App\Enums\Format;
use App\Models\Deck;

class WeeklyEnded extends Modal
{
    public Deck $deck;

    public function mount(array $params = [])
    {
        $this->deck = Deck::find($params['deckId'] ?? null);
    }

    public function render()
    {
        return view('livewire.modal.weekly-ended');
    }

    public function convert(string $format)
    {
        $this->deck->format = Format::tryFrom($format) ?? Format::Chaos;
        $this->deck->save();

        return $this->redirect($this->deck->route());
    }
}
