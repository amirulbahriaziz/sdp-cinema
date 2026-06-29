<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Seeder;

/**
 * One cinema, one hall, a full 8x10 seat grid (rows A-H, cols 1-10 = 80 seats).
 * Rows A-E are standard, rows F-H are premium. Mirrors app/mock/seats-1.json.
 */
class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $cinema = Cinema::create([
            'id' => 1,
            'name' => 'TGV Suria KLCC',
            'city' => 'Kuala Lumpur',
            'address' => 'Level 3, Suria KLCC, Kuala Lumpur City Centre, 50088 Kuala Lumpur',
        ]);

        $hall = Hall::create([
            'id' => 1,
            'cinema_id' => $cinema->id,
            'name' => 'Hall 1',
        ]);

        $premiumRows = ['F', 'G', 'H'];

        foreach (range('A', 'H') as $row) {
            $type = in_array($row, $premiumRows, true) ? 'premium' : 'standard';

            for ($col = 1; $col <= 10; $col++) {
                Seat::create([
                    'hall_id' => $hall->id,
                    'seat_code' => $row.$col,
                    'row_label' => $row,
                    'col_num' => $col,
                    'type' => $type,
                    'active' => true,
                ]);
            }
        }
    }
}
