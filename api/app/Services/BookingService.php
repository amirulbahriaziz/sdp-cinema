<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\SeatStatus;
use App\Events\SeatStatusChanged;
use App\Models\Booking;
use App\Models\FoodItem;
use App\Models\Payment;
use App\Models\SeatLock;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Turns a set of caller-held seats into a confirmed order, atomically.
 *
 * The whole confirm runs in ONE DB transaction (invariant 6): booking flips
 * pending -> confirmed, booking_seats are inserted (priced by lookup(tier,
 * seat.type)), the holds are deleted, and a stub payment is attached — all or
 * nothing. Totals are computed server-side in integer minor units (RM); never
 * float. The client's totals are advisory only.
 */
class BookingService
{
    /** Service charge rate (demo): 5% of (subtotal + food_total). */
    private const SERVICE_RATE_BPS = 500; // basis points

    /** Promo codes -> discount as basis points of subtotal (demo). */
    private const PROMOS = [
        'WELCOME10' => 1000, // 10%
    ];

    public function __construct(private readonly SeatMapService $seatMap) {}

    /** Relations BookingResource needs, for list + detail reads. */
    private const WITH = ['showtime.movie', 'showtime.hall.cinema', 'seats.seat', 'foodItems.foodItem', 'payment'];

    /** All of a user's bookings, newest first. */
    public function listForUser(User $user): Collection
    {
        return Booking::query()
            ->where('user_id', $user->id)
            ->with(self::WITH)
            ->latest()
            ->get();
    }

    /** Load a single booking's relations for BookingResource. */
    public function loadDetail(Booking $booking): Booking
    {
        return $booking->load(self::WITH);
    }

    /**
     * Confirm a booking for the caller.
     *
     * @param  array{showtime_id:int, seat_codes:array<int,string>, food?:array<int,array{food_item_id:int,qty:int}>, promo_code?:string|null, payment_method:string}  $data
     *
     * @throws HttpResponseException 409 when a seat is no longer held by the caller
     */
    public function confirm(User $user, array $data): Booking
    {
        $showtime = Showtime::with(['movie', 'hall.cinema', 'tier.seatTypePrices'])
            ->findOrFail($data['showtime_id']);

        // Price lookup for this showtime's tier: seat_type => int minor units.
        $priceByType = $showtime->tier->seatTypePrices
            ->mapWithKeys(fn ($p) => [$p->seat_type->value => (int) $p->price]);
        $currency = $showtime->tier->currency;

        $booking = DB::transaction(function () use ($user, $data, $showtime, $priceByType, $currency) {
            // 1) Validate every requested seat is STILL held by the caller, and
            //    lock those hold rows FOR UPDATE so a concurrent confirm/expiry
            //    can't pull the rug mid-transaction.
            $seatRows = [];   // [ ['seat' => Seat, 'unit_price' => int], ... ]
            $lockIds = [];

            foreach ($data['seat_codes'] as $seatCode) {
                $seat = $showtime->hall->seats()
                    ->where('seat_code', $seatCode)
                    ->where('active', true)
                    ->first();

                $lock = $seat
                    ? SeatLock::query()
                        ->where('showtime_id', $showtime->id)
                        ->where('seat_id', $seat->id)
                        ->where('holder_id', $user->id)
                        ->where('expires_at', '>', Carbon::now())
                        ->lockForUpdate()
                        ->first()
                    : null;

                if (! $seat || ! $lock) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'One or more seats are no longer available.',
                        'errors' => ['seat_codes' => ["{$seatCode} is no longer held by you."]],
                    ], 409));
                }

                $seatRows[] = [
                    'seat' => $seat,
                    'unit_price' => (int) ($priceByType[$seat->type->value] ?? 0),
                ];
                $lockIds[] = $lock->id;
            }

            // 2) Resolve F&B lines (effective price = discount_price ?? price).
            $foodRows = $this->resolveFood($data['food'] ?? []);

            // 3) Totals — integer minor units only, no float on money.
            $subtotal = array_sum(array_column($seatRows, 'unit_price'));
            $foodTotal = array_sum(array_column($foodRows, 'line_total'));
            $serviceCharge = intdiv(($subtotal + $foodTotal) * self::SERVICE_RATE_BPS, 10000);
            $discount = $this->discountFor($data['promo_code'] ?? null, $subtotal);
            $total = $subtotal + $foodTotal + $serviceCharge - $discount;

            // 4) Order header (pending -> confirmed).
            $booking = Booking::create([
                'user_id' => $user->id,
                'showtime_id' => $showtime->id,
                'reference' => 'TMP', // replaced with id-derived reference below
                'status' => BookingStatus::Confirmed,
                'subtotal' => $subtotal,
                'service_charge' => $serviceCharge,
                'food_total' => $foodTotal,
                'discount' => $discount,
                'total' => $total,
                'currency' => $currency,
                'promo_code' => $data['promo_code'] ?? null,
            ]);
            $booking->reference = sprintf('SDP-%d-%06d', Carbon::now()->year, $booking->id);
            $booking->save();

            // 5) Sold seats — the permanent source of truth (UNIQUE(showtime, seat)).
            foreach ($seatRows as $row) {
                $booking->seats()->create([
                    'showtime_id' => $showtime->id,
                    'seat_id' => $row['seat']->id,
                    'unit_price' => $row['unit_price'],
                ]);
            }

            // 6) F&B line items.
            foreach ($foodRows as $row) {
                $booking->foodItems()->create([
                    'food_item_id' => $row['food_item_id'],
                    'qty' => $row['qty'],
                    'unit_price' => $row['unit_price'],
                ]);
            }

            // 7) Release the holds — they have served their purpose; the seats are
            //    now sold (booking_seats), so the holds must go inside the same TX.
            SeatLock::whereIn('id', $lockIds)->delete();

            // 8) Stub payment (no gateway) — one row per booking, marked paid.
            Payment::create([
                'booking_id' => $booking->id,
                'method' => PaymentMethod::from($data['payment_method']),
                'amount' => $total,
                'currency' => $currency,
                'status' => PaymentStatus::Paid,
                'reference' => sprintf('PAY-%06d', $booking->id),
            ]);

            return $booking;
        });

        // Broadcast BOOKED for each seat AFTER the transaction commits, so the
        // socket never announces a state the DB hasn't durably reached.
        foreach ($booking->seats as $bookingSeat) {
            $bookingSeat->loadMissing('seat');
            SeatStatusChanged::announce($showtime->id, $bookingSeat->seat->seat_code, SeatStatus::Booked);
        }

        return $booking->load(['showtime.movie', 'showtime.hall.cinema', 'seats.seat', 'foodItems.foodItem', 'payment']);
    }

    /**
     * Resolve food request lines to priced rows.
     *
     * @param  array<int,array{food_item_id:int,qty:int}>  $food
     * @return array<int,array{food_item_id:int, name:string, qty:int, unit_price:int, line_total:int}>
     */
    private function resolveFood(array $food): array
    {
        if (empty($food)) {
            return [];
        }

        $items = FoodItem::whereIn('id', array_column($food, 'food_item_id'))->get()->keyBy('id');
        $rows = [];

        foreach ($food as $line) {
            $item = $items->get($line['food_item_id']);
            if (! $item) {
                continue; // unknown ids are rejected by validation; defensive skip
            }
            $qty = (int) $line['qty'];
            $unitPrice = (int) ($item->discount_price ?? $item->price);

            $rows[] = [
                'food_item_id' => $item->id,
                'name' => $item->name,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $qty,
            ];
        }

        return $rows;
    }

    /**
     * Resolve a promo code to a discount in minor units (off the subtotal).
     */
    private function discountFor(?string $promoCode, int $subtotal): int
    {
        if (! $promoCode) {
            return 0;
        }

        $bps = self::PROMOS[strtoupper($promoCode)] ?? 0;

        return intdiv($subtotal * $bps, 10000);
    }
}
