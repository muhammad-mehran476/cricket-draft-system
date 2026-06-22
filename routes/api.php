<?php

use App\Http\Controllers\Admin\DraftController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Used for polling fallback
| when Pusher/WebSockets are unavailable, and for general JSON queries.
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Draft live polling fallback (in case Pusher is not configured)
Route::middleware(['auth'])->prefix('draft')->group(function () {
    Route::get('/{draft}/poll', [DraftController::class, 'liveState']);
});
