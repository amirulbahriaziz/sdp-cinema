<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'showtime_id' => Showtime::factory(),
            'reference' => 'SDP-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'status' => 'confirmed',
            'subtotal' => 0,
            'service_charge' => 0,
            'food_total' => 0,
            'discount' => 0,
            'total' => 0,
            'currency' => 'RM',
            'promo_code' => null,
        ];
    }
}
