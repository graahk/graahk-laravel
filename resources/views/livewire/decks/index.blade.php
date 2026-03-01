<x-container class="flex gap-12 py-12">
    <div class="flex flex-col gap-8 w-1/4">
        <x-headers.h2>Options</x-headers.h2>

        <x-form.select
            label=""
            wire:model.live="filters.sorting"
            :options="[
                'updated_at-desc' => 'Recently updated/played',
                'created_at-desc' => 'Newest decks',
                'created_at-asc' => 'Oldest decks',
                'name-desc' => 'Name (Z-A)',
                'name-asc' => 'Name (A-Z)',
            ]"
        />

        <div class="flex flex-col gap-4">
            @foreach (\App\Enums\Format::options() as $format)
                @php $count = $decks->where('format', $format)->count() @endphp

                <div
                    x-on:click="$wire.set('filters.format', '{{ $format->value }}')"
                    @class([
                        'flex gap-4 items-center bg-surface rounded-lg p-2',
                        'transition-all duration-200 cursor-pointer',
                        'opacity-50 hover:opacity-100' => ($filters['format'] !== $format->value),
                    ])
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
                        <span class="text-lg font-bold">{{ $format->name() }} decks</span>
                        <span class="text-sm">
                            Includes {{ $count }} {{ Str::plural('deck', $count) }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-2 flex w-full">
            <x-form.button
                x-on:click="window.openModal('create-deck')"
                class="
                    text-xl bg-transparent border-text border-dashed
                    !text-text border-2 opacity-50 w-full
                    hover:bg-primary-hover hover:!text-surface hover:border-transparent
                    hover:opacity-100 transition-all duration-200
                "
            >
                <span class="inline-flex gap-4 items-center py-2">
                    <x-heroicon-o-plus class="w-8 h-8" />
                    <span>Create new deck</span>
                </span>
            </x-form.button>
        </div>
    </div>

    <div class="w-3/4 flex flex-col gap-6">
        <x-headers.h2>Your decks</x-headers.h2>

        @if ($shownDecks->count())
            <div class="w-full grid grid-cols-3 gap-4">
                @foreach ($shownDecks as $deck)
                    <x-deck :$deck :show-buttons="true" :wire:key="$deck->id" />
                @endforeach
            </div>

            {{-- <div class="w-full flex flex-col gap-4">
                @foreach ($shownDecks as $deck)
                    <x-deck.list :$deck :show-buttons="true" :wire:key="$deck->id" />
                @endforeach
            </div> --}}
        @else
            <div class="flex flex-col items-center gap-8 mt-24">
                <img
                    class="w-1/3 opacity-25"
                    src="{{ asset('images/need_help.png') }}"
                />

                <div class="flex flex-col gap-2 items-center">
                    <h3 class="font-bold text-3xl">EMPTY</h3>
                    <p>You don't have any decks yet... :(</p>
                </div>
            </div>
        @endif
    </div>
</x-container>
