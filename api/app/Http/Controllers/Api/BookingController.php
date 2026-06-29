<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

/**
 * Booking confirmation. Thin controller — the atomic confirm transaction lives
 * in BookingService; validation/authorization live in StoreBookingRequest.
 */
class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookings) {}

    /**
     * Confirm a booking (atomic).
     *
     * Validates every seat is still held by the caller, inserts booking_seats +
     * booking_food_items, deletes the holds, attaches a stub payment, and
     * computes totals server-side (integer minor units, RM) — all in one DB
     * transaction. Broadcasts `booked` per seat on the showtime channel.
     *
     * @group Bookings
     *
     * @response 201 scenario="confirmed" {"data":{"id":501,"reference":"SDP-2026-000501","status":"confirmed","total":8565}}
     * @response 409 {"message":"One or more seats are no longer available.","errors":{"seat_codes":["D5 is no longer held by you."]}}
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = $this->bookings->confirm($request->user(), $request->validated());

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }
}
