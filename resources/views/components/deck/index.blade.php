@props([
    'deck',
    'showButtons' => false,
])

<a
    @if (! $showButtons)
        href="{{ $deck->route() }}"
    @endif
    {{ $attributes->except('deck')->merge(['class' =>
        'flex flex-col gap-4 w-full rounded-xl py-4 bg-surface relative'
            . (! $showButtons ? ' hover:scale-105 transition-all duration-200' : '')
    ]) }}
>
    @if ($deck->weeklyEnded() || ! $deck->isLegal())
        <div class="pointer-events-none absolute inset-0 items-center justify-center flex">
            <div class="
                text-error flex items-center gap-3
                py-2 pr-4 pl-3 rounded-lg bg-opacity-75 bg-black
            ">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />

                @if ($deck->weeklyEnded())
                    Weekly pack has ended
                @elseif (! $deck->isLegal())
                    Not enough cards
                @endif
            </div>
        </div>
    @endif

    <div class="flex flex-col grow items-center gap-1">
        <x-headers.h3 class="text-xl gap-2">
            <x-format-icon :format="$deck->format" />
            {{ $deck->name }}
        </x-headers.h3>
    </div>

    <div
        class="w-full aspect-[2/1] bg-cover bg-[center_top_-5rem] bg-background"
        style="background-image: url('{{ $deck->image()?->path() }}')"
    ></div>

    @if ($showButtons)
        <div class="flex gap-2 px-4 mt-2 justify-center">
            <x-form.button
                x-on:click="window.openModal('delete-deck', { id: {{ $deck->id }} })"
                class="bg-red-600 hover:bg-red-800"
            >
                <x-heroicon-s-trash class="w-6 h-6" />
            </x-form.button>

            <div class="grow"></div>

            <x-form.button x-on:click="window.openModal('duplicate-deck', { id: {{ $deck->id }} })">
                <x-heroicon-s-document-duplicate class="w-6 h-6" />
            </x-form.button>

            <x-form.button x-on:click="window.location.href='{{ $deck->route() }}'">
                <x-heroicon-s-pencil class="w-6 h-6" />
            </x-form.button>
        </div>
    @endif
</a>
