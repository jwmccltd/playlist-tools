<?php

namespace App\PlaylistConfigs\Operations;

use Ramsey\Uuid\Type\Integer;

trait CheckSelectedTracksAndArtists
{
    /**
     * Check is artist or track has been selected to be ommitted from operations.
     * @param array $track.
     * @return bool.
     */
    public function artistOrTrackShouldBeExcluded(array $track): bool
    {
        $exclude = false;

        if (in_array($track['uri'], $this->config['excludeTracks'])) {
            $exclude = true;
        }

        if (!empty($this->config['excludeArtists'])) {
            foreach ($track['artists'] as $artist) {
                if (in_array($artist['id'], $this->config['excludeArtists'])) {
                    $exclude = true;
                }
            }
        }

        return $exclude;
    }

    /**
     * Filter out tracks from array of tracks.
     * @param array $selectedTracks The array of tracks.
     * @return int.
     */
    private function excludeTracks(array $selectedTracks): int
    {
        $removed = sizeof($this->selectedPlaylistData['all_tracks']);
        foreach ($this->selectedPlaylistData['all_tracks'] as $i => $playlistTrack) {
            if (in_array($playlistTrack['track']['id'], $selectedTracks)) {
                unset($this->selectedPlaylistData['all_tracks'][$i]);
            }
        }
        return $removed - sizeof($this->selectedPlaylistData['all_tracks']);
    }

    /**
     * Filter out artists tracks from array of artists.
     * @param array $selectedArtists The array of artists.
     * @return int.
     */
    private function excludeArtistTracks(array $selectedArtists): int
    {
        $removed = sizeof($this->selectedPlaylistData['all_tracks']);
        foreach ($this->selectedPlaylistData['all_tracks'] as $i => $playlistTrack) {
            foreach ($playlistTrack['track']['artists'] as $artist) {
                if (in_array($artist['id'], $selectedArtists)) {
                    unset($this->selectedPlaylistData['all_tracks'][$i]);
                }
            }
        }
        return $removed - sizeof($this->selectedPlaylistData['all_tracks']);
    }
}
