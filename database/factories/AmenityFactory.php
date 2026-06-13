<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Amenity>
 */
class AmenityFactory extends Factory
{
    protected $model = Amenity::class;

    /**
     * @var list<string>
     */
    private const AMENITIES = [
        'Wi-Fi',
        'Basen',
        'Parking',
        'Klimatyzacja',
        'Strefa SPA',
        'Sauna',
        'Restauracja',
        'Siłownia',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(self::AMENITIES),
        ];
    }
}
