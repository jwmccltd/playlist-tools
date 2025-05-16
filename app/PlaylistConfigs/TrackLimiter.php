<?php

namespace App\PlaylistConfigs;

class TrackLimiter extends PlaylistConfig {
    public function run($playlistId, $userId) {
        $selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistId, $userId);

        dd($selectedPlaylistData);
    }
}
