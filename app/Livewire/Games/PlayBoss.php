<?php

namespace App\Livewire\Games;

use App\Models\BossGame;
use Livewire\Component;

class PlayBoss extends Component
{
    public BossGame $bossGame;

    public function render()
    {
        $playerId = (int) auth()->id();
        $opponentId = (int) $this->bossGame->user_2;

        $data = collect($this->bossGame->data)->mapWithKeys(function (mixed $item, string $key) use ($playerId, $opponentId) {
            $key = match ($key) {
                "player_{$playerId}" => 'player',
                "player_{$opponentId}" => 'opponent',
                default => $key,
            };

            return [$key => $item];
        });

        return optional(view('livewire.games.play', [
            'state' => $data->toArray(),
            'game' => $this->bossGame,
        ]))->layout('components.layouts.game');
    }
}
