<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired on every seat-state transition for a showtime (lock / release / confirm).
 *
 * Invariant 4: every seat-state change broadcasts a real-time event on the
 * showtime's channel. Controllers never push sockets directly — services raise
 * this domain event and the broadcaster (Reverb) fans it out to subscribers.
 *
 * Channel: a public per-showtime channel `showtime.{id}`; the Expo client
 * subscribes via laravel-echo + pusher-js (Reverb protocol) and patches its
 * React Query seat-map cache.
 */
class SeatStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  string  $status  available | held | booked (shared seat-status vocabulary)
     */
    public function __construct(
        public int $showtimeId,
        public string $seatCode,
        public string $status,
    ) {}

    /**
     * The per-showtime channel every seat event rides on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel("showtime.{$this->showtimeId}");
    }

    /**
     * Stable event name the client listens for.
     */
    public function broadcastAs(): string
    {
        return 'seat.status.changed';
    }

    /**
     * The minimal payload the client needs to patch one seat in its cache.
     *
     * @return array{showtime_id:int, seat_code:string, status:string}
     */
    public function broadcastWith(): array
    {
        return [
            'showtime_id' => $this->showtimeId,
            'seat_code' => $this->seatCode,
            'status' => $this->status,
        ];
    }
}
