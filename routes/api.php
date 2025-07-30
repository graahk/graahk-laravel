<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::put('games/{game:id}', [Api\GameController::class, 'update']);
Route::put('games/{game:id}/finish/{winner}', [Api\GameController::class, 'finish']);

Route::post('games/{game:id}/event', Api\EventController::class);

Route::get('cards/tooltip/{card:id}', [Api\CardController::class, 'tooltip'])
    ->withoutScopedBindings();

Route::get('cards/{card:id}/{user:id?}', [Api\CardController::class, 'show'])
    ->withoutScopedBindings();
