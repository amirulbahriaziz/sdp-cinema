<?php

namespace App\Enums;

/** Per-showtime seat status — shared vocabulary across the seat map, locks and broadcasts. */
enum SeatStatus: string
{
    case Available = 'available';
    case Held = 'held';
    case Booked = 'booked';
}
