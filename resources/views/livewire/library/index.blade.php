<x-container class="py-12">
    <div class="flex flex-col gap-12">
        <x-headers.h1 label="Graahk's Library" />

        <div class="grid grid-cols-6 gap-4">
            @foreach ($sets as $set)
                <div
                    x-on:click="$wire.set('setId', {{ $set->id }})"
                    style="background-image: url('{{ $set->attachment->path() }}')"
                    @class([
                        'relative overflow-hidden aspect-[2.5/3.5] bg-cover bg-center border-2 border-border',
                        'rounded-xl hover:scale-105 transition-all duration-200 cursor-pointer',
                        'border-primary' => $set->id === $setId,
                    ])
                >
                    @if ($set->beta)
                        <div class="
                            absolute top-0 right-0 bg-red-500 text-white text-xs font-bold px-4 p-2 rounded-bl-xl
                            flex gap-2 items-center justify-center
                        ">
                            <x-rpg-crown-of-thorns class="w-4 h-4" />
                            BETA SET
                        </div>
                    @endif

                    <div @class([
                        'absolute bottom-0 left-0 right-0 font-bold p-2 text-center',
                        'bg-surface' => $set->id !== $setId,
                        'bg-primary color-black' => $set->id === $setId,
                    ])>
                        {{ $set->name }}
                    </div>
                </div>
            @endforeach
        </div>

        @if ($highlighted)
            <div
                class="z-10 absolute inset-0 bg-black bg-opacity-75 flex justify-center cursor-pointer"
                x-on:click="$wire.setHighlighted(null)"
            >
                <div class="aspect-[2.5/3.5] p-8">
                    <x-card
                        :card="$highlighted"
                        full-sized
                    />
                </div>
            </div>
        @endif

        <div wire:loading wire:target="setId" class="opacity-50">
            <x-loading />
        </div>

        <div wire:loading.remove wire:target="setId">
            @if ($cards->isNotEmpty())
                <div class="grid grid-cols-6 gap-4 cursor-pointer">
                    @foreach ($cards as $card)
                        <x-card :$card x-on:click="$wire.setHighlighted({{ $card->id }})" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-container>
