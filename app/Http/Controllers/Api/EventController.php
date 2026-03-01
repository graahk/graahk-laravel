<?php

namespace App\Http\Controllers\Api;

use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\BossGame;
use App\Models\Game;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __invoke(string $game, Request $request)
    {
        $game = Game::find($game) ?? BossGame::find($game) ?? abort(404);

        EmitEvent::dispatch(
            $game,
            $request->event,
            $request->data ?? [],
        );
    }
}
