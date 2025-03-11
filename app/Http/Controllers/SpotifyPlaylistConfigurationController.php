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
        $tracks = [];
        foreach ($selectedPlaylistData['all_tracks'] as $items) {
            $tracks[$items['track']['id']] = [];
            $tracks[$items['track']['id']]['name'] = $items['track']['name'];
            $tracks[$items['track']['id']]['artists'] = '';

            $trackArtists = [];
            foreach ($items['track']['artists'] as $artist) {
                $trackArtists[] = $artist['name'];
                if (!in_array($artist['name'], $artists)) {
                    $artists[$artist['id']] = $artist['name'];
                }
            }

            $tracks[$items['track']['id']]['artists'] = implode(', ', $trackArtists);
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
            'playlistTracks'        => $tracks,
        ]);
    }
}
