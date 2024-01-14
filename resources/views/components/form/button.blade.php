@props([
    'text' => $slot,
])

<button
    wire:loading.class="opacity-50 cursor-not-allowed"
    {{ $attributes->merge(['class' => '
        w-full block bg-primary rounded px-4 py-2
        hover:bg-primary-hover cursor-pointer
    ']) }}
>
    {{ $text }}
</button>