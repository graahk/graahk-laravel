@props([
    'card',
    'text' => $card->toText(),
    'clickers' => $card->getCardDetailClickers(),
])

<div {{ $attributes->merge(['class' => '
    flex gap-4 p-4 bg-surface
    border border-border rounded-lg
']) }}>
    <div
        class="rounded-lg bg-size-[200%] bg-center w-1/2 aspect-card flex justify-center items-center p-8 overflow-hidden relative"
        style="background-image: url({{ $card->attachment->path() }})"
    >
        <div class="absolute inset-0 backdrop-blur-xl"></div>
        <x-card :$card class="border border-background" />
    </div>

    <div class="flex flex-col w-1/2 gap-2">
        <div class="flex flex-col gap-2 w-full">
            @foreach (array_filter([
                'Name' => $card->name,
            ]) as $key => $value)
                <x-content.card-detail.tag :$key :$value />
            @endforeach
        </div>

        <div class="flex gap-2 w-full">
            @foreach (array_filter([
                'Tribes' => $card->getTribes()->join(', '),
                'Cost' => $card->cost,
                'Power' => $card->power,
            ]) as $key => $value)
                <x-content.card-detail.tag :$key :$value />
            @endforeach
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-content.card-detail.tag :value="filled($text) ? $text : '-'" />

            @if ($clickers->isNotEmpty())
                <div class="w-full h-[1px] border-t border-border my-2"></div>

                @foreach ($clickers as $clicker)
                    <x-content.card-detail.tag
                        :key="$clicker['name']"
                        :value="filled($clicker['description']) ? $clicker['description'] : '-'"
                        class="gap-2 w-1/2"
                    />
                @endforeach
            @endif
        </div>
    </div>
</div>
