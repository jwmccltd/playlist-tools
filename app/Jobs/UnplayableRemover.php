<?php

namespace App\Jobs;

use App\PlaylistConfigs\Operations\CheckSelectedTracksAndArtists;
use App\PlaylistConfigs\Operations\RemoveUnplayable;
use App\Services\DataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnplayableRemover implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CheckSelectedTracksAndArtists;
    use RemoveUnplayable;

    public $selectedPlaylistData;
    public $config;
    public $playlistLinkId;
    public $userId;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(protected DataService $dataService, $config, $playlistLinkId, $userId)
    {
        $this->config = $config;
        $this->playlistLinkId = $playlistLinkId;

        $fields = 'items(added_at,track(id,uri,name,artists(id,name),available_markets)),next,total';
        $this->selectedPlaylistData = $this->dataService->getData('tracks', 'playlists/' . $playlistLinkId . '/tracks', $userId, $fields);

        $this->userId = $userId;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(): void
    {
        $this->removeUnplayable($this->config);
    }
}
