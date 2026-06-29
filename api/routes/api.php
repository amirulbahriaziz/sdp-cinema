<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SeatLockController;
use App\Http\Controllers\Api\ShowtimeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
 * Public read endpoints (Step 2).
 * route -> controller -> service -> model; consistent { data } JSON; money int minor + RM.
 */
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show'])->whereNumber('id');

Route::get('/cinemas', [CinemaController::class, 'index']);

Route::get('/showtimes', [ShowtimeController::class, 'index']);
Route::get('/showtimes/{showtime}/seats', [ShowtimeController::class, 'seats'])->whereNumber('showtime');

Route::get('/food-items', [FoodItemController::class, 'index']);

/*
 * The graded core (Step 3): FCFS seat-lock + atomic booking. Bearer-authenticated.
 * Atomicity is a DB invariant (UNIQUE(showtime_id, seat_id) on seat_locks), not Redis.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/showtimes/{showtime}/seats/{seatCode}/lock', [SeatLockController::class, 'store'])
        ->whereNumber('showtime');
    Route::delete('/showtimes/{showtime}/seats/{seatCode}/lock', [SeatLockController::class, 'destroy'])
        ->whereNumber('showtime');

    Route::post('/bookings', [BookingController::class, 'store']);
});
