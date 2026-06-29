<?php

namespace Database\Seeders;

use App\Models\PriceTier;
use App\Models\SeatTypePrice;
use Illuminate\Database\Seeder;

/**
 * Option B pricing. Two selectable tiers, each with a price per seat type.
 * A tier card's range is MIN..MAX of its seat-type prices:
 *   Classic : standard RM 18.00, premium RM 25.00  -> 1800..2500
 *   Premium : standard RM 25.00, premium RM 35.00  -> 2500..3500
 * All values are integer minor units (cents), currency RM (MYR).
 */
class PricingSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['id' => 1, 'name' => 'Classic', 'prices' => ['standard' => 1800, 'premium' => 2500]],
            ['id' => 2, 'name' => 'Premium', 'prices' => ['standard' => 2500, 'premium' => 3500]],
        ];

        foreach ($tiers as $t) {
            $tier = PriceTier::create([
                'id' => $t['id'],
                'name' => $t['name'],
                'currency' => 'RM',
            ]);

            foreach ($t['prices'] as $type => $price) {
                SeatTypePrice::create([
                    'tier_id' => $tier->id,
                    'seat_type' => $type,
                    'price' => $price,
                ]);
            }
        }
    }
}
