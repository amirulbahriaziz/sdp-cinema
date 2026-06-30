<?php

namespace Database\Factories;

use App\Models\Hall;
use App\Models\Movie;
use App\Models\PriceTier;
use App\Models\Showtime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Showtime>
 */
class ShowtimeFactory extends Factory
{
    protected $model = Showtime::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+7 days');
        $end = (clone $start)->modify('+120 minutes');

        return [
            'movie_id' => Movie::factory(),
            'hall_id' => Hall::factory(),
            'tier_id' => PriceTier::factory(),
            'starts_at' => $start,
            'ends_at' => $end,
        ];
    }
}
