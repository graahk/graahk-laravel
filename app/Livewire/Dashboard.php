<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\User;
use App\Models\WeeklyPack;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        Game::where('user_id_1', 2)->where('user_id_2', 4)->delete();
        Game::where('user_id_1', 4)->where('user_id_2', 2)->delete();

        return view('livewire.dashboard', [
            'weeklyPack' => WeeklyPack::current(),
            'users' => User::latest()->get()->sortByDesc->gamesPlayed(),
        ]);
    }
}
