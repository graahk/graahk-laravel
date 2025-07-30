<?php

namespace App\Http\Controllers\Api;

use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function update(Game $game, Request $request)
    {
        $data = $game->data;

        foreach ($request->get('gameState') as $key => $value) {
            $data[$key] = $value;
        }

        $game->update(['data' => $data]);
    }

    public function finish(Game $game, int $winner)
    {
        if ($game->finished_at) {
            return;
        }

        $game->update(['finished_at' => now()]);

        $gains = collect($game->data['decks'])->mapWithKeys(function (int $deck, int $owner) use ($game, $winner) {
            $deck = Deck::find($deck);
            $card = Card::find($deck->idList()->shuffle()->first());

            $experience = DB::table('experience')
                ->where('user_id', $owner)
                ->where('card_id', $card->id)
                ->where('experience', '<', 3000)
                ->first();

            $gained = ($winner === $owner ? 500 : 250);

            // Don't give exp to test player fights
            if (collect($game->data['decks'])->keys()->contains(2)) {
                $gained = 0;
            }

            DB::table('experience')->updateOrInsert([
                'user_id' => $owner,
                'card_id' => $card->id,
            ], [
                'experience' => ($experience?->experience ?? 0) + $gained,
            ]);

            Cache::forget("cards-level-{$card->id}-{$owner}");

            return [
                $owner => [
                    'card' => $card->toJavaScript(User::find($owner)),
                    'gained' => $gained,
                    'started_at' => $experience?->experience ?? 0,
                ],
            ];
        });

        EmitEvent::dispatch($game, 'exp_gain', $gains->toArray());
    }
}
