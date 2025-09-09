<?php

namespace App\Services;

use App\Services\CacheService;
use App\Services\SpotifyService;
use App\Services\SpotifyPlaylistService;
use App\Models\User;

class DataService
{
    public $cache = false;

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
     * Function to make a request to the spotify api.
     *
     * @param string  $type   Type of request (e.g. POST, DELETE).
     * @param string  $url    The url.
     * @param integer $userId The user id.
     * @param mixed   $body   The request body.
     * @param string  $type   The type.
     * @param mixed   $fields The query fields.
     *
     * @return string
     */
    public function sendRequest(string $url, int $userId, $body = null, ?string $type = null, $fields = null)
    {
        $url = env('SPOTIFY_API_URL') . $url;
        return $this->spotifyService->apiRequest($url, $userId, null, $type, $body, $fields);
    }

    /**
     * Function to get data from spotify api, or cache.
     *
     * @param string  $identifier The identifier.
     * @param string  $url        The url.
     * @param integer $userId     The user id.
     * @param string  $fields     The fields if required.
     * @return array
     */
    public function getData(string $identifier, string $url, $userId, $fields = null)
    {
        $user = User::find($userId);

        if ($this->cache === true) {
            $cacheData = $this->cacheService->loadCacheItem($identifier);

            if ($cacheData !== null) {
                return json_decode($cacheData, true);
            }
        }

        $data = $this->spotifyService->apiRequest(env('SPOTIFY_API_URL') . str_replace(',', '/', $url), $userId, fields: $fields);

        // Decode the response JSON.
        $data = json_decode($data, true);

        $chunkedData = [];
        $returnResults = [];

        // Various processes to collate and format the data.
        switch ($identifier) {
            case 'playlists':
                $chunkedData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next'], $userId);
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
            case 'tracks':
                $returnResults = $data;
                $returnResults['all_tracks'] = [];

                $chunkedData[] = $data['items'];
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next'], $userId);
                    $data = json_decode($data, true);
                    $chunkedData[] = $data['items'];
                }

                foreach ($chunkedData as $i => $chunkData) {
                    if (empty($chunkData) === false) {
                        foreach ($chunkData as $track) {
                            $returnResults['all_tracks'][] = [
                                'uri' => $track['track']['uri'],
                                'added_at' => $track['added_at'],
                                'id' => $track['track']['id'],
                                'artists' => $track['track']['artists'],
                                'name' => $track['track']['name'],
                                'available_markets' => $track['track']['available_markets'],
                            ];
                        }
                    }
                }

                $returnResults = $returnResults['all_tracks'];

                break;

            case 'playlist':
                $returnResults = $data;
                $returnResults['all_tracks'] = [];

                $data = $data['tracks'];

                $chunkedData[] = $data['items'];
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next'], $userId);
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
        if ($this->cache === true) {
            $this->cacheService->setCacheItem($identifier, json_encode($returnResults), now()->addDays(1));
        }

        return $returnResults;
    }
}
