<?php

namespace App\Services;

use Auth;

class SpotifyPlaylistService
{
    public function filterUserPlaylists($data) {
        $spotifyId = Auth::user()->spotify_id;

        $results = array_filter($data['items'], function ($item) use ($spotifyId) {
            return (int) $item['owner']['id'] === (int) $spotifyId;
        });

        return $results;
    }
}
