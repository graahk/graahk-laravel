@props([
    'route' => '#',
    'icon' => '',
    'activeIcon' => Str::replace('-o-', '-s-', $icon),
    'active' => isset($route) && ($route === request()->url()),
    'label',
    'subtitle' => null,
])

<a
    href="{{ $route }}"
    {{ $attributes->only('x-on:click.prevent') }}
    @class([
        'text-primary' => $active,
        'flex p-4 text-title hover:text-primary w-full select-none transition-colors',
        'flex-row justify-start relative gap-4 items-center',
    ])
>
    <div class="relative w-8 h-6">
        <x-dynamic-component
            :component="$active ? $activeIcon : $icon"
            class="w-8 h-6"
        />
    </div>

    <div class="flex flex-col">
        <span>{{ $label }}</span>

        @if ($subtitle)
            <span class="opacity-50 text-xs">
                {{ $subtitle }}
            </span>
        @endif
    </div>
</a>
