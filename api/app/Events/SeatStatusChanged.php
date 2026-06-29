<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
 *
 * Broadcasts NOW (synchronously, in the request) rather than via the queue: the
 * local stack runs no queue worker (no Redis, DB-backed queue), so an inline
 * broadcast guarantees seat events reach Reverb the instant a lock/release/booking
 * commits — without a separate `queue:work` process to keep alive for the demo.
 */
class SeatStatusChanged implements ShouldBroadcastNow
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
     * Best-effort realtime announce. The seat-state transition has ALREADY been
     * durably committed to the DB (the source of truth) by the time we get here;
     * the broadcast is a side-effect. Because we broadcast NOW (inline, no queue
     * worker), a Reverb outage would otherwise bubble a BroadcastException up and
     * 500 a request whose DB write already succeeded — desyncing the client from
     * a hold/booking that really happened. So we swallow + log broadcast failures:
     * realtime degrades to the client's polling fallback, the API stays correct.
     */
    public static function announce(int $showtimeId, string $seatCode, string $status): void
    {
        try {
            self::dispatch($showtimeId, $seatCode, $status);
        } catch (Throwable $e) {
            Log::warning('SeatStatusChanged broadcast failed (realtime degraded to polling).', [
                'showtime_id' => $showtimeId,
                'seat_code' => $seatCode,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
        }
    }

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
