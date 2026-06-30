<?php

namespace Database\Factories;

use App\Models\Cinema;
use App\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hall>
 */
class HallFactory extends Factory
{
    protected $model = Hall::class;

    public function definition(): array
    {
        return [
            'cinema_id' => Cinema::factory(),
            'name' => 'Hall '.fake()->numberBetween(1, 8),
        ];
    }
}
