<?php

namespace App\PlaylistConfigs;

use App\Services\DataService;
use App\Models\PlaylistConfiguration;
use App\Models\PlaylistConfigurationSchedule;
use App\Models\Playlist;
use App\Jobs\DeDuplicator;
use App\Jobs\TrackLimiter;
use Illuminate\Support\Facades\Bus;
use DateTime;
use Carbon\Carbon;

class RunPlaylistConfig
{
    /**
     * @param DataService $dataService The data service.
     */
    public function __construct(protected DataService $dataService)
    {
        // Constructor.
    }

    /**
     * Config to run daily.
     *
     * @param string $playlistLinkId The playlist link id.
     * @param int    $userId         The user id.
     * @param string $runAtTime      A DateTime string.
     * @param array  $days           An array of days.
     * @param string $lastRunTime    The time last run.
     *
     * @return void
     */
    public function runDaily(
        string $playlistLinkId,
        int $userId,
        string $runAtDateTime,
        array $days,
        ?string $lastRunDateTime = null
    ) {
        if (!in_array(strtolower(date('l')), $days)) {
            return;
        }

        $currentDateTime = Carbon::now();
        $runAtDateTime = Carbon::parse($runAtDateTime);

        if ($runAtDateTime < $currentDateTime) {
            return;
        }

        if ($lastRunDateTime === null || ($runAtDateTime->gte($currentDateTime) && ($currentDateTime->gt($lastRunDateTime)))) {
            $this->run($playlistLinkId, $userId);
        }
    }

    /**
     * Config to run hourly.
     *
     * @param string $playlistLinkId The playlist link id.
     * @param int    $userId         The user id.
     * @param array  $days           An array of days.
     * @param string $lastRunTime    The time last run.
     *
     * @return void
     */
    public function runHourly($playlistLinkId, $userId, $days, ?string $lastRunDateTime = null)
    {
        if (!in_array(strtolower(date('l')), $days)) {
            return;
        }

        if ($lastRunDateTime !== null) {
            $currentDateTime = Carbon::now();
            $lastRunDateTime = Carbon::parse($lastRunDateTime)->addHour();

            if ($lastRunDateTime->gt($currentDateTime)) {
                return;
            }
        }

        $this->run($playlistLinkId, $userId);
    }

    /**
     * Run the config.
     * @param string $playlistLinkId The playlist link id.
     * @param int    $userId         The user id.
     * @param boolean $synchronous   Whether to trigger the job synchonously or not.
     */
    public function run($playlistLinkId, $userId, $synchronous = false)
    {
        $playlistConfigs = PlaylistConfiguration
            ::where('playlist_configurations.active', 1)
            ->join('playlist_configuration_options', 'playlist_configuration_options.id', 'playlist_configurations.option_id')
            ->join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->where('playlist_configuration_options.is_global', 0)
            ->where('playlists.playlist_link_id', $playlistLinkId)
            ->selectRaw('playlist_configurations.*, playlist_configuration_options.*, playlists.*')
            ->orderBy('step')
            ->get();

        $globalConfig = PlaylistConfiguration
            ::where('playlist_configurations.active', 1)
            ->join('playlist_configuration_options', 'playlist_configuration_options.id', 'playlist_configurations.option_id')
            ->join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->where('playlist_configuration_options.is_global', 1)
            ->where('playlists.playlist_link_id', $playlistLinkId)
            ->first();

        $jobs = [];

        foreach ($playlistConfigs as $config) {
            switch ($config['component']) {
                case 'DeDuplicator':
                    $jobs[] = new DeDuplicator(
                        $this->dataService,
                        array_merge($config['config'], $globalConfig['config']),
                        $playlistLinkId,
                        $userId
                    );
                    break;
                case 'TrackLimiter':
                    $jobs[] = new TrackLimiter(
                        $this->dataService,
                        array_merge($config['config'], $globalConfig['config']),
                        $playlistLinkId,
                        $userId
                    );
                    break;
            }
        }

        $job = Bus::chain($jobs);

        if ($synchronous === true) {
            $job->onConnection('sync');
        }

        $job->dispatch();

        $playlist = Playlist::where('playlist_link_id', $playlistLinkId)->first();
        $playlistConfigSchedule = PlaylistConfigurationSchedule::where('playlist_id', $playlist->id)->first();
        $playlistConfigSchedule->last_run = now();
        $playlistConfigSchedule->save();
    }
}
