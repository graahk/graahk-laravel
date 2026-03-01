<x-modal>
    <x-slot:main>
        <x-headers.h2 label="Fight the legendary bosses" />

        @if ($step === 1)
            <div class="w-full flex flex-col gap-4">
                @foreach ($bosses as $boss)
                    <div class="flex gap-4 p-6 rounded-lg border border-border bg-surface">
                        <div
                            class="flex gap-2 w-1/3 aspect-[2.5/3.5] bg-cover bg-center bg-no-repeat relative justify-center rounded-lg"
                            style="background-image: url('{{ $boss->attachment->path() }}')"
                        >
                            <div class="
                                absolute -bottom-[2rem] pb-1 pt-2 px-6 text-5xl font-bold bg-surface
                                border-[2px] border-black rounded-2xl overflow-hidden
                            ">
                                <span class="z-10 relative">
                                    {{ $boss->power }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 w-2/3">
                            <x-headers.h2 :label="$boss->name" class="px-4" />

                            <p class="px-4">
                                {{ $boss->victories }} {{ $boss->boss_type->victoryLabel() }} - 
                                {{ $boss->defeats }} {{ $boss->boss_type->defeatLabel() }}
                            </p>

                            <div class="grid grid-cols-3 w-full gap-2 p-4">
                                @foreach ($boss->artifacts as $artifact)
                                    <x-card :card="$artifact" />
                                @endforeach
                            </div>

                            <div class="px-4">
                                <x-form.button
                                    x-on:click.prevent="$wire.set('boss', {{ $boss->id }}) && $wire.set('step', 2)"
                                    label="I choose you!"
                                />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="w-full flex gap-4">
                <x-form.button-secondary
                    x-on:click="window.closeModal()"
                    label="Ehh maybe not"
                />
            </div>
        @else
            <div class="flex flex-col w-full gap-4">
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
                    label="Fighting time!"
                />
            </div>
        @endif
    </x-slot>
</x-modal>
