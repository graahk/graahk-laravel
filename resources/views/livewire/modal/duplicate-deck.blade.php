<x-modal>
    <x-slot:main>
        <div class="w-full flex flex-col gap-6 p-2">
            <x-headers.h2 label="Are you sure?" />

            <p class="opacity-75">
                You are about to duplicate the deck "<span class="font-bold">{{ $deck->name }}</span>".<br>
                What would you like to name the new deck?
            </p>

            <x-form.input
                wire:model="newDeckName"
                label="New Deck Name"
                required
            />
        </div>

        <div class="w-full flex gap-4">
            <x-form.button-secondary
                x-on:click="window.closeModal()"
                label="Hmm maybe not"
            />

            <x-form.button
                wire:click="confirm"
                label="Duplicate that bad boy"
            />
        </div>
    </x-slot>
</x-modal>
