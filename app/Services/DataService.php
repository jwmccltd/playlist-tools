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
            return json_decode($cacheData, true);
        }

        $data = $this->spotifyService->apiRequest(env('SPOTIFY_API_URL') . str_replace(',', '/', $url));

        // Decode the response JSON.
        $data = json_decode($data, true);

        $chunkedData = [];
        $returnResults = [];

        switch ($identifier) {
            case 'playlists':
                $chunkedData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next']);
                    $data = json_decode($data, true);
                    $chunkedData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                }

                foreach ($chunkedData as $i => $chunkData) {
                    if (empty($chunkData) === false) {
                        foreach ($chunkData as $listing) {
                            $returnResults[] = $listing;
                        }
                    }
                }

                break;
            case 'playlist':

                $returnResults = $data;
                $returnResults['all_tracks'] = [];

                $data = $data['tracks'];

                $chunkedData[] = $data['items'];
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next']);
                    $data = json_decode($data, true);        
                    $chunkedData[] = $data['items'];
                }

                foreach ($chunkedData as $i => $chunkData) {
                    if (empty($chunkData) === false) {
                        $returnResults['all_tracks'] = array_merge($returnResults['all_tracks'], $chunkData);
                    }
                }

                break;
            default:
                return $data ?? null;
        }

        $this->cacheService->setCacheItem($identifier, json_encode($returnResults), now()->addDays(1));

        return $returnResults;
    }
}
