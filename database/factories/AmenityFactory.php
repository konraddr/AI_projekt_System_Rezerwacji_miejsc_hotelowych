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
     * @var list<array{name: string, icon: string}>
     */
    private const AMENITIES = [
        ['name' => 'Wi-Fi', 'icon' => 'wifi'],
        ['name' => 'Basen', 'icon' => 'pool'],
        ['name' => 'Parking', 'icon' => 'parking'],
        ['name' => 'Klimatyzacja', 'icon' => 'ac'],
        ['name' => 'Strefa SPA', 'icon' => 'spa'],
        ['name' => 'Sauna', 'icon' => 'sauna'],
        ['name' => 'Restauracja', 'icon' => 'restaurant'],
        ['name' => 'Siłownia', 'icon' => 'gym'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return fake()->randomElement(self::AMENITIES);
    }
}
