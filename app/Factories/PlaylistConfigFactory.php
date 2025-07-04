<?php

namespace App\Factories;

use Exception;
use App\PlaylistConfigs\TrackLimiter;
use App\Services\DataService;

class PlaylistConfigFactory
{
    public static function factory(DataService $dataService, $component, $userId, $playlistLinkId)
    {
        if (class_exists("App\\PlaylistConfigs\\" . $component)) {
            $class = "App\\PlaylistConfigs\\" . $component;
            return new $class($dataService, $userId, $playlistLinkId);
        } else {
            throw new Exception("Invalid config type.");
        }
    }
}
