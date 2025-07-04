<?php

namespace App\Http\Controllers;

use App\Services\SpotifyPlaylistConfigService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(protected SpotifyPlaylistConfigService $spotifyPlaylistConfigService) {
        // Constructor.
    }

    public function index() {
        $playlistsWithConfigs = $this->spotifyPlaylistConfigService->getPlaylistsWithConfigs();

        return Inertia::render('Dashboard', [
            'playlistsWithConfigs' => $playlistsWithConfigs,
        ]);
    }
}
