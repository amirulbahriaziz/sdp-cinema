<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Seeder;

/**
 * Cinemas + halls + a full 8x10 seat grid per hall (rows A-H, cols 1-10 = 80 seats;
 * rows A-E standard, F-H premium). Cinema 1 / Hall 1 stay id=1 (referenced by the
 * showtime + demo-booking seeders).
 */
class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $cinemas = [
            [
                'id' => 1, 'name' => 'TGV Suria KLCC', 'city' => 'Kuala Lumpur',
                'address' => 'Level 3, Suria KLCC, Kuala Lumpur City Centre, 50088 Kuala Lumpur',
                'halls' => [['id' => 1, 'name' => 'Hall 1'], ['id' => 2, 'name' => 'Hall 2 (IMAX)']],
            ],
            [
                'id' => 2, 'name' => 'GSC Mid Valley Megamall', 'city' => 'Kuala Lumpur',
                'address' => '3rd Floor, Mid Valley Megamall, Lingkaran Syed Putra, 59200 Kuala Lumpur',
                'halls' => [['id' => 3, 'name' => 'Hall 1']],
            ],
            [
                'id' => 3, 'name' => 'TGV 1 Utama', 'city' => 'Petaling Jaya',
                'address' => '3rd Floor, 1 Utama Shopping Centre, Bandar Utama, 47800 Petaling Jaya, Selangor',
                'halls' => [['id' => 4, 'name' => 'Hall 1 (Beanieplex)']],
            ],
        ];

        foreach ($cinemas as $c) {
            $halls = $c['halls'];
            unset($c['halls']);

            $cinema = Cinema::create($c);

            foreach ($halls as $h) {
                $hall = Hall::create(['id' => $h['id'], 'cinema_id' => $cinema->id, 'name' => $h['name']]);
                $this->seedSeats($hall->id);
            }
        }
    }

    /** A standard 8x10 grid: rows A-E standard, F-H premium. */
    private function seedSeats(int $hallId): void
    {
        $premiumRows = ['F', 'G', 'H'];

        foreach (range('A', 'H') as $row) {
            $type = in_array($row, $premiumRows, true) ? 'premium' : 'standard';

            for ($col = 1; $col <= 10; $col++) {
                Seat::create([
                    'hall_id' => $hallId,
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
