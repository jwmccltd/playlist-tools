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
        return PlaylistConfiguration
            ::join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->join('playlist_configuration_options', 'playlist_configurations.option_id', 'playlist_configuration_options.id')
            ->where('playlist_link_id', $playlistLinkId)
            ->selectRaw('playlist_configurations.*, playlist_configuration_options.component')
            ->get()
            ->toArray();
    }
}
