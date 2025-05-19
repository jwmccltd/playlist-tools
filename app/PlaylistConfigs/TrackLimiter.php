<?php

namespace App\PlaylistConfigs;

class TrackLimiter extends PlaylistConfig {
    public function run($playlistId, $userId) {
        $this->selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistId, $userId);
        $this->limit($this->config->limitTo, $this->config->byRemovingOption);
    }

    private function limit(int $limitTo, string $byRemovingOption) {
        switch ($byRemovingOption) {
            case 'default-end':
                dd($this->selectedPlaylistData);
            break;
        }
    }

}
