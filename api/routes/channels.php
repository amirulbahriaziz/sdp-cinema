<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
 * Per-showtime seat channel. Public: any subscriber may watch a showtime's live
 * seat-state stream (held / available / booked). Carries no per-user secrets —
 * the SeatStatusChanged payload is just { showtime_id, seat_code, status }.
 */
Broadcast::channel('showtime.{showtimeId}', fn () => true);
