<?php

namespace App\Console\Commands;

use App\Enums\CardType;
use App\Enums\Format;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Set;
use App\Models\WeeklyPack;
use Illuminate\Console\Command;

class GenerateWeeklyPack extends Command
{
    protected $signature = 'generate:weekly-pack {--force}';

    public function handle()
    {
        // Check if the weekly pack already exists
        if (! $this->option('force') && WeeklyPack::where('created_at', '>=', now()->startOfWeek())->exists()) {
            $this->info('Weekly pack already exists.');
            return;
        }

        // Generate a new weekly pack
        $cards = Set::where('beta', false)
            ->where('artifacts_set', false)
            ->get()
            ->pluck('cards')
            ->flatten()
            ->reject(fn ($card) => $card->type === CardType::TOKEN)
            ->unique()
            ->values();

        $artifact = Card::where('type', CardType::ARTIFACT)
            ->where('id', '!=', WeeklyPack::latest()->first()->artifact_id ?? null)
            ->inRandomOrder()
            ->first();

        // Get X cards per cost ,to make sure we balance correctly
        $cards = $cards->groupBy('cost');

        $cardList = collect();
        for ($i = 1; $i <= 6; $i++) {
            $cardList->push($cards->get($i)->shuffle()->take(match ($i) {
                1 => rand(4, 6),
                2 => rand(8, 10),
                3 => rand(8, 10),
                4 => rand(8, 10),
                5 => rand(2, 4),
                6 => rand(1, 2),
            }));
        }

        // Get 5 random cards from cost 7+
        $cardList->push(
            $cards->skip(6)->flatten()->shuffle()->take(rand(1, 3))
        );

        // Fill up the rest up to 50 cards in total
        $cardList->push(
            $cards
                ->flatten()
                ->shuffle()
                ->reject(fn ($card) => $cardList->flatten()->pluck('id')->contains($card->id))
                ->take(50 - $cardList->flatten()->count())
        );


        $cardList = $cardList->flatten()->pluck('id')->toArray();

        WeeklyPack::create([
            'name' => 'Weekly Pack - ' . now()->isoFormat('MMMM Do'),
            'attachment_id' => Card::whereIn('id', $cardList)->get()->random()->attachment_id,
            'artifact_id' => $artifact->id,
            'cards' => $cardList,
        ]);

        // Set all old weekly decks to chaos
        Deck::where('format', Format::WEEKLY)->update(['format' => Format::WEEKLY_ENDED]);
    }
}
