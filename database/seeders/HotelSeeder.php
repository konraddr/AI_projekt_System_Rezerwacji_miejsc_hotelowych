<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wi-Fi', 'icon' => 'wifi'],
            ['name' => 'Basen', 'icon' => 'pool'],
            ['name' => 'Parking', 'icon' => 'parking'],
            ['name' => 'Klimatyzacja', 'icon' => 'ac'],
            ['name' => 'Strefa SPA', 'icon' => 'spa'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }

        $allAmenities = Amenity::all();

        Hotel::factory(10)->create()->each(function ($hotel) use ($allAmenities) {


            $randomAmenities = $allAmenities->random(rand(2, 5))->pluck('id')->toArray();

            foreach ($randomAmenities as $amenityId) {
                $hotel->amenities()->attach($amenityId, ['price' => rand(0, 50)]);
            }

            Room::factory(rand(3, 8))->create([
                'hotel_id' => $hotel->id
            ]);
        });
    }
}
