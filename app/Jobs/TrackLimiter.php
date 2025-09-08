<?php

namespace App\Jobs;

use App\PlaylistConfigs\Operations\CheckSelectedTracksAndArtists;
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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CheckSelectedTracksAndArtists;
    use LimitTracks;
    use AddToPlaylist;

    public $selectedPlaylistData;
    public $config;
    public $playlistLinkId;
    public $userId;

    /**
     * Create a new job instance.
     * @return void.
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
     * @return void.
     */
    public function handle(): void
    {
        $this->limitTracks();
    }
}
