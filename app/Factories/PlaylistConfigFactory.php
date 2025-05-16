<?php

namespace App\Factories;

use Exception;
use App\PlaylistConfigs\TrackLimiter;
use App\Services\DataService;

class PlaylistConfigFactory
{
    public static function factory(DataService $dataService, $config, $component)
    {
        if (class_exists("App\\PlaylistConfigs\\" . $component)) {
            $class = "App\\PlaylistConfigs\\" . $component;
            $dataService->cache = false;

            return new $class($dataService, $config);
        } else {
            throw new Exception("Invalid config type.");
        }
    }
}
