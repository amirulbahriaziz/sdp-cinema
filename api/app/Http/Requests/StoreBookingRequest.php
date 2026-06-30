<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation + authorization for POST /bookings. Per code-standards these live
 * ONLY in the Form Request — the controller receives an already-validated,
 * already-authorized request. (Authentication itself is the `auth:sanctum`
 * route middleware; the runtime "seat still held by you" check is concurrency
 * state, resolved in BookingService -> 409, not validation.)
 */
class StoreBookingRequest extends FormRequest
{
    /**
     * Any authenticated caller may attempt to book; ownership of the seats is
     * enforced at confirm time against their own holds.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'seat_codes' => ['required', 'array', 'min:1'],
            'seat_codes.*' => ['required', 'string', 'distinct'],
            'food' => ['sometimes', 'array'],
            'food.*.food_item_id' => ['required_with:food', 'integer', 'exists:food_items,id'],
            'food.*.qty' => ['required_with:food', 'integer', 'min:1', 'max:99'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }
}
