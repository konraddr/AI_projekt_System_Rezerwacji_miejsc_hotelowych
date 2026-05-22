<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hotel::factory()
            ->count(5)
            ->sequence(
                ['city' => 'Kraków', 'latitude' => 50.0647, 'longitude' => 19.9450],
                ['city' => 'Gdańsk', 'latitude' => 54.3520, 'longitude' => 18.6466],
                ['city' => 'Warszawa', 'latitude' => 52.2297, 'longitude' => 21.0122],
                ['city' => 'Zakopane', 'latitude' => 49.2992, 'longitude' => 19.9496],
                ['city' => 'Wrocław', 'latitude' => 51.1079, 'longitude' => 17.0385],
            )
            ->create();
    }
}
