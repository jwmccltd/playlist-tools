<?php

namespace App\PlaylistConfigs;

use App\Services\DataService;
abstract class PlaylistConfig
{
    public $selectedPlaylistData;
    public function __construct(protected DataService $dataService, protected $userId, protected $playlistLinkId) {
        // Constructor.
        $this->selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $this->playlistLinkId, $this->userId);
    }
    abstract protected function run(object $config);
}
