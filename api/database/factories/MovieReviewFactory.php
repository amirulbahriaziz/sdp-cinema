<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\MovieReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovieReview>
 */
class MovieReviewFactory extends Factory
{
    protected $model = MovieReview::class;

    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(3),
            'body' => fake()->paragraph(),
        ];
    }
}
