<?php

namespace Database\Factories;

use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition(): array
    {
        $row = fake()->randomLetter();
        $col = fake()->numberBetween(1, 10);

        return [
            'hall_id' => Hall::factory(),
            'seat_code' => strtoupper($row).$col,
            'row_label' => strtoupper($row),
            'col_num' => $col,
            'type' => fake()->randomElement(['standard', 'premium']),
            'active' => true,
        ];
    }
}
