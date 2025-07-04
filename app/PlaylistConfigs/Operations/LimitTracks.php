<?php
namespace App\PlaylistConfigs\Operations;
trait LimitTracks {
    private function limit(int $limitTo, string $byRemovingOption) {
        switch ($byRemovingOption) {
            case 'default-end':
                usort($this->selectedPlaylistData['all_tracks'], function($a, $b) {
                    return strtotime($a['added_at']) <=> strtotime($b['added_at']);
                });
            break;
        }

        $toRemove = sizeof($this->selectedPlaylistData['all_tracks']) - $limitTo;

        if ($toRemove <= 0) {
            return;
        }

        $delTracks['tracks'] = [];
        $trackListURIs = [];
        foreach ($this->selectedPlaylistData['all_tracks'] as $playlistTrack)
        {
            $delTracks['tracks'][] = ['uri' => $playlistTrack['track']['uri']];
            $trackListURIs[] = $playlistTrack['track']['uri'];
            if (sizeof($delTracks['tracks']) === $toRemove) {
                break;
            }
        }

        $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode($delTracks), 'DELETE');

        return $trackListURIs;
    }
}
