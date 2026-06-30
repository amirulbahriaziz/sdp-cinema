<?php

namespace Database\Factories;

use App\Models\PriceTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceTier>
 */
class PriceTierFactory extends Factory
{
    protected $model = PriceTier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Classic', 'Premium']),
            'currency' => 'RM',
        ];
    }
}
