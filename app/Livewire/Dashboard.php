<?php

namespace App\Livewire;

use App\Enums\Format;
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
        // Game::where('user_id_1', 2)->where('user_id_2', 4)->where('finished_at', null)->delete();
        // Game::where('user_id_1', 4)->where('user_id_2', 2)->where('finished_at', null)->delete();

        // $canCreate = auth()->id() && Game::ongoing()->where(function ($query) {
        //     $query->where('user_id_1', auth()->id())->orWhere('user_id_2', auth()->id());
        // })->count() === 0;

        // $canCreateBoss = auth()->id() && BossGame::ongoing()->where('user_id', auth()->id())->count() === 0;

        return view('livewire.dashboard', [

        ]);
    }
}
