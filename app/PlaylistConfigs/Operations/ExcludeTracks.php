<?php
namespace App\PlaylistConfigs\Operations;
trait ExcludeTracks {
    private function excludeTracks($selectedTracks) {
        $removed = sizeof($this->selectedPlaylistData['all_tracks']);
        foreach ($this->selectedPlaylistData['all_tracks'] as $i => $playlistTrack)
        {
            if (in_array($playlistTrack['track']['id'], $selectedTracks)) {
                unset($this->selectedPlaylistData['all_tracks'][$i]);
            }
        }
        return $removed - sizeof($this->selectedPlaylistData['all_tracks']);
    }
}
