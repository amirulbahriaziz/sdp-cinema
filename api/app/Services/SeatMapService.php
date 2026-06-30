<?php

namespace App\Services;

use App\Enums\SeatStatus;
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

        // Price lookup table: seat_type value => int minor units (seat_type is a SeatType enum).
        $priceByType = $showtime->tier->seatTypePrices
            ->mapWithKeys(fn ($p) => [$p->seat_type->value => (int) $p->price]);

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
                    $status = SeatStatus::Booked;
                } elseif (isset($heldSeatIds[$seat->id])) {
                    $status = SeatStatus::Held;
                } else {
                    $status = SeatStatus::Available;
                }

                return [
                    'id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'row_label' => $seat->row_label,
                    'col_num' => (int) $seat->col_num,
                    'type' => $seat->type->value,
                    'status' => $status->value,
                    'price' => $priceByType[$seat->type->value] ?? null,
                    'currency' => $currency,
                ];
            })
            ->values()
            ->all();

        // Contract shape (matches app/mock seat map): top-level showtime_id + currency,
        // tier with price range, and the rows/cols the grid renders from.
        $rows = collect($seats)->pluck('row_label')->unique()->values()->all();
        $cols = (int) (collect($seats)->max('col_num') ?? 0);
        $seatTypePrices = $priceByType
            ->map(fn ($price, $type) => ['seat_type' => $type, 'price' => $price])
            ->values()->all();

        return [
            'showtime_id' => $showtime->id,
            'currency' => $currency,
            'tier' => [
                'id' => $showtime->tier->id,
                'name' => $showtime->tier->name,
                'currency' => $currency,
                'price_min' => $priceByType->isNotEmpty() ? (int) $priceByType->min() : null,
                'price_max' => $priceByType->isNotEmpty() ? (int) $priceByType->max() : null,
                'seat_type_prices' => $seatTypePrices,
            ],
            'rows' => $rows,
            'cols' => $cols,
            'seat_type_prices' => $seatTypePrices,
            'seats' => $seats,
        ];
    }
}
