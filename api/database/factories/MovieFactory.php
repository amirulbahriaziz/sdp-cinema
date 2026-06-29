<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->catchPhrase(),
            'synopsis' => fake()->paragraph(),
            'duration_min' => fake()->numberBetween(85, 175),
            'release_date' => fake()->dateTimeBetween('-2 months', '+1 month')->format('Y-m-d'),
            'age_rating' => fake()->randomElement(['U', 'P13', '18']),
            'imdb_rating' => fake()->randomFloat(1, 5, 9),
            'poster_url' => 'https://image.tmdb.org/t/p/w500/placeholder.jpg',
            'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'genres' => fake()->randomElements(['Action', 'Sci-Fi', 'Drama', 'Comedy', 'Family'], 2),
            'casts' => [fake()->name(), fake()->name()],
            'director' => fake()->name(),
            'writers' => [fake()->name()],
            'sections' => fake()->randomElements(['new_releases', 'popular', 'recommended'], 1),
        ];
    }
}
