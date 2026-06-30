<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowtimeIndexRequest;
use App\Http\Resources\ShowtimeResource;
use App\Models\Showtime;
use App\Services\SeatMapService;
use App\Services\ShowtimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShowtimeController extends Controller
{
    public function __construct(
        private readonly ShowtimeService $showtimes,
        private readonly SeatMapService $seatMap,
    ) {}

    /**
     * List showtimes.
     *
     * Returns screenings, optionally filtered by movie, cinema, hall, tier or date.
     *
     * @group Showtimes
     *
     * @queryParam movie_id integer Filter by movie. Example: 1
     * @queryParam cinema_id integer Filter by cinema. Example: 1
     * @queryParam hall_id integer Filter by hall. Example: 1
     * @queryParam tier_id integer Filter by price tier. Example: 1
     * @queryParam date string Filter by calendar day (Y-m-d). Example: 2026-06-29
     */
    public function index(ShowtimeIndexRequest $request): AnonymousResourceCollection
    {
        return ShowtimeResource::collection(
            $this->showtimes->list($request->validated())
        );
    }

    /**
     * Get the seat map for a showtime.
     *
     * Derives per-seat status (available | held | booked) for this showtime and resolves
     * each seat's price via lookup(tier, seat.type). Money is integer minor units in RM.
     *
     * @group Showtimes
     *
     * @urlParam id integer required The showtime id. Example: 1
     */
    public function seats(Showtime $showtime): JsonResponse
    {
        return response()->json(['data' => $this->seatMap->forShowtime($showtime)]);
    }
}
