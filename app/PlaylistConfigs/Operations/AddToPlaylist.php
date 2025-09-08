<?php

namespace App\PlaylistConfigs\Operations;

trait AddToPlaylist
{
    /**
     * Function to add to playlists.
     * @param array $playlists The array of playlists.
     * @param array $tracks    The array of tracks.
     * @return void.
     */
    private function addToPlaylists(array $playlists, array $tracks): void
    {
        foreach ($playlists as $playlistId) {
            $this->dataService->sendRequest("playlists/$playlistId/tracks", $this->userId, json_encode($tracks), 'POST');
        }
    }
}
