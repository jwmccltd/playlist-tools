<?php
namespace App\PlaylistConfigs\Operations;
trait DeDuplicate {
    private function deDuplicate($deDuplicateBy) {
        $dupes = [];

        foreach ($this->selectedPlaylistData['all_tracks'] as $playlistTrack)
        {
            $dupes[$playlistTrack['track']['album']['artists'][0]['id']][] = [
                'track_uri' => $playlistTrack['track']['uri'],
                'added' => strtotime($playlistTrack['added_at']),
                'name' => $playlistTrack['track']['album']['artists'][0]['name']
            ];
        }

        $delTracks['tracks'] = [];
        foreach ($dupes AS $art => $d) {
            if (sizeof($d) > 1) {
                $trim = $d;

                usort($trim, function($a, $b) {
                    return $a['added'] <=> $b['added'];
                });

                array_pop($trim);

                foreach ($trim As $t) {
                    $delTracks['tracks'][] = ['uri' => $t['track_uri']];
                }

            }
        }

        $this->dataService->sendRequest("playlists/$this->playlistLinkId/tracks", $this->userId, json_encode($delTracks), 'DELETE');
    }
}
