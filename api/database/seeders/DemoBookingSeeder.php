<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\SeatTypePrice;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the *derived* seat state for the demo so the live seat map matches
 * app/mock/seats-1.json and seats-2.json:
 *   - booked seats  -> a confirmed Booking + booking_seats (the sold-once truth)
 *   - held seats    -> active seat_locks owned by a "house" patron
 *
 * Two demo accounts are created:
 *   - demo@sdpcinema.test (password) : the primary login used to exercise booking
 *   - house@sdpcinema.test           : simulates other patrons who already hold/booked seats
 */
class DemoBookingSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::firstOrCreate(
            ['email' => 'demo@sdpcinema.test'],
            ['name' => 'Demo User', 'password' => Hash::make('password')],
        );

        $house = User::firstOrCreate(
            ['email' => 'house@sdpcinema.test'],
            ['name' => 'House Guest', 'password' => Hash::make('password')],
        );

        // Pre-sold seats per showtime (mirrors the "booked" seats in the fixtures).
        $booked = [
            1 => ['A1', 'A2', 'B5', 'B6', 'H9', 'H10'],
            2 => ['E1', 'E2', 'E3', 'F7', 'G7'],
        ];

        // Currently-held seats per showtime (mirrors the "held" seats in the fixtures).
        $held = [
            1 => ['C5', 'C6', 'D4'],
            2 => ['A5'],
        ];

        $ref = 1;
        foreach ($booked as $showtimeId => $seatCodes) {
            $this->createConfirmedBooking($house, $showtimeId, $seatCodes, $ref++);
        }

        foreach ($held as $showtimeId => $seatCodes) {
            $this->createHolds($house, $showtimeId, $seatCodes);
        }
    }

    private function createConfirmedBooking(User $owner, int $showtimeId, array $seatCodes, int $ref): void
    {
        $showtime = Showtime::findOrFail($showtimeId);
        $prices = $this->priceMap($showtime->tier_id);

        $lines = [];
        $subtotal = 0;
        foreach ($seatCodes as $code) {
            $seat = $this->seat($code);
            $price = $prices[$seat->type];
            $subtotal += $price;
            $lines[] = ['seat_id' => $seat->id, 'unit_price' => $price];
        }

        $serviceCharge = (int) round($subtotal * 0.05);
        $total = $subtotal + $serviceCharge;
        $reference = 'SDP-2026-'.str_pad((string) $ref, 6, '0', STR_PAD_LEFT);

        $booking = Booking::create([
            'user_id' => $owner->id,
            'showtime_id' => $showtimeId,
            'reference' => $reference,
            'status' => 'confirmed',
            'subtotal' => $subtotal,
            'service_charge' => $serviceCharge,
            'food_total' => 0,
            'discount' => 0,
            'total' => $total,
            'currency' => 'RM',
            'promo_code' => null,
        ]);

        foreach ($lines as $line) {
            BookingSeat::create([
                'booking_id' => $booking->id,
                'showtime_id' => $showtimeId,
                'seat_id' => $line['seat_id'],
                'unit_price' => $line['unit_price'],
            ]);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'method' => 'card',
            'amount' => $total,
            'currency' => 'RM',
            'status' => 'paid',
            'reference' => 'PAY-'.str_pad((string) $ref, 6, '0', STR_PAD_LEFT),
        ]);
    }

    private function createHolds(User $holder, int $showtimeId, array $seatCodes): void
    {
        foreach ($seatCodes as $code) {
            SeatLock::create([
                'showtime_id' => $showtimeId,
                'seat_id' => $this->seat($code)->id,
                'holder_id' => $holder->id,
                // Far-future TTL so the demo seat map stays "held" across a session.
                'expires_at' => now()->addYear(),
            ]);
        }
    }

    /** Build a [seat_type => price] lookup for a tier (Option B pricing). */
    private function priceMap(int $tierId): array
    {
        return SeatTypePrice::where('tier_id', $tierId)
            ->pluck('price', 'seat_type')
            ->all();
    }

    private function seat(string $code): Seat
    {
        return Seat::where('hall_id', 1)->where('seat_code', $code)->firstOrFail();
    }
}
