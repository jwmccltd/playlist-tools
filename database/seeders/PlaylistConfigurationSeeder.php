<?php

namespace Database\Seeders;

use App\Models\PlaylistConfigurationOption;
use App\Models\PlaylistConfigurationOptionField;
use Illuminate\Database\Seeder;

class PlaylistConfigurationSeeder extends Seeder
{
    /**
     * Seed the playlist configuration options.
     */
    public function run(): void
    {
        $configs = [];

        $artistTrackFilter = [
            'name'      => 'Filter artists and tracks from operations',
            'component' => 'ArtistTrackFilter',
            'config-fields' => [
                'excludeArtists' => [
                    'label'       => 'Exclude these artists from removal',
                    'type'        => 'modal-select',
                    'dataSource'  => 'playlistArtists',
                    'modalTitle'  => 'Filter Playlist Artists',
                    'buttonLabel' => 'Select Artists',
                    'default'     => [],
                ],
                'excludeTracks'    => [
                    'label'         => 'Exclude these tracks from removal',
                    'type'          => 'modal-select',
                    'buttonLabel'   => 'Select Tracks',
                    'modalTitle'    => 'Filter Tracks',
                    'dataSource'    => 'playlistTracks',
                    'optionDisplay' => ['name','artists'],
                    'default'       => [],
                ],
            ],
            'is_global' => 1,
        ];

        $trackLimiter = [
            'name'      => 'Limit the playlist max track count',
            'component' => 'TrackLimiter',
            'config-fields' => [
                'limitTo' => [
                    'label'      => 'Limit track count to max tracks',
                    'type'       => 'number',
                    'validation' => 'required|integer|between:1,10000',
                ],
                'byRemovingOption' => [
                    'label'      => 'By removing',
                    'type'       => 'dropdown',
                    'validation' => 'required|in:default-end,default_start,oldest,newest,random',
                    'default'    => 'default-end',
                    'options'    => [
                        [ 'value' => 'default-end', 'text' => 'From the end of default order' ],
                    ],
                ],
                'moveToSelectedPlaylists' => [
                    'label'       => 'Move removed to tracks to',
                    'buttonLabel' => 'Select Playlists',
                    'modalTitle'  => 'Filter Playlists',
                    'dataSource'  => 'playlists',
                    'type'        => 'modal-select',
                ],
            ],
        ];

        $deduplicator = [
            'name'      => 'Deduplicate artists or tracks',
            'component' => 'DeDuplicator',
            'config-fields' => [
                'deduplicateBy' => [
                    'label'      => 'Deduplicate',
                    'type'       => 'dropdown',
                    'validation' => 'required|in:tracks,artists',
                    'default'    => 'tracks',
                    'options'    => [
                        [ 'value' => 'tracks', 'text' => 'By Track' ],
                        [ 'value' => 'artists', 'text' => 'By Artist' ],
                    ],
                ],
            ],
        ];

        $configs[] = $artistTrackFilter;
        $configs[] = $trackLimiter;
        $configs[] = $deduplicator;

        foreach ($configs as $config) {
            $configOption = PlaylistConfigurationOption::create([
                'name'      => $config['name'],
                'component' => $config['component'],
                'is_global' => $config['is_global'] ?? 0,
            ]);

            PlaylistConfigurationOptionField::create([
                'option_id'     => $configOption->id,
                'config_fields' => $config['config-fields'],
            ]);
        }
    }
}
