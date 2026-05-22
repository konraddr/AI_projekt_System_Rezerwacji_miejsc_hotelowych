<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hotel::all()->each(function (Hotel $hotel) {
            Room::factory(rand(2, 4))->create([
                'hotel_id' => $hotel->id,
            ]);
        });
    }
}
