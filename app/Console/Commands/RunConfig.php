<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlaylistConfigurationSchedule;
use App\PlaylistConfigs\RunPlaylistConfig;
use App\Services\DataService;
use App\Services\SpotifyService;

class RunConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the configs for all playlists with configs set';

    public function __construct(
        protected DataService $dataService,
        protected RunPlaylistConfig $runPlaylistConfig,
        protected SpotifyService $spotifyService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schedules = PlaylistConfigurationSchedule::join('playlists', 'playlists.id', 'playlist_configuration_schedule.playlist_id')
            ->whereNotNull('activated')
            ->selectRaw('playlists.playlist_link_id, playlists.user_id, playlist_configuration_schedule.*')
            ->get();

        foreach ($schedules as $schedule) {
            // Make sure access token is up to date.
            $this->spotifyService->getSetAccessToken($schedule->user_id);

            switch ($schedule->frequency) {
                case 'daily':
                    $this->runPlaylistConfig->runDaily(
                        $schedule->playlist_link_id,
                        $schedule->user_id,
                        $schedule->run_at_time,
                        $schedule->days,
                        $schedule->last_run,
                    );
                    break;
                case 'hourly':
                    $this->runPlaylistConfig->runHourly(
                        $schedule->playlist_link_id,
                        $schedule->user_id,
                        $schedule->days,
                        $schedule->last_run,
                    );
                    break;
            }
        }
    }
}
