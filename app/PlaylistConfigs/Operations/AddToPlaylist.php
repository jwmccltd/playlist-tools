<?php
namespace App\PlaylistConfigs\Operations;
trait AddToPlaylist {

    private function addToPlaylists(array $playlists, array $tracks) {
        foreach ($playlists as $playlistId) {
            $this->dataService->sendRequest("playlists/$playlistId/tracks", $this->userId, json_encode($tracks), 'POST');
        }
    }
}
