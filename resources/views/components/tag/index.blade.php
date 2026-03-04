<div {{ $attributes->merge(['class' => 'flex bg-surface px-4 py-2 rounded-lg']) }}>
    @isset($icon)
        <div class="w-6 h-6">
            {{ $icon }}
        </div>
    @endisset

    <p>
        {{ $slot }}
    </p>
</div>
