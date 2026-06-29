<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Transient FCFS holds only — never the source of truth for *sold* seats.
        // The UNIQUE(showtime_id, seat_id) constraint IS the atomic lock (no Redis):
        // acquire = INSERT; first writer wins, the loser's INSERT fails -> 409.
        Schema::create('seat_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showtime_id')->constrained('showtimes')->cascadeOnDelete();
            $table->foreignId('seat_id')->constrained('seats')->cascadeOnDelete();
            $table->foreignId('holder_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('expires_at');               // TTL ~5 min
            $table->timestamps();

            // Invariant 2: a seat is held by at most one user per showtime.
            $table->unique(['showtime_id', 'seat_id']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_locks');
    }
};
