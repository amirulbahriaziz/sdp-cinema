<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Services\SeatLockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FCFS seat holds. Thin controller — all hold logic lives in SeatLockService;
 * authentication is the `auth:sanctum` route middleware.
 */
class SeatLockController extends Controller
{
    public function __construct(private readonly SeatLockService $locks) {}

    /**
     * Acquire a seat hold.
     *
     * Atomic FCFS hold guarded by UNIQUE(showtime_id, seat_id): first writer
     * wins, the loser gets 409. TTL ~5 min. Broadcasts `held` on the showtime
     * channel.
     *
     * @group Seat locks
     *
     * @urlParam showtime integer required The showtime id. Example: 1
     * @urlParam seatCode string required The seat code. Example: D4
     *
     * @response 201 {"data":{"showtime_id":1,"seat_code":"D4","status":"held","holder":"you","expires_at":"2026-07-02T19:05:00+08:00","ttl_seconds":300}}
     * @response 409 {"message":"Seat is no longer available.","errors":{"seat":["This seat is already held or booked."]}}
     */
    public function store(Showtime $showtime, string $seatCode, Request $request): JsonResponse
    {
        $lock = $this->locks->acquire($showtime, $seatCode, $request->user());

        return response()->json([
            'data' => [
                'showtime_id' => $showtime->id,
                'seat_code' => $lock->seat->seat_code,
                'status' => 'held',
                'holder' => 'you',
                'expires_at' => $lock->expires_at->toIso8601String(),
                'ttl_seconds' => SeatLockService::TTL_SECONDS,
            ],
        ], 201);
    }

    /**
     * Release a seat hold owned by the caller.
     *
     * Only the holder may release; otherwise the hold expires on its TTL.
     * Broadcasts `available` on the showtime channel.
     *
     * @group Seat locks
     *
     * @urlParam showtime integer required The showtime id. Example: 1
     * @urlParam seatCode string required The seat code. Example: D4
     *
     * @response 200 {"data":{"showtime_id":1,"seat_code":"D4","status":"available"}}
     * @response 403 {"message":"You do not hold this seat."}
     */
    public function destroy(Showtime $showtime, string $seatCode, Request $request): JsonResponse
    {
        $seat = $this->locks->release($showtime, $seatCode, $request->user());

        return response()->json([
            'data' => [
                'showtime_id' => $showtime->id,
                'seat_code' => $seat->seat_code,
                'status' => 'available',
            ],
        ]);
    }
}
