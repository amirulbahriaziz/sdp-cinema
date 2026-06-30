<?php

namespace App\Console\Commands;

use App\Services\SeatLockService;
use Illuminate\Console\Command;

/**
 * Frees expired seat holds (invariant 5: a hold may not block a seat forever).
 * The seat-map read path also filters by `expires_at`, so this is belt-and-
 * braces — it keeps the `seat_locks` table from accumulating dead rows.
 */
class PruneSeatLocks extends Command
{
    protected $signature = 'seatlocks:prune';

    protected $description = 'Delete expired seat holds (TTL cleanup).';

    public function handle(SeatLockService $locks): int
    {
        $deleted = $locks->prune();

        $this->info("Pruned {$deleted} expired seat lock(s).");

        return self::SUCCESS;
    }
}
