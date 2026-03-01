<div>
    @if (! $game->user2)
        <div class="flex w-full h-screen items-center justify-center" wire:poll>
            <div class="flex flex-col gap-8 items-center">
                <x-headers.h1 label="Waiting on another player..." />
                <x-loading />

                <x-form.button
                    class="mt-12"
                    label="Cancel matchmaking"
                    wire:click="cancelMatchmaking()"
                />
            </div>
        </div>
    @else
        <div id="app">
            <play
                :starting-game-state="@js(json_encode($state))"
                :player-id="{{ auth()->user()->id }}"
                :game-id="'{{ $game->id }}'"
                :is-boss-fight="{{ $game->boss ? 'true' : 'false' }}"
            />
        </div>
    @endif
</div>
