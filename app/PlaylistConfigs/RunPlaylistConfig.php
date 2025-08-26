<?php

namespace App\PlaylistConfigs;

use App\Services\DataService;
use App\Models\PlaylistConfiguration;
use App\Jobs\DeDuplicator;
use App\Jobs\TrackLimiter;
use Illuminate\Support\Facades\Bus;
class RunPlaylistConfig
{
    public function __construct(protected DataService $dataService) {
        // Constructor.
    }
    public function run($playlistLinkId, $userId) {
        $playlistConfigs = PlaylistConfiguration
            ::where('playlist_configurations.active', 1)
            ->join('playlist_configuration_options', 'playlist_configuration_options.id', 'playlist_configurations.option_id')
            ->join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->where('playlists.playlist_link_id', $playlistLinkId)
            ->selectRaw('playlist_configurations.*, playlist_configuration_options.*, playlists.*')
            ->orderBy('step')
            ->get();

        $jobs = [];

        foreach ($playlistConfigs as $config) {
            $componentConfig = json_decode($config['config']);

            switch ($config['component']) {
                case 'DeDuplicator':
                    $jobs[] = new DeDuplicator(
                        $this->dataService,
                        $componentConfig,
                        $playlistLinkId,
                        $userId
                    );
                break;
                case 'TrackLimiter':
                    $jobs[] = new TrackLimiter(
                        $this->dataService,
                        $componentConfig,
                        $playlistLinkId,
                        $userId
                    );
                break;
            }
        }

        Bus::chain($jobs)->dispatch();
    }
}
