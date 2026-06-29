<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The ONLY source of truth for *sold* seats. showtime_id is denormalised
        // onto the row so the sold-once invariant can be a DB unique constraint.
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('showtime_id')->constrained('showtimes')->restrictOnDelete();
            $table->foreignId('seat_id')->constrained('seats')->restrictOnDelete();
            $table->unsignedInteger('unit_price');        // resolved per-seat price, minor units
            $table->timestamps();

            // Invariant 3: a seat is sold at most once per showtime.
            $table->unique(['showtime_id', 'seat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
