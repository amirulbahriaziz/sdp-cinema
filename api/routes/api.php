<?php

use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\MovieController;
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
