<?php

use App\Enums\BossType;
use App\Enums\CardType;
use App\Enums\ChallengeType;
use App\Enums\Format;
use App\Enums\Keyword;
use App\Http\Middleware\Authenticate;
use App\Livewire;
use App\Models\Boss;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Draft;
use App\Models\Reward;
use App\Models\WeeklyPack;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('login', Livewire\Auth\Login::class)
    ->name('login.index');

Route::get('/', Livewire\Dashboard::class)
    ->name('dashboard.index');

Route::get('library', Livewire\Library\Index::class)
    ->name('library.index');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('logout', function () {
        auth()->logout();

        return redirect()->to('login');
    })->name('logout.index');

    Route::get('profiles', Livewire\Profiles\Index::class)
        ->name('profile.index');

    Route::get('profile/edit', Livewire\Profiles\Edit::class)
        ->name('profile.edit');

    Route::get('stats', Livewire\Stats\Index::class)
        ->name('stats.index');

    Route::get('decks', Livewire\Decks\Index::class)
        ->name('deck.index');

    Route::get('decks/create/{format}', function (string $format) {
        $format = Format::from($format);

        $deck = Deck::create([
            'name' => "New {$format->name()} Deck",
            'format' => $format->value,
            'weekly_pack_id' => ($format === Format::WEEKLY ? WeeklyPack::latest()->first()->id : null),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('deck.edit', $deck);
    })->name('deck.create');

    Route::get('decks/edit/{deck:id}', Livewire\Decks\Edit::class)
        ->name('deck.edit');

    Route::get('deck-helper', Livewire\DeckHelper\Index::class)
        ->name('deck-helper.index');

    // Route::get('draft', function () {
    //     $draft = Draft::firstOrCreate(
    //         ['user_id' => auth()->id()],
    //         ['cards' => []],
    //     );

    //     return redirect()->to(route('draft.create', $draft));
    // })->name('draft.index');

    // Route::get('draft/{uuid}', Livewire\Drafts\Create::class)
    //     ->name('draft.create');

    Route::get('server', Livewire\Servers\Index::class)
        ->name('server.index');

    Route::get('play/{game:id}', Livewire\Games\Play::class)
        ->name('game.play');

    Route::get('play-boss/{bossGame:id}', Livewire\Games\PlayBoss::class)
        ->name('game.play-boss');

    Route::get('packs', Livewire\Packs\Index::class)
        ->name('packs.index');

    Route::get('collection', Livewire\Collection\Index::class)
        ->name('collection.index');
});

Route::get('challenges', function () {
    dd(ChallengeType::generate());
});

Route::get('countdown', Livewire\Countdown::class);

Route::get('csv', function () {
    $path = storage_path('app/cards.csv');

    $csv = Card::whereIn('type', [CardType::RUSE])
        ->whereHas('sets', fn ($q) => $q->where('beta', false))
        ->get()
        ->map(function (Card $card) {
            // Tab separated string
            return collect($card->toJavaScript())
                ->filter(fn ($v, $key) => in_array($key, ['name', 'cost', 'tribesText', 'text', 'image']))
                ->map(function (mixed $data, string $key) {
                    if ($key === 'image') {
                        @mkdir(storage_path('app/cards'), 0755, true);
                        $filename = md5($data) . '.jpg';
                        $path = storage_path("app/cards/{$filename}");
                        if (!file_exists($path)) {
                            @file_put_contents($path, file_get_contents($data));
                        }

                        return $filename;
                    }

                    return $data;
                })
                ->implode("\t");
        })
        ->join("\n");

    file_put_contents($path, $csv);
});
