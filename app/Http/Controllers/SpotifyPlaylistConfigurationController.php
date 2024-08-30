<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use Inertia\Inertia;

class SpotifyPlaylistConfigurationController extends Controller
{
    /**
     * Constructor.
     * @param DataService $dataService The data service.
     */
    public function __construct(
        protected DataService $dataService
    ) {
        // Constructor
    }

    public function index($playlistId) 
    {
        $data = $this->dataService->getData('playlist', 'playlists/' . $playlistId);

        dd($data);

        return Inertia::render('PlaylistConfiguration', [
            
        ]);
    }
}
