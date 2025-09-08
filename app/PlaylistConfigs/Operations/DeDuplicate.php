<?php

namespace App\PlaylistConfigs\Operations;

trait DeDuplicate
{
    /**
     * Deduplicate tracks.
     * @return void.
     */
    private function deDuplicate()
    {
        $trackMaps = [];

        // Loop and map all tracks key by URI.
        foreach ($this->selectedPlaylistData as $position => $playlistTrack) {
            if ($this->artistOrTrackShouldBeExcluded($playlistTrack)) {
                continue;
            }

            if (
                !isset($trackMaps[$playlistTrack['uri']])
            ) {
                $trackMaps[$playlistTrack['uri']] = [
                    'added_at' => $playlistTrack['added_at'],
                    'position' => $position,
                    'count'    => 1,
                ];
            } else {
                $trackMaps[$playlistTrack['uri']]['count'] += 1;

                // Make sure we end up with the newest added record as we want to preserve the track at this position.
                if (strtotime($playlistTrack['added_at']) > strtotime($trackMaps[$playlistTrack['uri']]['added_at'])) {
                    $trackMaps[$playlistTrack['uri']]['added_at'] = $playlistTrack['added_at'];
                    $trackMaps[$playlistTrack['uri']]['position'] = $position;
                }
            }
        }

        // For all URI keyed arrays of length > 1, we have a duplicate so return the duplicates.
        $trackMaps = array_filter($trackMaps, function ($trackData) {
            return $trackData['count'] > 1;
        });

        if (empty($trackMaps)) {
            return;
        }

        // Find where we will re-insert the track after removal and create the array structure for deletion.
        $deleteTracks['tracks'] = [];
        foreach ($trackMaps as $uri => $data) {
            $deleteTracks['tracks'][] = ['uri' => $uri];
            $insertBeforeUri = null;

            $position = $data['position'];
            while (isset($this->selectedPlaylistData[$position + 1])) {
                if ($this->selectedPlaylistData[$position + 1]['uri'] !== $uri) {
                    $insertBeforeUri = $this->selectedPlaylistData[$position + 1]['uri'];
                    break;
                }
                $position--;
            }

            $trackMaps[$uri]['insert_before_uri'] = $insertBeforeUri;
        }

        // Remove all duplicate tracks from playlist by URI.
        $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode($deleteTracks), 'DELETE');

        // Add the duplicate tracks to the end of the playlist.
        $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode(['uris' => array_keys($trackMaps)]), 'POST');

        // Loop read playlist and re-map the duplicate tracks
        foreach ($trackMaps as $uri => $mappedTrack) {
            $fields = 'items(added_at,track(id,uri,name,artists(id,name))),next,total';
            // Get the updated list
            $this->selectedPlaylistData = $this->dataService->getData('tracks', 'playlists/' . $this->playlistLinkId . '/tracks', $this->userId, $fields);

            $updateData = [];
            $fields = 'snapshot_id';
            $snap = $this->dataService->getData('snapshot_id', 'playlists/' . $this->playlistLinkId, $this->userId, $fields);

            $updateData['snapshot_id'] = $snap['snapshot_id'];
            foreach ($this->selectedPlaylistData as $newPosition => $data) {
                if ($data['uri'] === $mappedTrack['insert_before_uri'] || $mappedTrack['insert_before_uri'] === null) {
                    if ($mappedTrack['insert_before_uri'] === null) {
                        $newPosition = sizeof($this->selectedPlaylistData);
                    }

                    $updateData['insert_before'] = $newPosition;
                }

                if ($data['uri'] === $uri) {
                    $updateData['range_start'] = $newPosition;
                }
            }

            $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode($updateData), 'PUT');
        }
    }
}
