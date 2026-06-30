<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with the full demo dataset. Order matters:
     * pricing -> cinema/halls/seats -> movies -> showtimes -> food -> derived
     * seat state (bookings + holds).
     */
    public function run(): void
    {
        // Primary test account for API/manual login.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => Hash::make('password')],
        );

        $this->call([
            PricingSeeder::class,
            CinemaSeeder::class,
            MovieSeeder::class,
            ShowtimeSeeder::class,
            FoodItemSeeder::class,
            DemoBookingSeeder::class,
        ]);
    }
}
