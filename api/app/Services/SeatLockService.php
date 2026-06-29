<?php

namespace App\Services;

use App\Events\SeatStatusChanged;
use App\Models\BookingSeat;
use App\Models\Seat;
use App\Models\SeatLock;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Carbon;

/**
 * The FCFS seat-lock core. There is NO Redis: the atomic hold is a plain
 * `INSERT` into `seat_locks` guarded by `UNIQUE(showtime_id, seat_id)`. The
 * first writer wins; a concurrent second writer's INSERT violates the unique
 * index and is mapped to a 409. This is the single place that owns hold logic
 * (code-standards: root-cause, one service — never check-then-write per caller).
 */
class SeatLockService
{
    /** Hold time-to-live in seconds (~5 minutes). */
    public const TTL_SECONDS = 300;

    /**
     * Acquire an FCFS hold on one seat for a showtime.
     *
     * Atomicity: we never "check if free then insert" (that races). We let the
     * DB's UNIQUE(showtime_id, seat_id) be the arbiter — INSERT, and on a unique
     * violation the loser gets 409. Expired holds are pruned first so a stale TTL
     * never blocks a seat; a booked seat is rejected up front (booked is permanent).
     *
     * @throws HttpResponseException 404 unknown seat, 409 already held/booked
     */
    public function acquire(Showtime $showtime, string $seatCode, User $holder): SeatLock
    {
        $seat = $this->resolveSeat($showtime, $seatCode);

        // Booked is permanent (booking_seats is the source of truth) — reject early.
        $alreadyBooked = BookingSeat::query()
            ->where('showtime_id', $showtime->id)
            ->where('seat_id', $seat->id)
            ->exists();

        if ($alreadyBooked) {
            $this->throwConflict();
        }

        // Free any expired hold on this exact seat so a stale TTL can't block it.
        SeatLock::query()
            ->where('showtime_id', $showtime->id)
            ->where('seat_id', $seat->id)
            ->where('expires_at', '<=', Carbon::now())
            ->delete();

        try {
            // The atomic step: UNIQUE(showtime_id, seat_id) makes this FCFS.
            $lock = SeatLock::create([
                'showtime_id' => $showtime->id,
                'seat_id' => $seat->id,
                'holder_id' => $holder->id,
                'expires_at' => Carbon::now()->addSeconds(self::TTL_SECONDS),
            ]);
        } catch (UniqueConstraintViolationException) {
            // An active hold by someone else already exists — loser of the race.
            $this->throwConflict();
        }

        $lock->setRelation('seat', $seat);

        SeatStatusChanged::dispatch($showtime->id, $seat->seat_code, 'held');

        return $lock;
    }

    /**
     * Release a hold owned by the caller.
     *
     * Only the holder may release (otherwise the TTL expires it). Releasing a
     * seat nobody holds is a 404; releasing someone else's hold is a 403.
     *
     * @throws HttpResponseException 404 no active lock, 403 not the holder
     */
    public function release(Showtime $showtime, string $seatCode, User $holder): Seat
    {
        $seat = $this->resolveSeat($showtime, $seatCode);

        $lock = SeatLock::query()
            ->where('showtime_id', $showtime->id)
            ->where('seat_id', $seat->id)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $lock) {
            $this->throwNotFound('No active hold for this seat.');
        }

        if ((int) $lock->holder_id !== (int) $holder->id) {
            throw new HttpResponseException(response()->json([
                'message' => 'You do not hold this seat.',
            ], 403));
        }

        $lock->delete();

        SeatStatusChanged::dispatch($showtime->id, $seat->seat_code, 'available');

        return $seat;
    }

    /**
     * Delete every expired hold across all showtimes. Wired to a scheduled
     * command (`seatlocks:prune`); the seat-map read path also filters by
     * `expires_at`, so an abandoned hold can never block a seat indefinitely.
     */
    public function prune(): int
    {
        return SeatLock::query()
            ->where('expires_at', '<=', Carbon::now())
            ->delete();
    }

    /**
     * Resolve an active seat of the showtime's hall by its seat code.
     *
     * @throws HttpResponseException 404 when the code is unknown for this hall
     */
    private function resolveSeat(Showtime $showtime, string $seatCode): Seat
    {
        $seat = $showtime->hall->seats()
            ->where('seat_code', $seatCode)
            ->where('active', true)
            ->first();

        if (! $seat) {
            $this->throwNotFound('Seat not found for this showtime.');
        }

        return $seat;
    }

    /**
     * @throws HttpResponseException
     */
    private function throwConflict(): never
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Seat is no longer available.',
            'errors' => ['seat' => ['This seat is already held or booked.']],
        ], 409));
    }

    /**
     * @throws HttpResponseException
     */
    private function throwNotFound(string $message): never
    {
        throw new HttpResponseException(response()->json([
            'message' => $message,
        ], 404));
    }
}
