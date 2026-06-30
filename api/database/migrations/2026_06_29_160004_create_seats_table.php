<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->string('seat_code');                 // "A3"
            $table->string('row_label');                 // "A"
            $table->unsignedSmallInteger('col_num');      // 3
            $table->enum('type', ['standard', 'premium'])->default('standard');
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Invariant: a seat_code is unique within a hall.
            $table->unique(['hall_id', 'seat_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
