<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MatchesController;
use App\Http\Controllers\Web\BuildsController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/matches', [DashboardController::class, 'matches'])->name('dashboard.matches');
    Route::get('/builds', [DashboardController::class, 'builds'])->name('dashboard.builds');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
});

Route::prefix('matches')->group(function () {
    Route::get('/', [MatchesController::class, 'index'])->name('matches.index');
    Route::get('/{matchId}', [MatchesController::class, 'show'])->name('matches.show');
});

Route::prefix('builds')->group(function () {
    Route::get('/', [BuildsController::class, 'index'])->name('builds.index');
    Route::get('/{buildId}', [BuildsController::class, 'show'])->name('builds.show');
});
