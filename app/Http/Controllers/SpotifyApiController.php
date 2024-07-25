<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\SpotifyAuth;
use App\Models\User;
use App\Services\CacheService;
use App\Services\SpotifyService;
use App\Services\SpotifyPlaylistService;

class SpotifyApiController extends Controller
{
    /**
     * Constructor.
     * @param SpotifyService $spotifyService The spotify service class.
     */
    public function __construct(
        protected SpotifyService $spotifyService,
        protected SpotifyPlaylistService $spotifyPlaylistService
    ) {
        // Constructor
    }

    public function getData($identifier)
    {
        $data = $this->spotifyService->apiRequest(env('SPOTIFY_API_URL') . '/me/' . $identifier);
        // Decode the response JSON.
        $data = json_decode($data, true);
        $returnData = [];

        switch ($identifier) {
            case 'playlists':
                $returnData[] = $this->spotifyPlaylistService->filterUserPlaylists($data);
                while (isset($data['next'])) {
                    $data = $this->spotifyService->apiRequest($data['next']);
                    $data = json_decode($data, true);
                    $returnRestults = $this->spotifyPlaylistService->filterUserPlaylists($data);
                    if (!empty($returnRestults)) {
                        $returnData[] = $returnRestults;
                    }
                }
                break;
            default:
                return null;
        }

        return $returnData;
    }
}
