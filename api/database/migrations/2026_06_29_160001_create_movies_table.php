<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('synopsis')->nullable();
            $table->unsignedSmallInteger('duration_min');
            $table->date('release_date')->nullable();
            $table->string('age_rating')->nullable();           // U, P13, 18, ...
            $table->decimal('imdb_rating', 3, 1)->nullable();    // 0.0 - 10.0
            $table->string('poster_url')->nullable();
            $table->string('trailer_url')->nullable();
            $table->json('genres')->nullable();                  // ["Action", ...]
            $table->json('casts')->nullable();
            $table->string('director')->nullable();
            $table->json('writers')->nullable();
            $table->json('sections')->nullable();                // ["new_releases","popular",...]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
