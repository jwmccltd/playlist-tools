<?php

namespace App\Jobs;

use App\PlaylistConfigs\Operations\ExcludeArtistTracks;
use App\PlaylistConfigs\Operations\ExcludeTracks;
use App\PlaylistConfigs\Operations\AddToPlaylist;
use App\PlaylistConfigs\Operations\DeDuplicate;
use App\Services\DataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeDuplicator implements ShouldQueue
{
    public $selectedPlaylistData, $config, $playlistLinkId, $userId;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ExcludeArtistTracks, ExcludeTracks, AddToPlaylist, DeDuplicate;

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
            $this->excludeArtistTracks($this->config->selectedArtists);
        }

        if (!empty($this->config->selectedTracks)) {
            $this->excludeTracks($this->config->selectedTracks);
        }

        $this->deduplicate($this->config->deduplicateBy);
    }
}
