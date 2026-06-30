<?php

namespace Database\Factories;

use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodItem>
 */
class FoodItemFactory extends Factory
{
    protected $model = FoodItem::class;

    public function definition(): array
    {
        $price = fake()->numberBetween(500, 6500);

        return [
            'category' => fake()->randomElement(['combo', 'food_snacks', 'beverages']),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => $price,
            'discount_price' => fake()->boolean(40) ? (int) ($price * 0.85) : null,
            'currency' => 'RM',
            'image_url' => 'https://images.unsplash.com/photo-1585647347483-22b66260dfff?w=400',
        ];
    }
}
