<x-container class="py-12">
    <div class="flex flex-col gap-12">
        <x-headers.h1 label="Dashboard of random gobbledegook" />

        <div class="flex flex-col gap-6">
            <x-headers.h2 label="Players" />

            <div class="grid grid-cols-5 gap-4">
                @foreach ($users as $user)
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

        @if ($weeklyPack)
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <x-headers.h2 :label="'Current ' . $weeklyPack->name" />

                        <p class="opacity-50">
                            {{  \App\Enums\Format::WEEKLY->description() }}<br>
                            Each weekly will have a different randomly picked Artifact that is active during every game.<br>
                            This weeks artifact is <strong>{{ $weeklyPack->artifact->name }}</strong>:
                        </p>
                    </div>

                    <div class="grid grid-cols-6 gap-4">
                        <x-card :card="$weeklyPack->artifact" />
                    </div>
                </div>

                <div class="flex flex-col w-full gap-6">
                    <p class="opacity-50">
                        The following cards are available in the weekly pack. You can use these cards in your decks, but only during the weekly format.<br>
                    </p>

                    <div class="grid grid-cols-6 gap-4">
                        @foreach ($weeklyPack->list() as $card)
                            <x-card :$card />
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-container>
