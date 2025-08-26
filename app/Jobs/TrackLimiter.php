<?php

namespace App\Jobs;

use App\PlaylistConfigs\Operations\ExcludeArtistTracks;
use App\PlaylistConfigs\Operations\ExcludeTracks;
use App\PlaylistConfigs\Operations\LimitTracks;
use App\PlaylistConfigs\Operations\AddToPlaylist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\DataService;

class TrackLimiter implements ShouldQueue
{
    public $selectedPlaylistData, $config, $playlistLinkId, $userId;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ExcludeArtistTracks, ExcludeTracks, LimitTracks, AddToPlaylist;

    /**
     * Create a new job instance.
     */
    public function __construct(protected DataService $dataService, $config, $playlistLinkId, $userId)
    {
        $this->config = $config;
        $this->playlistLinkId = $playlistLinkId;
        $this->selectedPlaylistData = $this->dataService->getData('playlist', 'playlists/' . $playlistLinkId, $userId);
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!empty($this->config->selectedArtists)) {
            $omitted = $this->excludeArtistTracks($this->config->selectedArtists);
            // Adjust limit to account for omitted artists.
            $this->config->limitTo -= $omitted;
        }

        if (!empty($this->config->selectedTracks)) {
            $omitted = $this->excludeTracks($this->config->selectedTracks);
            // Adjust limit to account for omitted artists.
            $this->config->limitTo -= $omitted;
        }

        $removedTracks = $this->limit($this->config->limitTo, $this->config->byRemovingOption);

        if (!empty($config->selectedPlaylists) && !empty($removedTracks)) {
            $this->addToPlaylists($this->config->selectedPlaylists, $removedTracks);
        }
    }
}
