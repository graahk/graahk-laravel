<x-modal>
    <x-slot:main>
        <x-headers.h2 label="Create table" />

        <div class="flex flex-col w-full gap-4">
            <x-form.input
                wire:model="fields.name"
                label="Table name"
                nullable
            />

            <div
                class="grid grid-cols-3 gap-4 overflow-y-auto max-h-[55vh] p-2"
                x-data="{
                    deck: @entangle('fields.deck_id'),
                }"
            >
                @foreach ($decks as $deck)
                    <x-deck
                        :$deck
                        x-on:click.prevent="deck = {{ $deck->id }}"
                        x-bind:class="{
                            'opacity-25': deck !== {{ $deck->id }},
                            'opacity-100': deck === {{ $deck->id }},
                        }"
                    />
                @endforeach
            </div>
        </div>

        <div class="w-full flex gap-4">
            <x-form.button-secondary
                x-on:click="window.closeModal()"
                label="Never mind, I'm scared"
            />
            
            <x-form.button
                wire:click="create"
                label="Create new table"
            />
        </div>
    </x-slot>
</x-modal>
