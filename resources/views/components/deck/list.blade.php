@props([
    'deck',
    'showButtons' => false,
])

<a
    @if (! $showButtons)
        href="{{ $deck->route() }}"
    @endif
    {{ $attributes->except('deck')->merge(['class' =>
        'flex gap-6 w-full rounded-xl bg-surface relative overflow-hidden' . (! $showButtons ? ' hover:scale-105 transition-all duration-200' : '')
    ]) }}
>
    <div
        class="w-[8rem] aspect-square bg-cover bg-center bg-background"
        style="background-image: url('{{ $deck->image()?->path() }}')"
    ></div>

    <div class="flex flex-col justify-center grow gap-2">
        <x-headers.h3 class="text-xl gap-2">
            <x-format-icon :format="$deck->format" />
            {{ $deck->name }}
        </x-headers.h3>

        @if ($deck->weeklyEnded() || ! $deck->isLegal())
            <span class="text-error flex items-center gap-3 pt-2 pl-1">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />

                @if ($deck->weeklyEnded())
                    Weekly pack has ended
                @elseif (! $deck->isLegal())
                    Not enough cards
                @endif
            </span>
        @else
            <p class="text-sm opacity-50">
                {{ $deck->created_at->format('F j, Y') }}
            </p>
        @endif
    </div>

    @if ($showButtons)
        <div class="flex gap-2 px-4 mt-2 items-center">
            <x-form.button x-on:click="window.location.href='{{ $deck->route() }}'">
                <x-heroicon-s-pencil class="w-6 h-6" /> Edit
            </x-form.button>

            <x-form.button x-on:click="window.openModal('duplicate-deck', { id: {{ $deck->id }} })">
                <x-heroicon-s-document-duplicate class="w-6 h-6" /> Duplicate
            </x-form.button>

            <x-form.button
                x-on:click="window.openModal('delete-deck', { id: {{ $deck->id }} })"
                class="bg-red-600 hover:bg-red-800"
            >
                <x-heroicon-s-trash class="w-6 h-6" />
            </x-form.button>
        </div>
    @endif
</a>
