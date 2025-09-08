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
    /*
     * SPOTIFY API
     */
    Route::get('/spotify/get-data/{identifier}/{url}', [SpotifyApiController::class, 'getData'])->name('spotify.get-data');

    /*
     * SPOTIFY PLAYLISTS CONFIGURE
     */
    Route::get('/spotify/playlist/configure/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'index'])->name('spotify-playlist.index');
    Route::post('/spotify/playlist/save-configuration', [SpotifyPlaylistConfigurationController::class, 'store'])->name('spotify-playlist.store');
    Route::post('/spotify/playlist/update-active-state/{playlistLinkId}/{optionId}/{state}', [SpotifyPlaylistConfigurationController::class, 'updateActiveState'])->name('spotify-playlist.update-active-state');
    Route::post('/spotify/playlist/update-step-order/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'updateStepOrder'])->name('spotify-playlist.update-step-order');
    Route::post('/spotify/playlist/execute-config/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'executeConfig'])->name('spotify-playlist.execute-config');
    Route::post('/spotify/playlist/set-schedule/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'setSchedule'])->name('spotify-playlist.set-schedule');
    Route::post('/spotify/playlist/deactivate-schedule/{playlistLinkId}', [SpotifyPlaylistConfigurationController::class, 'deactivateSchedule'])->name('spotify-playlist.deactivate-schedule');
});
