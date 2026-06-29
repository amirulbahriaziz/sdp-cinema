<?php

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Confirmed-order shape returned by POST /bookings (matches CONTRACT §12).
 * All money fields are integer minor units in RM.
 *
 * @mixin Booking
 */
class BookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'status' => $this->status,
            'currency' => $this->currency,
            'showtime' => [
                'id' => $this->showtime->id,
                'starts_at' => $this->showtime->starts_at?->toIso8601String(),
                'movie' => [
                    'id' => $this->showtime->movie->id,
                    'title' => $this->showtime->movie->title,
                ],
                'cinema' => $this->showtime->hall->cinema ? [
                    'id' => $this->showtime->hall->cinema->id,
                    'name' => $this->showtime->hall->cinema->name,
                ] : null,
                'hall' => [
                    'id' => $this->showtime->hall->id,
                    'name' => $this->showtime->hall->name,
                ],
            ],
            'seats' => $this->seats->map(fn ($bs) => [
                'seat_code' => $bs->seat->seat_code,
                'type' => $bs->seat->type,
                'unit_price' => (int) $bs->unit_price,
            ])->values(),
            'food' => $this->foodItems->map(fn ($bf) => [
                'food_item_id' => $bf->food_item_id,
                'name' => $bf->foodItem->name,
                'qty' => (int) $bf->qty,
                'unit_price' => (int) $bf->unit_price,
                'line_total' => (int) $bf->unit_price * (int) $bf->qty,
            ])->values(),
            'subtotal' => (int) $this->subtotal,
            'food_total' => (int) $this->food_total,
            'service_charge' => (int) $this->service_charge,
            'promo_code' => $this->promo_code,
            'discount' => (int) $this->discount,
            'total' => (int) $this->total,
            'payment' => $this->payment ? [
                'method' => $this->payment->method,
                'amount' => (int) $this->payment->amount,
                'currency' => $this->payment->currency,
                'status' => $this->payment->status,
                'reference' => $this->payment->reference,
            ] : null,
        ];
    }
}
