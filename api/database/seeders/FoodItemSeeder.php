<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use Illuminate\Database\Seeder;

/**
 * F&B catalog — combos, food/snacks, beverages. Mirrors app/mock/food-items.json.
 * Prices are integer minor units; discount_price is null when there is no discount.
 */
class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 1, 'category' => 'combo', 'name' => 'Sweet Couple Combo', 'description' => '2 medium sweet popcorn + 2 regular soft drinks', 'price' => 3900, 'discount_price' => 3200, 'image_url' => 'https://images.unsplash.com/photo-1585647347483-22b66260dfff?w=400'],
            ['id' => 2, 'category' => 'combo', 'name' => 'Family Feast Combo', 'description' => '1 large popcorn + 4 regular drinks + 2 nacho boxes', 'price' => 6500, 'discount_price' => 5500, 'image_url' => 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=400'],
            ['id' => 3, 'category' => 'combo', 'name' => 'Solo Saver Combo', 'description' => '1 medium popcorn + 1 regular drink', 'price' => 2200, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1505686994434-e3cc5abf1330?w=400'],
            ['id' => 4, 'category' => 'food_snacks', 'name' => 'Caramel Popcorn (Large)', 'description' => 'Freshly popped caramel-coated popcorn', 'price' => 1800, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1578849278619-e73505e9610f?w=400'],
            ['id' => 5, 'category' => 'food_snacks', 'name' => 'Loaded Nachos', 'description' => 'Tortilla chips with cheese sauce and jalapeños', 'price' => 1500, 'discount_price' => 1200, 'image_url' => 'https://images.unsplash.com/photo-1513456852971-30c0b8199d4d?w=400'],
            ['id' => 6, 'category' => 'food_snacks', 'name' => 'Hot Dog', 'description' => 'Classic beef hot dog with toppings', 'price' => 1300, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1612392062798-2dd6e0b6b86b?w=400'],
            ['id' => 7, 'category' => 'beverages', 'name' => 'Regular Coke', 'description' => 'Chilled Coca-Cola, regular size', 'price' => 850, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=400'],
            ['id' => 8, 'category' => 'beverages', 'name' => 'Large Sprite', 'description' => 'Chilled Sprite, large size', 'price' => 1050, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=400'],
            ['id' => 9, 'category' => 'beverages', 'name' => 'Mineral Water', 'description' => '500ml bottled mineral water', 'price' => 500, 'discount_price' => null, 'image_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400'],
        ];

        foreach ($items as $item) {
            FoodItem::create($item + ['currency' => 'RM']);
        }
    }
}
