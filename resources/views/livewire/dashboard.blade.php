<div class="flex w-full min-h-screen justify-center">
    <div class="p-4 flex flex-col items-center justify-center min-h-screen gap-4 bg-surface border-x border-border">
        <img src="{{ rand(0, 100) === 50 ? asset('images/logo_fun.png') : asset('images/logo.png') }}" class="p-4" />

        <div class="flex flex-col gap-12 my-12 mx-auto w-1/2">
            <x-form.button.massive label="Play" />
            <x-form.button.massive label="Bosses" />
            <x-form.button.massive label="Decks" />
        </div>

        {{-- @foreach ($users as $user)
            <x-content>
                <x-slot:header :background="$user->avatar_url">
                    <h1 class="text-5xl font-bold pb-8 pt-16">
                        {{ $user->username }}
                    </h1>

                    <x-tag.group>
                        <x-tag>
                            <span class="opacity-50">Joined</span>
                            {{ $user->created_at->diffForHumans() }}
                        </x-tag>

                        <x-tag>
                            <span class="opacity-50">Played</span>
                            {{ $user->gamesPlayed() }}
                            <span class="opacity-50">total games</span>
                        </x-tag>

                        <x-tag>
                            <span class="opacity-50">Fought</span>
                            {{ $user->bossesPlayed() }}
                            <span class="opacity-50">bosses</span>
                        </x-tag>
                    </x-tag.group>
                </x-slot:header>

                {{ $user->username }}'s Dashboard
            </x-content>
        @endforeach

        @foreach ($cards as $card)
            <x-content.card-detail :$card />
        @endforeach --}}
    </div>
</div>
