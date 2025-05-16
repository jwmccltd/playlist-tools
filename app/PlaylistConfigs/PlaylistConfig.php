<?php

namespace App\PlaylistConfigs;

use App\Services\DataService;
abstract class PlaylistConfig
{
    public function __construct(protected DataService $dataService, protected $config) {
        // Constructor.
    }
    abstract protected function run($playlistId, $userId);
}
