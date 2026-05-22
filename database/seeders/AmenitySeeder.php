<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
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
            ['name' => 'Sauna', 'icon' => 'sauna'],
            ['name' => 'Restauracja', 'icon' => 'restaurant'],
            ['name' => 'Siłownia', 'icon' => 'gym'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                ['icon' => $amenity['icon']]
            );
        }
    }
}
