@props([
    'key' => null,
    'value',
])

<div {{ $attributes->merge(['class' => 'flex flex-col bg-background rounded-lg px-4 py-3 w-full' ]) }}>
    @if ($key)
        <span class="opacity-50 text-xs">{{ $key }}</span>
    @endif

    <span>{!! $value !!}</span>
</div>
