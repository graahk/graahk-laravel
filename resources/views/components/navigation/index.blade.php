<div class="w-[300px] h-screen bg-surface px-4 py-8 flex flex-col gap-4">
    <a href="{{ route('dashboard.index') }}">
        <img src="{{ rand(0, 100) === 50 ? asset('images/logo_fun.png') : asset('images/logo.png') }}" class="p-4" />
    </a>

    @php
        $count = \App\Models\Game::ongoing()->count();
    @endphp

    <x-navigation.item
        route="{{ route('dashboard.index') }}"
        :active="request()->routeIs('dashboard.index')"
        icon="heroicon-o-home"
        label="Dashboard"
        :subtitle="auth()->check() ? ($count . ' ' . Str::plural('table', $count) . ' ongoing') : null
        "
    />

    @if (auth()->check())
        <x-navigation.item
            route="{{ route('deck.index') }}"
            :active="request()->routeIs('deck.index')"
            icon="heroicon-o-square-3-stack-3d"
            label="Decks"
        />

        <x-navigation.item
            route="{{ route('packs.index') }}"
            :active="request()->routeIs('packs.index')"
            icon="heroicon-o-arrow-up-on-square-stack"
            label="Booster Packs"
        />
    @endif

    <x-navigation.item
        route="{{ route('library.index') }}"
        :active="request()->routeIs('library.index')"
        icon="heroicon-o-book-open"
        label="Library"
    />

    @if (! auth()->check())
        <x-navigation.item
            route="{{ route('login.index') }}"
            :active="request()->routeIs('login.index')"
            icon="heroicon-o-arrow-left-end-on-rectangle"
            label="Log in"
        />
    @endif

    @if (auth()->check())
        <div class="grow"></div>

        <x-navigation.item
            route="{{ route('profile.edit') }}"
            :active="request()->routeIs('profile.edit')"
            icon="heroicon-o-user-circle"
            label="Profile"
        />
    @endif
</div>
