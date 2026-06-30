<?php

namespace Database\Seeders;

use App\Models\Showtime;
use Illuminate\Database\Seeder;

/**
 * Screenings across the halls. tier_id sets pricing (Option B): 1 = Classic, 2 = Premium.
 * Ids 1-4 stay on Hall 1 (referenced by DemoBookingSeeder's pre-sold/held seats).
 */
class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $showtimes = [
            // Cinema 1 — Hall 1 (ids 1-4 are fixed: demo bookings reference them)
            ['id' => 1,  'movie_id' => 1, 'hall_id' => 1, 'tier_id' => 1, 'starts_at' => '2026-07-02T19:30:00+08:00', 'ends_at' => '2026-07-02T21:19:00+08:00'],
            ['id' => 2,  'movie_id' => 1, 'hall_id' => 1, 'tier_id' => 2, 'starts_at' => '2026-07-02T22:00:00+08:00', 'ends_at' => '2026-07-02T23:49:00+08:00'],
            ['id' => 3,  'movie_id' => 2, 'hall_id' => 1, 'tier_id' => 1, 'starts_at' => '2026-07-02T18:45:00+08:00', 'ends_at' => '2026-07-02T21:31:00+08:00'],
            ['id' => 4,  'movie_id' => 3, 'hall_id' => 1, 'tier_id' => 1, 'starts_at' => '2026-07-03T14:15:00+08:00', 'ends_at' => '2026-07-03T15:51:00+08:00'],
            // Cinema 1 — Hall 2 (IMAX, premium)
            ['id' => 5,  'movie_id' => 2, 'hall_id' => 2, 'tier_id' => 2, 'starts_at' => '2026-07-02T20:00:00+08:00', 'ends_at' => '2026-07-02T22:46:00+08:00'],
            ['id' => 6,  'movie_id' => 6, 'hall_id' => 2, 'tier_id' => 2, 'starts_at' => '2026-07-03T21:00:00+08:00', 'ends_at' => '2026-07-03T23:28:00+08:00'],
            // Cinema 2 — GSC Mid Valley (Hall 3)
            ['id' => 7,  'movie_id' => 1, 'hall_id' => 3, 'tier_id' => 1, 'starts_at' => '2026-07-02T19:00:00+08:00', 'ends_at' => '2026-07-02T20:49:00+08:00'],
            ['id' => 8,  'movie_id' => 5, 'hall_id' => 3, 'tier_id' => 1, 'starts_at' => '2026-07-03T16:30:00+08:00', 'ends_at' => '2026-07-03T19:10:00+08:00'],
            ['id' => 9,  'movie_id' => 7, 'hall_id' => 3, 'tier_id' => 1, 'starts_at' => '2026-07-03T13:00:00+08:00', 'ends_at' => '2026-07-03T14:40:00+08:00'],
            // Cinema 3 — TGV 1 Utama (Hall 4)
            ['id' => 10, 'movie_id' => 4, 'hall_id' => 4, 'tier_id' => 2, 'starts_at' => '2026-07-02T21:15:00+08:00', 'ends_at' => '2026-07-02T23:37:00+08:00'],
            ['id' => 11, 'movie_id' => 8, 'hall_id' => 4, 'tier_id' => 2, 'starts_at' => '2026-07-03T20:30:00+08:00', 'ends_at' => '2026-07-03T22:38:00+08:00'],
            ['id' => 12, 'movie_id' => 3, 'hall_id' => 4, 'tier_id' => 1, 'starts_at' => '2026-07-04T11:00:00+08:00', 'ends_at' => '2026-07-04T12:36:00+08:00'],
        ];

        foreach ($showtimes as $s) {
            Showtime::create($s);
        }
    }
}
