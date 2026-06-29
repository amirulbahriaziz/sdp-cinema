<?php

namespace App\Services;

use App\Models\BookingSeat;
use App\Models\SeatLock;
use App\Models\Showtime;
use Illuminate\Support\Carbon;

class SeatMapService
{
    /**
     * Derive the live seat map for a showtime.
     *
     * Status is per (showtime x seat), never a flag on the seat row:
     *   booked    -> a booking_seats row exists for (showtime, seat)
     *   held      -> an active (non-expired) seat_locks row exists for (showtime, seat)
     *   available -> neither
     *
     * Price = lookup(showtime.tier, seat.type) from seat_type_prices (int minor units, RM).
     *
     * @return array{showtime: array, tier: array, seats: array<int, array>}
     */
    public function forShowtime(Showtime $showtime): array
    {
        $showtime->loadMissing(['tier.seatTypePrices', 'hall.cinema']);

        // Price lookup table: seat_type => int minor units.
        $priceByType = $showtime->tier->seatTypePrices
            ->pluck('price', 'seat_type')
            ->map(fn ($p) => (int) $p);

        $currency = $showtime->tier->currency;

        // Sold seats for this showtime (source of truth: booking_seats).
        $bookedSeatIds = BookingSeat::query()
            ->where('showtime_id', $showtime->id)
            ->pluck('seat_id')
            ->all();
        $bookedSeatIds = array_flip($bookedSeatIds);

        // Active (non-expired) holds for this showtime.
        $heldSeatIds = SeatLock::query()
            ->where('showtime_id', $showtime->id)
            ->where('expires_at', '>', Carbon::now())
            ->pluck('seat_id')
            ->all();
        $heldSeatIds = array_flip($heldSeatIds);

        $seats = $showtime->hall->seats()
            ->where('active', true)
            ->orderBy('row_label')
            ->orderBy('col_num')
            ->get()
            ->map(function ($seat) use ($bookedSeatIds, $heldSeatIds, $priceByType, $currency) {
                if (isset($bookedSeatIds[$seat->id])) {
                    $status = 'booked';
                } elseif (isset($heldSeatIds[$seat->id])) {
                    $status = 'held';
                } else {
                    $status = 'available';
                }

                return [
                    'id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'row_label' => $seat->row_label,
                    'col_num' => (int) $seat->col_num,
                    'type' => $seat->type,
                    'status' => $status,
                    'price' => $priceByType[$seat->type] ?? null,
                    'currency' => $currency,
                ];
            })
            ->values()
            ->all();

        return [
            'showtime' => [
                'id' => $showtime->id,
                'movie_id' => $showtime->movie_id,
                'hall_id' => $showtime->hall_id,
                'tier_id' => $showtime->tier_id,
                'starts_at' => $showtime->starts_at?->toIso8601String(),
                'ends_at' => $showtime->ends_at?->toIso8601String(),
                'cinema' => $showtime->hall->cinema ? [
                    'id' => $showtime->hall->cinema->id,
                    'name' => $showtime->hall->cinema->name,
                ] : null,
                'hall_name' => $showtime->hall->name,
            ],
            'tier' => [
                'id' => $showtime->tier->id,
                'name' => $showtime->tier->name,
                'currency' => $currency,
                'prices' => $priceByType->map(fn ($price, $type) => [
                    'seat_type' => $type,
                    'price' => $price,
                ])->values()->all(),
            ],
            'seats' => $seats,
        ];
    }
}
