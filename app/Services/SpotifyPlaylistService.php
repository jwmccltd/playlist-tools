<?php

namespace App\Services;

use Auth;

class SpotifyPlaylistService
{
    /**
     * Get playlists owned by user with given spotify id.
     * @param array $data An array of data containing items from spotify API.
     * @return array
     */
    public function filterUserPlaylists($data): array
    {
        $spotifyId = Auth::user()->spotify_id;

        $results = array_filter($data['items'], function ($item) use ($spotifyId) {
            return $item !== null && (int) $item['owner']['id'] === (int) $spotifyId;
        });

        return $results;
    }
}
