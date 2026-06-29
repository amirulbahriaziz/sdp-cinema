<?php

namespace Database\Seeders;

use App\Models\Showtime;
use Illuminate\Database\Seeder;

/**
 * Four screenings in Hall 1. Mirrors app/mock/showtimes.json. tier_id sets the
 * seat pricing (Option B): ST1/3/4 = Classic (1), ST2 = Premium (2).
 */
class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $showtimes = [
            ['id' => 1, 'movie_id' => 1, 'tier_id' => 1, 'starts_at' => '2026-07-02T19:30:00+08:00', 'ends_at' => '2026-07-02T21:19:00+08:00'],
            ['id' => 2, 'movie_id' => 1, 'tier_id' => 2, 'starts_at' => '2026-07-02T22:00:00+08:00', 'ends_at' => '2026-07-02T23:49:00+08:00'],
            ['id' => 3, 'movie_id' => 2, 'tier_id' => 1, 'starts_at' => '2026-07-02T18:45:00+08:00', 'ends_at' => '2026-07-02T21:31:00+08:00'],
            ['id' => 4, 'movie_id' => 3, 'tier_id' => 1, 'starts_at' => '2026-07-03T14:15:00+08:00', 'ends_at' => '2026-07-03T15:51:00+08:00'],
        ];

        foreach ($showtimes as $s) {
            Showtime::create([
                'id' => $s['id'],
                'movie_id' => $s['movie_id'],
                'hall_id' => 1,
                'tier_id' => $s['tier_id'],
                'starts_at' => $s['starts_at'],
                'ends_at' => $s['ends_at'],
            ]);
        }
    }
}
