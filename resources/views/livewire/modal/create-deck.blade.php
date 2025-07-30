<x-modal>
    <x-slot:main>
        <div class="w-full flex flex-col gap-4 px-4 pt-2">
            <x-headers.h2 label="Creating a new deck" />
            <p>What kind of deck would you like to create?</p>
        </div>

        <div class="flex flex-col gap-4 px-4 pb-2">
            @foreach ($formats as $format)
                <div
                    wire:click="createDeck('{{ $format->value }}')"
                    class="
                        flex gap-4 items-center py-4 px-5 bg-surface rounded-lg
                        hover:scale-105 transition-all duration-200 cursor-pointer
                    "
                >
                    <div
                        class="flex justify-center items-center rounded-lg gap-2 aspect-square p-1"
                        style="{{ $format->style() }}"
                    >
                        <x-dynamic-component
                            :component="$format->icon()"
                            class="w-12 h-12"
                        />
                    </div>

                    <div class="flex flex-col">
                        <x-headers.h3 class="gap-2">
                            <span>{{ $format->name() }} deck</span>
                            @if ($format->isRecommended())
                                <span class="text-primary">(Recommended)</span>
                            @endif
                        </x-headers.h3>

                        <p class="opacity-50">
                            {!! $format->description() !!}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-4 pb-2">
            <x-form.button-secondary
                x-on:click="window.closeModal()"
                label="Never mind, I don't want to have fun"
            />
        </div>
    </x-slot>
</x-modal>
