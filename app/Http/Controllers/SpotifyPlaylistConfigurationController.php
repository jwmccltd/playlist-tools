<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use App\Services\SpotifyPlaylistConfigService;
use Inertia\Inertia;
use App\Models\PlaylistConfigurationOption;
use App\Models\PlaylistConfiguration;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Auth;

class SpotifyPlaylistConfigurationController extends Controller
{
    /**
     * Constructor.
     * @param DataService $dataService The data service.
     * @param PlaylistConfigurationOption $playlistConfigurationOption Playlist configuration options.
     */
    public function __construct(
        protected DataService $dataService,
        protected PlaylistConfigurationOption $playlistConfigurationOption,
        protected SpotifyPlaylistConfigService $spotifyPlaylistConfigService,
    ) {
        // Constructor
    }

    public function index(string $playlistLinkId)
    {
        $playlistConfig = $this->spotifyPlaylistConfigService->getPlaylistConfig($playlistLinkId);
        $selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistLinkId, Auth::id());

        foreach ($playlistConfig as $i => $config) {
            $playlistConfig[$i]['config'] = json_decode($config['config']);
        }

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

        $playlistsData = $this->dataService->getData('playlists', 'me/playlists', Auth::id());
        $playlists = [];
        foreach ($playlistsData as $playlist) {
            if ($playlist['id'] !== $selectedPlaylistData['id']) {
                $playlists[$playlist['id']] = $playlist['name'];
            }
        }

        return Inertia::render('PlaylistConfiguration', [
            'playlistLinkId'         => $selectedPlaylistData['id'],
            'playlistName'           => $selectedPlaylistData['name'],
            'playlistDescription'    => $selectedPlaylistData['description'],
            'playlistImageUrl'       => $selectedPlaylistData['images'][0]['url'],
            'playlistFollowers'      => (string) $selectedPlaylistData['followers']['total'] ?? '0',
            'playlistTrackTotal'     => (string) $selectedPlaylistData['tracks']['total'] ?? '0',
            'playlistConfigOptions'  => $this->playlistConfigurationOption->get(),
            'playlistArtists'        => $artists,
            'playlists'              => $playlists,
            'playlistTracks'         => $tracks,
            'playlistConfigurations' => $playlistConfig,
        ]);
    }

    public function update(Request $request)
    {
        dd($request->all());

        $request->validate([
            'configOptionId' => 'required|integer',
        ]);

        $option = $this->playlistConfigurationOption->find($request->input('configOptionId'));

        $validation = config("playlistConfigValidation.{$option->component}");
        $request->validate($validation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'configOptionId' => 'required|integer',
        ]);

        $option = $this->playlistConfigurationOption->find($request->input('configOptionId'));

        $validation = config("playlistConfigValidation.{$option->component}");

        $request->validate($validation);

        $playlist = Playlist::where('playlist_link_id', $request->input('playlistLinkId'))->first();

        if (!$playlist) {
            $playlist = Playlist::create([
                'playlist_link_id' => $request->input('playlistLinkId'),
                'user_id' => Auth::id()
            ]);
        }

        PlaylistConfiguration::create([
            'option_id' => $option->id,
            'playlist_id' => $playlist->id,
            'config' => json_encode($request->input('config')),
        ]);
    }

    public function delete($configId, $playlistLinkId)
    {
        PlaylistConfiguration::find($configId)->delete();

        return redirect()->route('spotify-playlist.index', [$playlistLinkId]);
    }
}
