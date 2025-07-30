<a
    href="{{ $deck->route() }}"
    {{ $attributes->except('deck')->merge(['class' => '
        flex flex-col gap-4 w-full rounded-xl p-4 bg-surface relative
        hover:scale-105 transition-all duration-200
    ']) }}
>
    @if ($deck->weeklyEnded())
        <div class="
            absolute top-6 left-6 text-error flex items-center gap-3
            py-2 pr-4 pl-3 rounded-lg bg-opacity-75 bg-black
        ">
            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
            Weekly pack has ended
        </div>
    @elseif (! $deck->isLegal())
        <div class="
            absolute top-6 left-6 text-error flex items-center gap-3
            py-2 pr-4 pl-3 rounded-lg bg-opacity-75 bg-black
        ">
            <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
            Not enough cards
        </div>
    @endif

    <div
        class="w-full rounded-lg aspect-[2/1] bg-cover bg-[center_top_-5rem] bg-background"
        style="background-image: url('{{ $deck->image()?->path() }}')"
    ></div>

    <div class="flex flex-col grow items-center gap-1">
        <x-headers.h3 class="text-xl gap-2">
            <x-format-icon :format="$deck->format" />

            {{ $deck->name }}
        </x-headers.h3>

        <p class="opacity-50 text-sm">
            {{ $deck->mainCard?->name ?? $deck->created_at?->isoFormat('MMM Do, YYYY') }}
        </p>
    </div>
</a>
