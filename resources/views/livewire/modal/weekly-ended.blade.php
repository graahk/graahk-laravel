<x-modal>
    <x-slot:main>
        <x-headers.h2 label="This weekly has ended" />

        <p class="opacity-75">
            This weekly has ended, so you will not be able to play it anymore.<br>
            You can convert it to a Standard or Chaos deck, or leave it as it is.
        </p>

        <div class="flex gap-4">
            <x-form.button-secondary
                x-on:click="window.closeModal()"
                label="Leave it as it is"
            />

            <x-form.button
                wire:click="convert('standard')"
                label="Convert to a Standard deck"
            />

            <x-form.button
                wire:click="convert('chaos')"
                label="Convert to a Chaos deck"
            />
        </div>
    </x-slot>
</x-modal>
