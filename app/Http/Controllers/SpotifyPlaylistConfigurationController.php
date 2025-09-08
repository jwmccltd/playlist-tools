<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Services\DataService;
use App\Services\SpotifyPlaylistConfigService;
use Inertia\Inertia;
use App\Models\PlaylistConfigurationOption;
use App\Models\PlaylistConfigurationOptionField;
use App\Models\PlaylistConfiguration;
use App\Models\PlaylistConfigurationSchedule;
use App\Models\Playlist;
use App\PlaylistConfigs\RunPlaylistConfig;
use Illuminate\Http\Request;
use Auth;
use Inertia\Response;
use Ramsey\Uuid\Type\Integer;

class SpotifyPlaylistConfigurationController extends Controller
{
    /**
     * Constructor.
     * @param DataService                      $dataService                      The data service.
     * @param PlaylistConfigurationOption      $playlistConfigurationOption      Playlist configuration options.
     * @param PlaylistConfigurationOptionField $playlistConfigurationOptionField The option fields linked to the playlist configuration.
     * @param SpotifyPlaylistConfigService     $spotifyPlaylistConfigService     Spotify playlist config service id.
     * @param RunPlaylistConfig                $runPlaylistConfig                Playlist config runner.
     * @param PlaylistConfigurationSchedule    $playlistConfigurationSchedule    The schedule.
     */
    public function __construct(
        protected DataService $dataService,
        protected PlaylistConfigurationOption $playlistConfigurationOption,
        protected PlaylistConfigurationOptionField $playlistConfigurationOptionField,
        protected SpotifyPlaylistConfigService $spotifyPlaylistConfigService,
        protected RunPlaylistConfig $runPlaylistConfig,
        protected PlaylistConfigurationSchedule $playlistConfigurationSchedule,
    ) {
        // Constructor
    }

    /**
     * Main page for spotify playlist configuration for playlist with a given link id.
     * @param string $playlistLinkId The spotify playlist id.
     * @return Response
     */
    public function index(string $playlistLinkId): Response
    {
        $playlistConfigurations = $this->spotifyPlaylistConfigService->getPlaylistConfig($playlistLinkId);
        $selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistLinkId, Auth::id());

        $artists = [];
        $tracks = [];
        foreach ($selectedPlaylistData['all_tracks'] as $items) {
            $tracks[$items['track']['uri']] = [];
            $tracks[$items['track']['uri']]['name'] = $items['track']['name'];
            $tracks[$items['track']['uri']]['artists'] = '';

            $trackArtists = [];
            foreach ($items['track']['artists'] as $artist) {
                $trackArtists[] = $artist['name'];
                if (!in_array($artist['name'], $artists)) {
                    $artists[$artist['id']] = $artist['name'];
                }
            }

            $tracks[$items['track']['uri']]['artists'] = implode(', ', $trackArtists);
        }

        $playlistsData = $this->dataService->getData('playlists', 'me/playlists', Auth::id());
        $playlists = [];
        foreach ($playlistsData as $playlist) {
            if ($playlist['id'] !== $selectedPlaylistData['id']) {
                $playlists[$playlist['id']] = $playlist['name'];
            }
        }

        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        $playlistConfigurationSchedule = [];
        if ($playlist) {
            $playlistConfigurationSchedule = $this->playlistConfigurationSchedule->where('playlist_id', $playlist->id)->get()->toArray();
        }

        return Inertia::render('PlaylistConfiguration', [
            'playlistLinkId'                    => $selectedPlaylistData['id'],
            'playlistName'                      => $selectedPlaylistData['name'],
            'playlistDescription'               => $selectedPlaylistData['description'],
            'playlistImageUrl'                  => $selectedPlaylistData['images'][0]['url'],
            'playlistFollowers'                 => (string) $selectedPlaylistData['followers']['total'] ?? '0',
            'playlistTrackTotal'                => (string) $selectedPlaylistData['tracks']['total'] ?? '0',
            'playlistArtists'                   => $artists,
            'playlists'                         => $playlists,
            'playlistTracks'                    => $tracks,
            'playlistConfigurations'            => $playlistConfigurations,
            'playlistConfigurationSchedule'                  => $playlistConfigurationSchedule,
        ]);
    }

    /**
     * Turn off the scheduling.
     * @param string $playlistLinkId The spotify playlist id.
     * @return void.
     */
    public function deactivateSchedule($playlistLinkId)
    {
        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        $playlistConfigSchedule = PlaylistConfigurationSchedule::where('playlist_id', $playlist->id)->first();
        $playlistConfigSchedule->activated = null;
        $playlistConfigSchedule->save();
    }

    /**
     * Turn off the scheduling.
     * @param ScheduleRequest $request        The schedule request class.
     * @param string          $playlistLinkId The spotify playlist id.
     * @return void.
     */
    public function setSchedule(ScheduleRequest $request, $playlistLinkId): void
    {
        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        $playlistConfigSchedule = PlaylistConfigurationSchedule::where('playlist_id', $playlist->id)->first();
        if (!$playlistConfigSchedule) {
            $playlistConfigSchedule = new PlaylistConfigurationSchedule();
        }
        $playlistConfigSchedule->playlist_id = $playlist->id;
        $playlistConfigSchedule->frequency   = $request->input('frequency');
        $playlistConfigSchedule->days        = $request->input('days');

        if (isset($request->input('time')['hours'])) {
            $playlistConfigSchedule->run_at_time = $request->input('time')['hours'] . ':' . $request->input('time')['minutes'];
        } else {
            $playlistConfigSchedule->run_at_time = $request->input('time') ? : null;
        }

        if ($request->input('frequency') === 'hourly') {
            $playlistConfigSchedule->run_at_time = null;
        }

        $playlistConfigSchedule->activated = now();
        $playlistConfigSchedule->save();
    }

    /**
     * Validate data.
     * @param Request $request      The request object to validate.
     * @param array   $optionFields The options fields with validation rules.
     * @return void.
     */
    private function validateData($request, array $optionFields): void
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

    /**
     * Store a new playlist configurations setting.
     * @param Request $request The request object.
     * @return integer.
     */
    public function store(Request $request): Integer
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
        $step = $config['step'];
        unset($config['playlistLinkId']);
        unset($config['configOptionId']);
        unset($config['step']);
        unset($config['active']);
        unset($config['id']);

        $playlistConfig = PlaylistConfiguration::where('option_id', $request->input('configOptionId'))
            ->where('playlist_id', $playlist->id)
            ->first();

        if ($playlistConfig === null) {
            $playlistConfig = new PlaylistConfiguration();
        }

        $playlistConfig->option_id  = $request->input('configOptionId');
        $playlistConfig->playlist_id = $playlist->id;
        $playlistConfig->config = $config;
        $playlistConfig->active = 1;
        $playlistConfig->step = $step;

        $playlistConfig->save();

        return $playlistConfig->id;
    }

    /**
     * Updates if playlist is active or not.
     * @param Request $request  The request object.
     * @param integer $optionId The option id.
     * @return void.
     */
    public function updateActiveState($playlistLinkId, $optionId, $state): void
    {
        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        PlaylistConfiguration::where('option_id', $optionId)
            ->where('playlist_id', $playlist->id)
            ->update(['active' => $state]);
    }

    /**
     * @param Request $request        The request object.
     * @param string  $playlistLinkId The spotify playlist id.
     * @return void.
     */
    public function updateStepOrder(Request $request, $playlistLinkId)
    {
        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        if ($playlist === null) {
            $playlist = Playlist::create([
                'playlist_link_id' => $playlistLinkId,
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($request->input('configOptionIds') as $i => $id) {
            $playlistConfig = PlaylistConfiguration::where('option_id', $id)
                ->where('playlist_id', $playlist->id)
                ->first();

            if ($playlistConfig === null) {
                $playlistConfig = new PlaylistConfiguration();
            }

            $playlistConfig->option_id = $id;
            $playlistConfig->playlist_id = $playlist->id;
            $playlistConfig->config = $playlistConfig->config ?? null;
            $playlistConfig->step = $i + 1;
            $playlistConfig->save();
        }
    }

    /**
     * Execute config saved against playlist.
     * @param string $playlistLinkId The spotify playlist id.
     * @return void.
     */
    public function executeConfig($playlistLinkId)
    {
        $this->runPlaylistConfig->run($playlistLinkId, Auth::id(), true);
    }
}
