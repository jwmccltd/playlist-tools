<?php

namespace App\PlaylistConfigs\Operations;

trait ExcludeTracks
{
    /**
     * Exclude tracks
     * @param array $selectedTrack The selected tracks.
     * @return int.
     */
    private function excludeTracks($selectedTracks): int
    {
        $removed = sizeof($this->selectedPlaylistData['all_tracks']);
        foreach ($this->selectedPlaylistData['all_tracks'] as $i => $playlistTrack) {
            if (in_array($playlistTrack['track']['id'], $selectedTracks)) {
                unset($this->selectedPlaylistData['all_tracks'][$i]);
            }
        }
        return $removed - sizeof($this->selectedPlaylistData['all_tracks']);
    }
}
