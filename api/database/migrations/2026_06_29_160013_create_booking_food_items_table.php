<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('food_item_id')->constrained('food_items')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedInteger('unit_price');        // effective price at purchase, minor units
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_food_items');
    }
};
