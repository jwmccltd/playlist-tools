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

        $trackLimiter = [
            'name'      => 'Limit the playlist max track count',
            'component' => 'TrackLimiter',
            'config-fields' => [
                'limitTo' => [
                    'label'      => 'Limit track count to max tracks',
                    'type'       => 'number',
                    'validation' => 'required|integer|between:1,10000',
                    'arrow'      => 1,
                ],
                'byRemovingOption' => [
                    'label'      => 'By removing',
                    'type'       => 'dropdown',
                    'validation' => 'required|in:default-end,default_start,oldest,newest,random',
                    'default'    => 'default-end',
                    'options'    => [
                        [ 'value' => 'default-end', 'text' => 'From the end of default order' ],
                        [ 'value' => 'default-start', 'text' => 'From the start of default order' ],
                        [ 'value' => 'oldest', 'text' => 'Oldest tracks first' ],
                        [ 'value' => 'newest', 'text' => 'Newest tracks first'],
                        [ 'value' => 'random', 'text' => 'Random tracks'],
                    ],
                    'plus'        => 1,
                ],
                'excludeArtists' => [
                    'label'       => 'Exclude these artists from removal',
                    'type'        => 'modal-select',
                    'dataSource'  => 'playlistArtists',
                    'modalTitle'  => 'Filter Playlist Artists',
                    'buttonLabel' => 'Select Artists',
                    'plus'        => 1,
                ],
                'excludeTracks'    => [
                    'label'         => 'Exclude these tracks from removal',
                    'type'          => 'modal-select',
                    'buttonLabel'   => 'Select Tracks',
                    'modalTitle'    => 'Filter Tracks',
                    'dataSource'    => 'playlistTracks',
                    'optionDisplay' => ['name','artists'],
                    'plus'          => 1,
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
                    'plus'        => 1,
                ],
                'excludeArtists' => [
                    'label'       => 'Exclude these artists from removal',
                    'type'        => 'modal-select',
                    'dataSource'  => 'playlistArtists',
                    'modalTitle'  => 'Filter Playlist Artists',
                    'buttonLabel' => 'Select Artists',
                    'plus'        => 1,
                ],
                'excludeTracks'    => [
                    'label'         => 'Exclude these tracks from removal',
                    'type'          => 'modal-select',
                    'buttonLabel'   => 'Select Tracks',
                    'modalTitle'    => 'Filter Tracks',
                    'dataSource'    => 'playlistTracks',
                    'optionDisplay' => ['name','artists'],
                    'plus'          => 1,
                ],
            ],
        ];

        $configs[] = $trackLimiter;
        $configs[] = $deduplicator;

        foreach ($configs as $config) {
            $configOption = PlaylistConfigurationOption::create([
                'name'      => $config['name'],
                'component' => $config['component'],
            ]);

            PlaylistConfigurationOptionField::create([
                'option_id'     => $configOption->id,
                'config_fields' => $config['config-fields'],
            ]);
        }
    }
}
