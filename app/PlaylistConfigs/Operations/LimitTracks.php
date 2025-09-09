<?php

namespace App\PlaylistConfigs\Operations;

trait LimitTracks
{
    /**
     * Limit tracks
     * @return void.
     */
    private function limitTracks(): void
    {
        if (!empty($this->config['selectedArtists'])) {
            $omitted = $this->excludeArtistTracks($this->config['selectedArtists']);
            // Adjust limit to account for omitted artists.
            $this->config['limitTo'] -= $omitted;
        }

        if (!empty($this->config['selectedArtists'])) {
            $omitted = $this->excludeTracks($this->config['selectedArtists']);
            // Adjust limit to account for omitted artists.
            $this->config['limitTo'] -= $omitted;
        }

        switch ($this->config['byRemovingOption']) {
            case 'default-end':
                usort($this->selectedPlaylistData['all_tracks'], function ($a, $b) {
                    return strtotime($a['added_at']) <=> strtotime($b['added_at']);
                });
                break;
        }

        $toRemove = sizeof($this->selectedPlaylistData['all_tracks']) - $this->config['limitTo'];

        if ($toRemove <= 0) {
            return;
        }

        $delTracks['tracks'] = [];
        $trackListURIs = [];
        foreach ($this->selectedPlaylistData['all_tracks'] as $playlistTrack) {
            $delTracks['tracks'][] = ['uri' => $playlistTrack['track']['uri']];
            $trackListURIs[] = $playlistTrack['track']['uri'];
            if (sizeof($delTracks['tracks']) === $toRemove) {
                break;
            }
        }

        $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode($delTracks), 'DELETE');

        if (!empty($this->config['moveToSelectedPlaylists']) && !empty($trackListURIs)) {
            $this->addToPlaylists($this->config['moveToSelectedPlaylists'], $trackListURIs);
        }
    }
}
