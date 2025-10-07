<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TftApiController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\BuildController;
use App\Http\Controllers\Api\PlayerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// TFT API Routes
Route::prefix('tft')->group(function () {
    
    // Global endpoints
    Route::get('/stats', [TftApiController::class, 'getGlobalStats']);
    Route::get('/status', [TftApiController::class, 'getTftStatus']);
    Route::get('/top-players', [TftApiController::class, 'getTopPlayers']);
    Route::post('/sync-matches', [TftApiController::class, 'syncMatches']);
    Route::get('/player', [TftApiController::class, 'getPlayer']);
    
    // Analytics endpoints
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboardData']);
        Route::get('/best-builds', [AnalyticsController::class, 'getBestBuilds']);
        Route::get('/popular-compositions', [AnalyticsController::class, 'getMostPopularCompositions']);
        Route::get('/best-augments', [AnalyticsController::class, 'getBestAugments']);
        Route::get('/best-items', [AnalyticsController::class, 'getBestItems']);
    });
    
    // Match endpoints
    Route::prefix('matches')->group(function () {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('/{matchId}', [MatchController::class, 'show']);
        Route::get('/{matchId}/replay', [MatchController::class, 'getReplay']);
    });
    
    // Build endpoints
    Route::prefix('builds')->group(function () {
        Route::get('/', [BuildController::class, 'index']);
        Route::get('/{buildId}', [BuildController::class, 'show']);
        Route::post('/', [BuildController::class, 'store']);
        Route::put('/{buildId}', [BuildController::class, 'update']);
        Route::delete('/{buildId}', [BuildController::class, 'destroy']);
    });
    
    // Player endpoints
    Route::prefix('players')->group(function () {
        Route::get('/', [PlayerController::class, 'index']);
        Route::post('/search', [PlayerController::class, 'search']);
        Route::post('/sync', [PlayerController::class, 'sync']);
        Route::get('/{puuid}', [PlayerController::class, 'show']);
    });
});
