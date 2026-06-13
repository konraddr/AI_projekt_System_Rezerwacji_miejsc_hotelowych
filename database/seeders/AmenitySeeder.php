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
            'Wi-Fi',
            'Basen',
            'Parking',
            'Klimatyzacja',
            'Strefa SPA',
            'Sauna',
            'Restauracja',
            'Siłownia',
        ];

        foreach ($amenities as $name) {
            Amenity::firstOrCreate(['name' => $name]);
        }
    }
}
