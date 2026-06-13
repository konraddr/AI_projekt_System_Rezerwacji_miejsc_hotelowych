<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'permission' => UserPermission::Administrator,
            ]
        );

        $this->call([
            AmenitySeeder::class,
            HotelSeeder::class,
            RoomSeeder::class,
        ]);
    }
}
