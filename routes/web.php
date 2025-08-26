<?php

use App\Http\Controllers\DashboardController;
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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
    ->middleware(['auth', 'verified']);

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
    Route::get('/spotify/playlist/configure/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'index'])->name('spotify-playlist.index');
    Route::get('/spotify/playlist/load-configuration-fields/{configOptionId}', [SpotifyPlaylistConfigurationController::class, 'loadConfigurationFields'])->name('spotify-playlist.load-configuration-fields');
    Route::post('/spotify/playlist/save-configuration', [SpotifyPlaylistConfigurationController::class, 'store'])->name('spotify-playlist.store');
    Route::post('/spotify/playlist/update-configuration', [SpotifyPlaylistConfigurationController::class, 'update'])->name('spotify-playlist.update');
    Route::post('/spotify/playlist/delete-configuration/{configId}/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'delete'])->name('spotify-playlist.delete');
    Route::post('/spotify/playlist/update-active-state/{configId}/{state}', [SpotifyPlaylistConfigurationController::class, 'updateActiveState'])->name('spotify-playlist.update-active-state');
    Route::post('/spotify/playlist/update-step-order/{ids}', [SpotifyPlaylistConfigurationController::class, 'updateStepOrder'])->name('spotify-playlist.update-step-order');
    Route::post('/spotify/playlist/execute-config/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'executeConfig'])->name('spotify-playlist.execute-config');
});
