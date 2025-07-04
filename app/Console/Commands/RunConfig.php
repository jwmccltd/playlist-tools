<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlaylistConfiguration;
use App\Factories\PlaylistConfigFactory;
use App\Services\DataService;

class RunConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the config';

    public function __construct(protected DataService $dataService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playlistConfigs = PlaylistConfiguration
            ::where('playlist_configurations.active', 1)
            ->join('playlist_configuration_options', 'playlist_configuration_options.id', 'playlist_configurations.option_id')
            ->join('playlists', 'playlists.id', 'playlist_configurations.playlist_id')
            ->selectRaw('playlist_configurations.*, playlist_configuration_options.*, playlists.*')
            ->get();

        foreach ($playlistConfigs as $config) {
            $factory = PlaylistConfigFactory::factory(
                $this->dataService,
                $config->component,
                $config->user_id,
                $config->playlist_link_id
            );
            $factory->run(json_decode($config->config));
        }
    }
}
