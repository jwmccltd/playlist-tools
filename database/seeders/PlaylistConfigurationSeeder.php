<?php

namespace Database\Seeders;

use App\Models\PlaylistConfigurationOption;
use Illuminate\Database\Seeder;

class PlaylistConfigurationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        PlaylistConfigurationOption::create([
            'name' => 'Limit the playlist max track count',
            'component' => 'TrackLimiter',
        ]);
    }
}
