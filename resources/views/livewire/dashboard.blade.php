<x-container class="py-12">
    <div class="flex flex-col gap-12">
        <x-headers.h1 label="Welcome to Graahk" />

        <div wire:poll class="flex w-full gap-8">
            <div class="flex w-2/3 flex-col gap-8">
                <div class="flex w-2/3 flex-col gap-8">
                    <x-headers.h2 label="Available tables" />

                    {{-- Active games --}}
                    @if ($games->count())
                        <div class="w-full flex flex-wrap gap-4">
                            @foreach ($games as $game)
                                <div class="w-full flex gap-2 relative bg-surface p-4 rounded-xl">
                                    <x-avatar :user="$game->user1" />
                                    <x-avatar :user="$game->user2" />

                                    @if ($game->user2)
                                        <img
                                            class="absolute top-5 w-14 left-14 z-100"
                                            src="{{ asset('images/swords.png') }}"
                                        />
                                    @endif

                                    <div class="flex flex-col justify-center pl-4 gap-1">
                                        <span class="text-lg font-bold">
                                            {{ $game->name }}
                                        </span>

                                        <div class="flex items-center gap-2">
                                            <x-format-icon :format="$game->format()" size="sm" />
                                            <p class="opacity-50">
                                                {{ $game->format()?->name() }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grow"></div>

                                    @if (auth()->check())
                                        <div class="flex flex-col justify-center">
                                            @if (! $game->user2 && $game->user1->id !== auth()->id())
                                                <x-form.button
                                                    label="Join"
                                                    x-on:click="window.openModal('joinGame', {
                                                        gameId: '{{ $game->id }}',
                                                    })"
                                                />
                                            @endif

                                            @if (in_array(auth()->id(), [$game->user1?->id, $game->user2?->id]))
                                                <x-form.button
                                                    label="Continue"
                                                    x-on:click="window.location.href = '{{ route('game.play', $game) }}'"
                                                />
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($canCreate)
                        <div>
                            <x-form.button
                                label="Create new table"
                                x-on:click="window.openModal('createGame')"
                            />
                        </div>
                    @endif
                </div>

                <div class="flex w-2/3 flex-col gap-8">
                    <x-headers.h2 label="Boss fights" />

                    {{-- Active games --}}
                    @if ($bossGames->count())
                        <div class="w-full flex flex-wrap gap-4">
                            @foreach ($bossGames as $game)
                                <div class="w-full flex gap-2 relative bg-surface p-4 rounded-xl">
                                    <x-avatar :user="$game->user" />
                                    <x-avatar :user="$game->boss" />

                                    <img
                                        class="absolute top-5 w-14 left-14 z-100"
                                        src="{{ asset('images/swords.png') }}"
                                    />

                                    <div class="flex flex-col justify-center pl-4 gap-1">
                                        <span class="text-lg font-bold">
                                            {{ $game->boss->name }}
                                        </span>

                                        <div class="flex items-center gap-2">
                                            <x-format-icon :format="$game->format()" size="sm" />
                                            <p class="opacity-50">{{ $game->format()?->name() }}</p>
                                        </div>
                                    </div>

                                    <div class="grow"></div>

                                    @if (auth()->check())
                                        <div class="flex flex-col justify-center">
                                            @if (auth()->id() === $game->user_id)
                                                <x-form.button
                                                    label="Continue"
                                                    x-on:click="window.location.href = '{{ route('game.play-boss', $game) }}'"
                                                />
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($canCreateBoss)
                        <div>
                            <x-form.button
                                label="Challenge a boss"
                                x-on:click="window.openModal('createBossGame')"
                            />
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex w-1/3 flex-col gap-6">
                <x-headers.h2 label="Top 3 players" />

                <div class="flex flex-col gap-4">
                    @foreach ($users->take(3) as $user)
                        <div class="flex gap-4 items-center">
                            <x-avatar :user="$user" />

                            <div class="flex flex-col">
                                <span class="font-bold">
                                    {{ $user->username }}
                                </span>
                                <span class="opacity-50 text-sm">
                                    @php $played = $user->gamesPlayed(); @endphp
                                    Played {{ $played }} {{ Str::plural('game', $played) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-8" wire:ignore>
            <div class="flex flex-col gap-1">
                <x-headers.h2 label="Latest updated cards" />

                <p class="opacity-50">
                    Here you can find the latest cards that have been updated, this includes new cards and changes to existing cards.
                </p>
            </div>

            <div class="grid grid-cols-6 gap-4">
                @foreach ($latestCards as $card)
                    <div class="w-full p-2 border border-border bg-surface rounded-lg flex flex-col gap-2 items-center">
                        @php $created = $card->created_at->isCurrentWeek() @endphp

                        <x-card :$card />

                        <p class="p-2 text-xs">
                            @if ($created)
                                <span class="flex-inline p-1 px-2 mr-2 bg-red-500 text-white rounded-lg font-bold">NEW</span>
                            @endif

                            <span class="opacity-50">
                                @if ($created) Created @else Updated @endif
                                {{ $card->updated_at->diffForHumans() }}
                            </span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- @if ($weeklyPack)
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <x-headers.h2 :label="'Current ' . $weeklyPack->name" />

                        <p class="opacity-50">
                            {{  \App\Enums\Format::WEEKLY->description() }}<br>
                        </p>
                    </div>
                </div>

                <div class="flex flex-col w-full gap-6">
                    <div class="grid grid-cols-6 gap-4">
                        @foreach ($weeklyPack->list() as $card)
                            <x-card :$card />
                        @endforeach
                    </div>
                </div>
            </div>
        @endif --}}
    </div>
</x-container>
