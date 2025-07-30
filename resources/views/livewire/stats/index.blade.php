<x-container class="flex flex-col gap-12 py-8">
    <div class="grid grid-cols-6 gap-4">
        @foreach ($popularCards as $card)
            <div class="flex flex-col gap-4">
                <x-card :$card wire:click="checkCommonCombos({{ $card->id }})" />
                <p>Used in {{ $popularCardCounts[$card->id] ?? 0 }}% of decks</p>
            </div>
        @endforeach
    </div>
</x-container>
