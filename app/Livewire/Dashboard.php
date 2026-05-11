<?php

namespace App\Livewire;

use App\Models\BossGame;
use App\Models\Card;
use App\Models\Game;
use App\Models\User;
use App\Models\WeeklyPack;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        Game::where('user_id_1', 2)->where('user_id_2', 4)->where('finished_at', null)->delete();
        Game::where('user_id_1', 4)->where('user_id_2', 2)->where('finished_at', null)->delete();

        $canCreate = auth()->id() && Game::ongoing()->where(function ($query) {
            $query->where('user_id_1', auth()->id())->orWhere('user_id_2', auth()->id());
        })->count() === 0;

        $canCreateBoss = auth()->id() && BossGame::ongoing()->where('user_id', auth()->id())->count() === 0;

        $latestCards = Card::orderBy('updated_at', 'desc')
            ->whereHas('sets', fn ($q) => $q->where([
                'beta' => false,
                'artifacts_set' => false,
                'boss_cards' => false,
            ]))
            ->take(18)
            ->get();

        return view('livewire.dashboard', [
            'weeklyPack' => WeeklyPack::current(),
            'users' => User::latest()->get()->sortByDesc->gamesPlayed(),
            'games' => Game::latest()->ongoing()->get(),
            'bossGames' => BossGame::latest()->ongoing()->get(),
            'canCreate' => $canCreate,
            'canCreateBoss' => $canCreateBoss,
            'latestCards' => $latestCards,
        ]);
    }
}
