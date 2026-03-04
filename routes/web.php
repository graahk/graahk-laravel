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

    Route::get('server', fn () => redirect()->to(route('dashboard.index')))
        ->name('server.index');

    Route::get('play/{game:id}', Livewire\Games\Play::class)
        ->name('game.play');

    Route::get('play-boss/{bossGame:id}', Livewire\Games\PlayBoss::class)
        ->name('game.play-boss');
});

// Route::get('csv', function () {
//     echo '<pre>';
//     echo Card::noTokens()->get()->map(function (Card $card) {
//         // Tab separated string
//         return collect($card->toJavaScript())
//             ->filter(fn ($v, $key) => in_array($key, ['id', 'name', 'type', 'cost', 'power', 'keywords', 'tribesText', 'text']))
//             ->map(function ($value, $key) {
//                 if ($key === 'keywords') {
//                     return collect($value)->map(fn ($v) => Keyword::from($v)->toText())->implode(', ');
//                 }

//                 if ($key === 'type') {
//                     return CardType::from($value)->getLabel();
//                 }

//                 return $value;
//             })
//             ->implode("\t");
//     })->join("<br>");
// });

// Route::get('thercon-login-redirect', function () {
//     return redirect()->to('https://dev.thercon.be/oauth/authorize?' . http_build_query([
//         'client_id' => '1',
//         'redirect_uri' => 'https://beta.graahk.dev/oauth/callback',
//         'response_type' => 'code',
//     ]));
// });

// Route::get('/oauth/callback', function (\Illuminate\Http\Request $request) {
//     $response = (new GuzzleHttp\Client)->post('https://dev.thercon.be/oauth/token', [
//         'auth' => ['staging', 'staging'],
//         'form_params' => [
//             'grant_type' => 'authorization_code',
//             'client_id' => '1',
//             'client_secret' => 'sc9fjw6FmNIj7hqyKkQDFRZ0i3e1JpcM3ANofX6N',
//             'redirect_uri' => 'https://beta.graahk.dev/oauth/callback',
//             'code' => $request->code,
//         ],
//     ]);

//     $token = json_decode((string) $response->getBody(), true)['access_token'];

//     dump($token);

//     $response = Http::withHeaders(['Accept' => 'application/json'])
//         ->withHeaders(['Authorization' => "Bearer {$token}"])
//         ->get('https://dev.thercon.be/api/user/current');

//     dd($response, $response->json());
// });
// https://beta.graahk.dev/thercon-login-redirect
Route::get('challenges', function () {
    dd(ChallengeType::generate());
});

Route::get('csv', function () {
    $path = storage_path('app/cards.csv');

    $csv = Card::whereIn('type', [CardType::DUDE])
        ->whereHas('sets', fn ($q) => $q->where('beta', false))
        ->get()
        ->map(function (Card $card) {
            // Tab separated string
            return collect($card->toJavaScript())
                ->filter(fn ($v, $key) => in_array($key, ['name', 'cost', 'power', 'tribesText', 'text', 'image']))
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
