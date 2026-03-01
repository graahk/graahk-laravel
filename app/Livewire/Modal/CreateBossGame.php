<?php

namespace App\Livewire\Modal;

use App\Enums\Format;
use App\Models\Boss;
use App\Models\BossGame;
use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateBossGame extends Modal
{
    public int $step = 1;

    public Collection $bosses;

    public int $boss;

    public Collection $decks;

    public array $fields = [];

    public function mount()
    {
        $this->bosses = Boss::whereHas('artifacts')->get();

        $this->decks = auth()->user()->decks
            ->filter(fn (Deck $deck) => $deck->isLegal() && ! $deck->weeklyEnded())
            ->sortByDesc('updated_at');

        $this->fields['deck_id'] = $this->decks->first()->id ?? null;
    }

    public function render()
    {
        return view('livewire.modal.create-boss-game');
    }

    public function create()
    {
        $this->validate(
            ['fields.deck_id' => 'required|exists:decks,id'],
            ['fields.deck_id.required' => 'Please select a deck.']
        );

        $player = auth()->id();
        $deck = Deck::find($this->fields['deck_id']);
        $boss = Boss::find($this->boss);

        $game = BossGame::create([
            'user_id' => $player,
            'boss_id' => $boss->id,
            'data' => [
                'decks' => [
                    $player => $deck->id,
                    ($boss->id + 10000) => Deck::where('name', $boss->name)
                        ->where('format', Format::BOSS)
                        ->pluck('id')
                        ->first(),
                ],
            ],
        ]);

        $gameData = $game->data;
        $gameData['decks'][$player] = $this->fields['deck_id'];
        $gameData['current_player'] = $player;

        $boss->artifacts->each(function (Card $artifact) use (&$gameData, $boss) {
            $artifactData = $artifact->toJavaScript();
            $artifactData['owner'] = ($boss->id + 10000);
            $gameData['boss_artifacts'][] = $artifactData;
        });

        foreach ($gameData['decks'] as $playerId => $deck) {
            $user = User::find($playerId) ?? $boss;
            $deck = Deck::find($deck);

            $deck->touch();

            $isBoss = ($playerId > 10000);

            $gameData["player_{$playerId}"] = [
                'id' => $playerId,
                'name' => $user->username ?? $user->name ?? $boss->name,
                'avatar' => $user->avatar_url ?? $boss->attachment?->path(),
                'uuid' => (string) Str::uuid(),
                'board' => [],
                'hand' => [],
                'graveyard' => [],
                'energy' => 0,
                'power' => ($isBoss ? $boss->power : 2000),
                'originalPower' => ($isBoss ? $boss->power : 2000),
                'deck' => $deck
                    ->idList()
                    ->shuffle()
                    ->map(fn (int $id) => Card::find($id)->toJavaScript($isBoss ? null : $user))
                    ->map(function (array $card) use ($playerId) {
                        $card['owner'] = $playerId;

                        return $card;
                    })
                    ->toArray(),
            ];
        }

        $game->update(['data' => $gameData]);

        return redirect()->to($game->route());
    }
}
