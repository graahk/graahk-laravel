@props([
    'label' => '$label',
])

<div {{ $attributes->merge(['class' => '
    w-full py-4 px-6 bg-primary text-white text
    text-2xl font-bold text-center transition-colors duration-200
    hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary-focus focus:ring-offset-2
    massive-button
']) }}>
    {{ $label }}
</div>
