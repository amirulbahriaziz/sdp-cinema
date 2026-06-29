<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SeatLockController;
use App\Http\Controllers\Api\ShowtimeController;
use Illuminate\Support\Facades\Route;

/*
 * Auth (Step 4). Stateless Sanctum bearer tokens — register/login mint a token,
 * logout revokes the one used on the request. Validation + authorization live in
 * the Form Requests; credential logic in AuthService.
 */
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

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
