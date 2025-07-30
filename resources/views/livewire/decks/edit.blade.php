<x-container class="flex !p-0">
    <div
        class="w-full h-screen flex gap-4"
        x-data="{
            deckList: @entangle('deckList'),
            mainCardId: @entangle('mainCardId'),
            cardList: @js($cardList),
            addCard (id) {
                const card = this.deckList.find((card) => card.card.id === id)

                if (card) {
                    if (card.amount < 4 || card.card.keywords.includes('innumerable')) {
                        card.amount++
                    }
                } else {
                    this.deckList.push({
                        amount: 1,
                        card: this.cardList.find((card) => card.id === id),
                    })
                }
            },
            removeCard (id) {
                const card = this.deckList.find((card) => card.card.id === id)

                if (card) {
                    if (card.amount > 1) {
                        card.amount--
                    } else {
                        this.deckList = this.deckList.filter((card) => card.card.id !== id)
                    }
                }
            },
            getDeckList () {
                // Sort by cost, then by name
                return this.deckList.sort((a, b) => (a.card.cost - b.card.cost) || a.card.name.localeCompare(b.card.name))
            },
        }"
    >
        {{-- Deck list --}}
        <div
            wire:ignore
            class="w-[30rem] h-screen flex flex-col gap-4 py-8"
        >
            <div class="flex gap-2 w-full items-center">
                <x-format-icon :format="$deck->format" size="lg" />

                <x-form.input
                    class="grow"
                    label="Deck name"
                    wire:model="name"
                />
            </div>

            <div class="
                w-full h-screen flex flex-col gap-2
                p-6 rounded-lg bg-surface overflow-y-auto
            ">
                <p
                    class="text-lg px-2"
                    x-bind:class="{ 'text-red-500': deckList.reduce((a, c) => a + c.amount, 0) !== 30 }"
                    x-show="deckList.length > 0"
                    x-cloak
                >
                    Contains <span x-text="deckList.reduce((a, c) => a + c.amount, 0)"></span>/30 cards
                </p>

                <div class="flex flex-col gap-2">
                    <template x-if="deckList.length === 0">
                        <div class="flex flex-col items-center gap-8 mt-24">
                            <img
                                class="w-2/3 opacity-25"
                                src="{{ asset('images/need_help.png') }}"
                            />

                            @if ($format->isWeekly())
                                <div class="flex flex-col gap-2 items-center">
                                    <h3 class="font-bold text-3xl">EMPTY</h3>
                                    <p>Where are all the dudes?</p>
                                </div>
                            @else
                                <div class="flex flex-col gap-2 items-center">
                                    <h3 class="font-bold text-3xl">HELP</h3>
                                    <p>Don't know how to get started?</p>
                                </div>

                                <x-form.button-secondary
                                    label="Help me, Jack!"
                                    x-on:click="window.location.href = '{{ route('deck-helper.index') }}'"
                                />
                            @endif
                        </div>
                    </template>

                    <template x-for="card in getDeckList()">
                        <div
                            class="has-tooltip flex gap-2 items-center relative cursor-pointer"
                            x-bind:data-card-id="card.card.id"
                        >
                            <div
                                style="background-image: url('{{ asset('images/frames/nameplate.svg') }}')"
                                x-on:click="removeCard(card.card.id)"
                                class="
                                    relative flex gap-1 items-center justify-between w-full
                                    bg-cover bg-no-repeat aspect-[586/107]
                                "
                            >
                                <span
                                    x-text="card.card.cost"
                                    class="w-[16%] text-2xl font-extrabold text-center text-black"
                                ></span>

                                <span
                                    x-text="`(${card.amount}x) ${card.card.name}`"
                                    class="w-[83%] ml-[1%] text-xl font-bold text-black line-clamp-1"
                                ></span>
                            </div>

                            <div
                                x-on:click="mainCardId = card.card.id"
                                class="absolute right-[12%] -top-1 cursor-pointer"
                                x-bind:class="{
                                    '!opacity-100': mainCardId === card.card.id,
                                }"
                            >
                                <x-heroicon-s-bookmark x-show="mainCardId !== card.card.id" class="z-10 top-0 absolute w-8 h-8 text-gray-100 hover:text-primary transition-all" />
                                <x-heroicon-s-bookmark x-show="mainCardId === card.card.id" class="z-10 top-0 absolute w-8 h-8 text-primary" />
                                <x-heroicon-o-bookmark class="pointer-events-none z-10 top-0 absolute w-8 h-8 text-gray-600" />
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex gap-4">
                <x-form.button
                    class="w-full"
                    wire:click="saveDeck"
                    label="Save changes"
                />

                <x-form.button
                    x-on:click="window.openModal('delete-deck', { id: {{ $deck->id }} })"
                    class="bg-red-600 hover:bg-red-800"
                >
                    <x-heroicon-s-trash class="w-6 h-6" />
                </x-form.button>
            </div>
        </div>

        {{-- Cardpool --}}
        <div class="flex flex-col gap-8 h-screen w-full p-8 overflow-y-auto" x-data="{
            open: false,
        }">
            {{-- Filters --}}
            <div class="flex flex-col gap-4">
                <div class="flex gap-4">
                    <div class="w-1/2">
                        <x-form.input
                            label="Search by name or text"
                            wire:model.debounce.live="filters.search"
                        />
                    </div>

                    <div class="w-1/2 flex gap-4">
                        <x-form.select
                            label="Card type"
                            wire:model.live="filters.type"
                            :options="$cardTypes"
                            nullable
                        />

                        <x-form.select
                            label="Sorting"
                            wire:model.live="filters.sort"
                            :options="$sorting"
                        />
                    </div>
                </div>

                <x-headers.h2
                    label="Advanced filters"
                    class="p-2 font-normal cursor-pointer"
                    x-on:click="open = ! open"
                >
                    <x-slot:actions>
                        <x-heroicon-s-chevron-double-down
                            class="w-6 h-6 text-gray-100 hover:text-primary transition-all"
                            x-bind:class="{
                                'transform rotate-180': open,
                            }"
                        />

                        @if ($filters->count() > 1)
                            <x-form.button
                                wire:loading.remove
                                wire:target="resetFilters"
                                wire:click="resetFilters()"
                                class="text-xs"
                            >
                                Reset filters
                            </x-form.button>
                        @endif
                    </x-slot:actions>
                </x-headers.h2>

                <div
                    class="flex flex-col gap-4"
                    x-show="open"
                    x-cloak
                    x-transition
                >
                    <div class="flex gap-4">
                        <x-form.input
                            label="Cost"
                            type="number"
                            wire:model.live="filters.cost"
                        />

                        <x-form.input
                            label="Power"
                            type="number"
                            wire:model.live="filters.power"
                        />
                    </div>

                    <div class="flex gap-4">
                        <x-form.select
                            label="Set"
                            nullable
                            wire:model.live="filters.set"
                            :options="$sets"
                        />

                        <x-form.select
                            label="Keyword"
                            nullable
                            wire:model.live="filters.keyword"
                            :options="$keywords"
                        />

                        <x-form.select
                            label="Tribe"
                            nullable
                            wire:model.live="filters.tribe"
                            :options="$tribes"
                        />
                    </div>
                </div>
            </div>

            <div class="overflow-y-auto">
                <div wire:loading.flex class="w-full">
                    <x-loading />
                </div>

                <div wire:loading.remove class="flex flex-wrap w-full overflow-y-auto">
                    @forelse ($cards as $card)
                        <div
                            wire:key="{{ $card->id }}"
                            x-on:click="addCard({{ $card->id }})"
                            class="w-1/4 p-2 hover:opacity-75 transition-all cursor-pointer"
                            x-bind:class="{
                                '!opacity-25 p-3': (deckList.find((card) => card.card.id === {{ $card->id }})?.amount || 0) >= 4
                                    && ! '{{ json_encode($card->keywords) }}'.includes('innumerable'),
                            }"
                        >
                            <x-card :$card class="relative">
                                {{-- <div class="opacity-0 hover:opacity-100 absolute inset-x-0 bottom-0 flex justify-between items-center p-2">
                                    <x-form.button>
                                        <x-heroicon-o-minus class="w-6 h-6" />
                                    </x-form.button>
                                        
                                    <x-form.button>
                                        <x-heroicon-o-eye class="w-6 h-6" />
                                    </x-form.button>
                                
                                    <x-form.button>
                                        <x-heroicon-o-plus class="w-6 h-6" />
                                    </x-form.button>
                                </div> --}}
                            </x-card>
                        </div>
                    @empty
                        <div
                            wire:key="404-not-found"
                            class="w-full flex flex-col gap-4 items-center justify-center"
                        >
                            <img
                                src="{{ asset('images/404.png') }}"
                                class="opacity-25"
                            />

                            <p class="text-2xl text-white opacity-50">
                                No cards found that match your filters :(
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-container>
