<?php

namespace App\Services;

use App\Services\CacheService;
use App\Services\SpotifyService;
use App\Services\SpotifyPlaylistService;

class DataService
{
    /**
     * Constructor.
     * @param SpotifyService         $spotifyService         The spotify service class.
     * @param SpotifyPlaylistService $spotifyPlaylistService The spotify playlist service class.
     * @param CacheService           $cacheService           The cache service.
     */
    public function __construct(
        protected SpotifyService $spotifyService,
        protected SpotifyPlaylistService $spotifyPlaylistService,
        protected CacheService $cacheService,
    ) {
        // Constructor
    }

    /**
     * Function to get data from spotify api, or cache.
     *
     * @param string $identifier The identifier.
     * @param string $url        The url.
     * @return array
     */
    public function getData(string $identifier, string $url)
    {
        $cacheData = $this->cacheService->loadCacheItem($identifier);

        if ($cacheData !== null) {
            return json_decode($cacheData);
        }

        $data = $this->spotifyService->apiRequest(env('SPOTIFY_API_URL') . str_replace(',', '/', $url));

        // Decode the response JSON.
        $data = json_decode($data, true);
        $returnData = [];

        switch ($identifier) {
            case 'playlists':
                $returnData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next']);
                    $data = json_decode($data, true);
                    $returnData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                }
                break;
            default:
                return $data ?? null;
        }

        $returnResults = [];

        foreach ($returnData as $i => $returnedData) {
            if (empty($returnedData) === false) {
                foreach ($returnedData as $listing) {
                    $returnResults[] = $listing;
                }
            }
        }

        $this->cacheService->setCacheItem($identifier, json_encode($returnResults), now()->addDays(1));

        return $returnResults;
    }
}
