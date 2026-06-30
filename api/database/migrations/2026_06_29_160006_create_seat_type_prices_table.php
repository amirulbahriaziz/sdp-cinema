<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Option B pricing: price per (tier, seat type). A seat's price =
        // lookup(showtime.tier, seats.type). Stored as integer minor units.
        Schema::create('seat_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('price_tiers')->cascadeOnDelete();
            $table->enum('seat_type', ['standard', 'premium']);
            $table->unsignedInteger('price');             // minor units (cents)
            $table->timestamps();

            $table->unique(['tier_id', 'seat_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_type_prices');
    }
};
