@props([
    'header' => null,
    'headerBackground' => $header->attributes['background'] ?? null,
])

<div {{ $attributes->merge(['class' => '
    flex flex-col gap-2
    border border-border rounded-lg
    bg-background
']) }}>
    @if ($header?->hasActualContent())
        <div {{ $header->attributes->merge(['class' => 'w-full border-b border-border pt-4 pb-12 px-12 relative overflow-hidden']) }}>
            <div class="relative z-10">
                {{ $header }}
            </div>

            @isset($headerBackground)
                <img
                    src="{{ $headerBackground }}"
                    class="absolute top-0 left-0 w-full h-full object-cover opacity-30 z-0 gradient-fade-down rounded-t-lg"
                >
            @endisset
        </div>
    @endif

    <div class="py-8 px-12 z-20">
        {{ $slot }}
    </div>
</div>
