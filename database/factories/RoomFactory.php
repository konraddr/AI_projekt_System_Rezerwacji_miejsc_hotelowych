<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'name' => fake()->randomElement(['Apartament Prezydencki', 'Pokój 2-osobowy Standard', 'Studio z widokiem', 'Pokój Rodzinny']),
            'description' => fake()->realText(150),
            'capacity' => fake()->numberBetween(1, 5),
            'price_per_night' => fake()->randomFloat(2, 120, 1500),
            'quantity' => fake()->numberBetween(1, 15),
        ];
    }
}
