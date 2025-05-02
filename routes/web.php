<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpotifyConnectController;
use App\Http\Controllers\SpotifyApiController;
use App\Http\Controllers\SpotifyPlaylistConfigurationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
 * SPOTIFY CONNECT.
 */
Route::get('/', function () {
    return redirect()->route('connect');
});
Route::get('/login', function () {
    return redirect()->route('connect');
})->name('login');
Route::get('/connect', [SpotifyConnectController::class, 'connect'])->name('connect');
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
    Route::get('/spotify/get-data/{identifier}/{url}', [SpotifyApiController::class, 'getData'])->name('spotify.get-data');

    /*
     * SPOTIFY PLAYLISTS CONFIGURE
     */
    Route::get('/spotify/playlist/configure/{playlistId}', [SpotifyPlaylistConfigurationController::class, 'index'])->name('spotify-playlist.index');
    Route::post('/spotify/playlist/save-configuration', [SpotifyPlaylistConfigurationController::class, 'store'])->name('spotify-playlist.store');
});