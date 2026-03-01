<?php

namespace App\Livewire\Games;

use App\Models\Game;
use Livewire\Component;

class Play extends Component
{
    public Game $game;

    public function render()
    {
        if (! $this->game->user2) {
            return optional(view('livewire.games.play', [
                'state' => [],
            ]))->layout('components.layouts.game');
        }

        $playerId = (int) auth()->id();
        $opponentId = (int) $this->game->opponentId($playerId);

        $data = collect($this->game->data)->mapWithKeys(function (mixed $item, string $key) use ($playerId, $opponentId) {
            $key = match ($key) {
                "player_{$playerId}" => 'player',
                "player_{$opponentId}" => 'opponent',
                default => $key,
            };

            return [$key => $item];
        });

        return optional(view('livewire.games.play', [
            'state' => $data->toArray(),
        ]))->layout('components.layouts.game');
    }

    public function cancelMatchmaking()
    {
        $this->game->delete();

        return redirect()->route('dashboard.index');
    }
}
