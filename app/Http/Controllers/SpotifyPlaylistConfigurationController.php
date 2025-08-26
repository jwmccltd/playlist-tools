<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use App\Services\SpotifyPlaylistConfigService;
use Inertia\Inertia;
use App\Models\PlaylistConfigurationOption;
use App\Models\PlaylistConfigurationOptionField;
use App\Models\PlaylistConfiguration;
use App\Models\Playlist;
use App\PlaylistConfigs\RunPlaylistConfig;
use Illuminate\Http\Request;
use Auth;

class SpotifyPlaylistConfigurationController extends Controller
{
    /**
     * Constructor.
     * @param DataService $dataService The data service.
     * @param PlaylistConfigurationOption $playlistConfigurationOption Playlist configuration options.
     * @param PlaylistConfigurationOptionField $playlistConfigurationOptionField The option fields linked to the playlist configuration option.
     * @param SpotifyPlaylistConfigService $spotifyPlaylistConfigService Spotify playlist config service id.
     * @param RunPlaylistConfig $runPlaylistConfig Playlist config runner.
     */
    public function __construct(
        protected DataService $dataService,
        protected PlaylistConfigurationOption $playlistConfigurationOption,
        protected PlaylistConfigurationOptionField $playlistConfigurationOptionField,
        protected SpotifyPlaylistConfigService $spotifyPlaylistConfigService,
        protected RunPlaylistConfig $runPlaylistConfig,
    ) {
        // Constructor
    }

    public function index(string $playlistLinkId)
    {
        $playlistConfigurations = $this->spotifyPlaylistConfigService->getPlaylistConfig($playlistLinkId);
        $selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistLinkId, Auth::id());

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
            'playlistLinkId'                    => $selectedPlaylistData['id'],
            'playlistName'                      => $selectedPlaylistData['name'],
            'playlistDescription'               => $selectedPlaylistData['description'],
            'playlistImageUrl'                  => $selectedPlaylistData['images'][0]['url'],
            'playlistFollowers'                 => (string) $selectedPlaylistData['followers']['total'] ?? '0',
            'playlistTrackTotal'                => (string) $selectedPlaylistData['tracks']['total'] ?? '0',
            'playlistConfigOptions'             => $this->playlistConfigurationOption->get(),
            'playlistConfigurationOptionFields' => $this->playlistConfigurationOptionField->get(),
            'playlistArtists'                   => $artists,
            'playlists'                         => $playlists,
            'playlistTracks'                    => $tracks,
            'playlistConfigurations'            => $playlistConfigurations,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'configOptionId' => 'required|integer',
            'configId'       => 'required|integer',
        ]);

        $optionFields = $this->playlistConfigurationOptionField->where('option_id', $request->input('configOptionId'))->first();
        $optionFields = json_decode($optionFields->config_fields, true);

        $this->validateData($request, $optionFields);

        $config = $request->all();
        unset($config['playlistLinkId']);
        unset($config['configOptionId']);

        $playlistConfig = PlaylistConfiguration::where('id', $request->input('configId'))->first();
        $playlistConfig->config = json_encode($config);
        $playlistConfig->option_id = $request->input('configOptionId');
        $playlistConfig->active = 0;
        $playlistConfig->save();
    }

    private function validateData($request, $optionFields)
    {
        // Compile validation from option fields.
        $validation = [];
        foreach ($optionFields as $fieldName => $field) {
            if (isset($field['validation'])) {
                $validation[$fieldName] = $field['validation'];
            }
        }

        $request->validate($validation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'configOptionId' => 'required|integer',
        ]);

        $optionFields = $this->playlistConfigurationOptionField->where('option_id', $request->input('configOptionId'))->first();
        $optionFields = $optionFields->config_fields;

        $this->validateData($request, $optionFields);

        $playlist = Playlist::where('playlist_link_id', $request->input('playlistLinkId'))->first();

        if (!$playlist) {
            $playlist = Playlist::create([
                'playlist_link_id' => $request->input('playlistLinkId'),
                'user_id' => Auth::id()
            ]);
        }

        $config = $request->all();
        unset($config['playlistLinkId']);
        unset($config['configOptionId']);

        $config = PlaylistConfiguration::create([
            'option_id' => $request->input('configOptionId'),
            'playlist_id' => $playlist->id,
            'config' => json_encode($config),
        ]);

        $config->step = $config->id;
        $config->save();

        return $config->id;
    }

    public function updateActiveState($configId, $state) {
        PlaylistConfiguration::where('id', $configId)->update(['active' => $state]);
    }

    public function delete($configId, $playlistLinkId)
    {
        PlaylistConfiguration::find($configId)->delete();

        return redirect()->route('spotify-playlist.index', [$playlistLinkId]);
    }

    public function updateStepOrder(string $ids)
    {
        $idArray = explode(',', $ids);

        foreach ($idArray as $i => $id) {
            PlaylistConfiguration::where('id', $id)->update(['step' => $i+1]);
        }
    }

    public function executeConfig($playlistLinkId)
    {
        $this->runPlaylistConfig->run($playlistLinkId, Auth::id());
    }
}
