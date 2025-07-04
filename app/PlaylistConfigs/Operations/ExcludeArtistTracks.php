<?php
namespace App\PlaylistConfigs\Operations;
trait ExcludeArtistTracks {
    private function excludeArtistTracks($selectedArtists) {
        $removed = sizeof($this->selectedPlaylistData['all_tracks']);
        foreach ($this->selectedPlaylistData['all_tracks'] as $i => $playlistTrack)
        {
            foreach ($playlistTrack['track']['artists'] as $artist) {
                if (in_array($artist['id'], $selectedArtists)) {
                    unset($this->selectedPlaylistData['all_tracks'][$i]);
                }
            }
        }
        return $removed - sizeof($this->selectedPlaylistData['all_tracks']);
    }
}
