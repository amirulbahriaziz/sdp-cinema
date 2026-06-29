<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dummy/stub payment — one per booking (1-1).
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->enum('method', ['card', 'bank', 'crypto']);
            $table->unsignedInteger('amount');            // minor units
            $table->string('currency', 3)->default('RM');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('reference')->nullable();      // PAY-000501
            $table->timestamps();

            $table->unique('booking_id');                 // 1-1 booking : payment
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
