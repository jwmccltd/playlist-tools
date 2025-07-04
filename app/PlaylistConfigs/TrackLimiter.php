<?php

namespace App\PlaylistConfigs;

use App\PlaylistConfigs\Operations\ExcludeArtistTracks;
use App\PlaylistConfigs\Operations\ExcludeTracks;
use App\PlaylistConfigs\Operations\LimitTracks;
use App\PlaylistConfigs\Operations\AddToPlaylist;

class TrackLimiter extends PlaylistConfig {

    use ExcludeArtistTracks;
    use ExcludeTracks;
    use LimitTracks;
    use AddToPlaylist;

    public function run(object $config) {
        if (!empty($config->selectedArtists)) {
            $omitted = $this->excludeArtistTracks($config->selectedArtists);
            // Adjust limit to account for omitted artists.
            $config->limitTo -= $omitted;
        }

        if (!empty($config->selectedTracks)) {
            $omitted = $this->excludeTracks($config->selectedTracks);
            // Adjust limit to account for omitted artists.
            $config->limitTo -= $omitted;
        }

        $removedTracks = $this->limit($config->limitTo, $config->byRemovingOption);

        if (!empty($config->selectedPlaylists) && !empty($removedTracks)) {
            $this->addToPlaylists($config->selectedPlaylists, $removedTracks);
        }
    }
}
