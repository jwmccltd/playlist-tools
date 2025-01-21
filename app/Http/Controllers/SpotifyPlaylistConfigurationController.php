<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use Inertia\Inertia;
use App\Models\PlaylistConfigurationOption;

class SpotifyPlaylistConfigurationController extends Controller
{
    /**
     * Constructor.
     * @param DataService $dataService The data service.
     */
    public function __construct(
        protected DataService $dataService,
        protected PlaylistConfigurationOption $playlistConfigurationOption
    ) {
        // Constructor
    }

    public function index($playlistId)
    {
        $data = $this->dataService->getData('playlist', 'playlists/' . $playlistId);

        dd($data);

        $artists = [];
        foreach ($data['tracks']['items'] as $items) {
            foreach($items['track']['artists'] as $artist) {
                $artists[] = $artist['name'];
            }
        }

        dd($artists);

        return Inertia::render('PlaylistConfiguration', [
            'playlistId'            => $data['id'],
            'playlistName'          => $data['name'],
            'playlistDescription'   => $data['description'],
            'playlistImageUrl'      => $data['images'][0]['url'],
            'playlistFollowers'     => (string) $data['followers']['total'] ?? '0',
            'playlistTrackTotal'    => (string) $data['tracks']['total'] ?? '0',
            'playlistConfigOptions' => $this->playlistConfigurationOption->get(),
        ]);
    }
}
