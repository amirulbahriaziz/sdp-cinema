<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cinema>
 */
class CinemaFactory extends Factory
{
    protected $model = Cinema::class;

    public function definition(): array
    {
        return [
            'name' => 'TGV '.fake()->company(),
            'city' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
