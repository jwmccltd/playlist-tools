<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpotifyConnectController;
use App\Http\Controllers\SpotifyApiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
 * SPOTIFY CONNECT.
 */
Route::get('/', [SpotifyConnectController::class, 'connect'])->name('connect');
Route::get('/spotify-auth', [SpotifyConnectController::class, 'spotifyAuth'])->name('spotify.auth');

/*
 * DASHBOARD
 */

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
     * SPOTIFY API
     */
    Route::get('/spotify/get-data/{identifier}', [SpotifyApiController::class, 'getData'])->name('spotify.get-data');
});

require __DIR__.'/auth.php';
