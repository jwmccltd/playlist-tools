<?php

namespace App\Services;

use App\Models\PlaylistConfiguration;

class SpotifyPlaylistConfigService
{
    public function __construct(protected PlaylistConfiguration $playlistConfiguration)
    {
        // Constructor.
    }

    public function getPlaylistsWithConfigs()
    {
        return PlaylistConfiguration
            ::join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->selectRaw('
                SUM(
                    CASE WHEN playlist_configurations.active = 1 THEN
                        1
                    ELSE
                        0
                    END) AS active_count,
                SUM(
                    CASE WHEN playlist_configurations.active = 0 THEN
                        1
                    ELSE
                        0
                    END) AS inactive_count,
                playlists.playlist_link_id
            ')
            ->groupBy('playlist_link_id')
            ->get()
            ->keyBy('playlist_link_id')
            ->toArray();
    }

    public function getPlaylistConfig($playlistLinkId)
    {
        $playlistConfigurations = PlaylistConfiguration
            ::join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->join('playlist_configuration_options', 'playlist_configurations.option_id', 'playlist_configuration_options.id')
            ->join('playlist_configuration_option_fields', 'playlist_configuration_option_fields.option_id', 'playlist_configuration_options.id')
            ->orderBy('step')
            ->where('playlist_link_id', $playlistLinkId)
            ->selectRaw('playlist_configurations.*, playlist_configuration_options.component')
            ->get()
            ->toArray();

        foreach ($playlistConfigurations as $i => $config) {
            $playlistConfigurations[$i]['model'] = json_decode($config['config'], true);
            $playlistConfigurations[$i]['configComponent'] = ['component' => $config['component'], 'id' => $config['option_id']];
            $playlistConfigurations[$i]['itemId'] = $i + 1;

            unset($playlistConfigurations[$i]['config']);
        }

        return $playlistConfigurations;
    }

    public function runConfig($playlistId) {

    }
}
