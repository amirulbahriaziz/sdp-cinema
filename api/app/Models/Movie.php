<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'synopsis', 'duration_min', 'release_date', 'age_rating',
        'imdb_rating', 'poster_url', 'trailer_url', 'genres', 'casts',
        'director', 'writers', 'sections',
    ];

    protected $casts = [
        'release_date' => 'date',
        'imdb_rating' => 'float',
        'duration_min' => 'integer',
        'genres' => 'array',
        'casts' => 'array',
        'writers' => 'array',
        'sections' => 'array',
    ];

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MovieReview::class);
    }
}
