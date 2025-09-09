<?php

namespace App\Services;

use App\Models\PlaylistConfiguration;
use App\Models\PlaylistConfigurationOption;

class SpotifyPlaylistConfigService
{
    public function __construct(
        protected PlaylistConfiguration $playlistConfiguration,
        protected PlaylistConfigurationOption $playlistConfigurationOption
    ) {
        // Constructor.
    }

    /**
     * Get all playlists with configs
     *
     * @return array
     */
    public function getPlaylistsWithConfigs(): array
    {
        return PlaylistConfiguration
            ::join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->join('playlist_configuration_options', 'playlist_configuration_options.id', 'playlist_configurations.option_id')
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
            ->where('is_global', 0)
            ->groupBy('playlist_link_id')
            ->get()
            ->keyBy('playlist_link_id')
            ->toArray();
    }

    /**
     * Get playlist config options with any saved config.
     *
     * @return array
     */
    public function getPlaylistConfig($playlistLinkId): array
    {
        $playlistConfigurations = $this->playlistConfigurationOption
            ->with('fields')
            ->with([
                'config' => function ($query) use ($playlistLinkId) {
                    $query->join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
                    ->where('playlist_link_id', $playlistLinkId);
                }])
            ->get()
            ->sortBy(function ($item, $key) {
                return $item->config->step ?? $key;
            })
            ->toArray();

        $playlistConfigurations = array_values($playlistConfigurations);

        $step = 1;
        foreach ($playlistConfigurations as $i => $config) {
            if ($config['config'] === null) {
                $playlistConfigurations[$i]['config'] = [];
                $playlistConfigurations[$i]['config']['step'] = $step;
                $playlistConfigurations[$i]['config']['active'] = 0;
                $step++;
            } else {
                $playlistConfigurations[$i]['config'] = $config['config']['config'];
                $playlistConfigurations[$i]['config']['step'] = $config['config']['step'];
                $playlistConfigurations[$i]['config']['active'] = $config['config']['active'];
            }
        }

        return $playlistConfigurations;
    }
}
