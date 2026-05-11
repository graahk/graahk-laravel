<x-container class="py-12">
    <div class="grid grid-cols-4 gap-4 p-4">
        @foreach ($cards as $card)
            <div>
                <x-card :card="$card" />

                Amount: {{ $card->amount }}
            </div>
        @endforeach
    </div>
</x-container>
