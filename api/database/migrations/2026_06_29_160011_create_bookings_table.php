<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('showtime_id')->constrained('showtimes')->restrictOnDelete();
            $table->string('reference')->unique();        // SDP-2026-000501
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            // All amounts are integer minor units; currency stored alongside.
            $table->unsignedInteger('subtotal')->default(0);        // Σ seat unit_price
            $table->unsignedInteger('service_charge')->default(0);
            $table->unsignedInteger('food_total')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->string('currency', 3)->default('RM');
            $table->string('promo_code')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
