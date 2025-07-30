<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public Collection $decks;

    public array $filters = [
        'sorting' => 'updated_at-desc',
        'format' => null,
        'type' => 'yours',
    ];

    public array $types = [
        'yours' => 'Your decks',
        'public' => 'Public decks',
    ];

    public function mount()
    {
        app('site')->title('Decks');

        // Remove empty decks
        Deck::where('user_id', auth()->id())->get()
            ->filter(fn (Deck $deck) => collect($deck->cards)->isEmpty())
            ->each->delete();
    }

    public function render()
    {
        $this->decks = Deck::query()
            ->when($this->filters['type'] === 'public', fn ($query) => $query->where('user_id', '!=', auth()->id()))
            ->when($this->filters['type'] === 'yours', fn ($query) => $query->where('user_id', auth()->id()))
            ->orderBy(...explode('-', $this->filters['sorting']))
            ->get();

        return view('livewire.decks.index', [
            'shownDecks' => $this->decks->when($this->filters['format'], fn ($decks) =>
                $decks->filter(fn ($deck) => Str::startsWith($deck->format->value, $this->filters['format']))
            ),
        ]);
    }
}
