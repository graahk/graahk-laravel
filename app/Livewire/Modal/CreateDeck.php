<?php

namespace App\Livewire\Modal;

use App\Enums\Format;
use App\Models\Deck;
use App\Models\WeeklyPack;

class CreateDeck extends Modal
{
    public function render()
    {
        return view('livewire.modal.create-deck', [
            'formats' => collect([Format::STANDARD, Format::WEEKLY, Format::CHAOS]),
        ]);
    }

    public function createDeck(string $format)
    {
        $format = Format::from($format);

        $deck = Deck::create([
            'name' => "New {$format->name()} Deck",
            'format' => $format->value,
            'weekly_pack_id' => ($format === Format::WEEKLY ? WeeklyPack::latest()->first()->id : null),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('deck.edit', $deck);
    }
}
