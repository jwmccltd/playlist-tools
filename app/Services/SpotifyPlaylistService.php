<?php

namespace App\Services;

use Auth;

class SpotifyPlaylistService
{
    public function filterUserPlaylists($data) {
        $spotifyId = Auth::user()->spotify_id;

        $results = array_filter($data['items'], function ($item) use ($spotifyId) {
            return $item !== null && (int) $item['owner']['id'] === (int) $spotifyId;
        });

        return $results;
    }
}
