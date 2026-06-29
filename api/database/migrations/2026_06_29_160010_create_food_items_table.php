<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['combo', 'food_snacks', 'beverages']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price');             // minor units
            $table->unsignedInteger('discount_price')->nullable();
            $table->string('currency', 3)->default('RM');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
