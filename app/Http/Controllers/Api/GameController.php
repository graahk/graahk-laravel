<?php

namespace App\Http\Controllers\Api;

use App\Events\EmitEvent;
use App\Http\Controllers\Controller;
use App\Models\BossGame;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Game;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function update(string $game, Request $request)
    {
        $game = Game::find($game) ?? BossGame::find($game) ?? abort(404);

        $data = $game->data;

        foreach ($request->get('gameState') as $key => $value) {
            $data[$key] = $value;
        }

        $game->update(['data' => $data]);
    }

    public function finish(string $game, int $winner)
    {
        $game = Game::find($game) ?? BossGame::find($game) ?? abort(404);

        if ($game->finished_at) {
            return;
        }

        $game->update(['finished_at' => now()]);

        if ($game::class === BossGame::class) {
            return $this->handleBossGame($game, $winner);
        }

        $gains = collect($game->data['decks'])->mapWithKeys(function (int $deck, int $owner) use ($game, $winner) {
            $deck = Deck::find($deck);
            $card = $deck->mainCard ?? Card::find($deck->idList()->shuffle()->first());

            $experience = DB::table('experience')
                ->where('user_id', $owner)
                ->where('card_id', $card->id)
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

    public function handleBossGame(BossGame $game, int $winner)
    {
        $playerWon = $winner < 10000;

        $boss = $game->boss;
        $boss->increment(! $playerWon ? 'victories' : 'defeats');

        if (! $playerWon) {
            // Boss won, no rewards for players
            return [];
        }

        // Rewards
        $rewards = Reward::where('boss_type', $game->boss->boss_type)->get()
            ->map(fn (Reward $reward) => $reward->reward_type::find($reward->reward_id))
            ->each(function (Model $reward) {
                DB::table('user_unlocks')->updateOrInsert([
                    'unlock_type' => $reward->getMorphClass(),
                    'unlock_id' => $reward->id,
                    'user_id' => auth()->id(),
                ], [
                    'enabled' => 1,
                ]);
            });

        EmitEvent::dispatch($game, 'exp_gain', $rewards->map(fn (Model $reward) => [
            'card' => $reward->card->toJavaScript(User::find($winner)),
            'text' => $reward->rewardText(),
        ])->toArray());
    }
}
