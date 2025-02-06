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

    public function index(string $playlistId)
    {
        $selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistId);

        $artists = [];
        foreach ($selectedPlaylistData['all_tracks'] as $items) {
            foreach ($items['track']['artists'] as $artist) {
                if (!in_array($artist['name'], $artists)) {
                    $artists[$artist['id']] = $artist['name'];
                }
            }
        }

        $playlistsData = $this->dataService->getData('playlists', 'me/playlists');
        $playlists = [];
        foreach ($playlistsData as $playlist) {
            if ($playlist['id'] !== $selectedPlaylistData['id']) {
                $playlists[$playlist['id']] = $playlist['name'];
            }
        }

        return Inertia::render('PlaylistConfiguration', [
            'playlistId'            => $selectedPlaylistData['id'],
            'playlistName'          => $selectedPlaylistData['name'],
            'playlistDescription'   => $selectedPlaylistData['description'],
            'playlistImageUrl'      => $selectedPlaylistData['images'][0]['url'],
            'playlistFollowers'     => (string) $selectedPlaylistData['followers']['total'] ?? '0',
            'playlistTrackTotal'    => (string) $selectedPlaylistData['tracks']['total'] ?? '0',
            'playlistConfigOptions' => $this->playlistConfigurationOption->get(),
            'playlistArtists'       => $artists,
            'playlists'             => $playlists,
        ]);
    }
}
