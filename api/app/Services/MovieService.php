<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MovieService
{
    /**
     * Catalog list for Home. Lightweight — no reviews eager-loaded.
     */
    public function list(): Collection
    {
        return Movie::query()
            ->orderByDesc('release_date')
            ->get();
    }

    /**
     * Movie detail with reviews (newest first) and review aggregates.
     * Casts are a column on the movie row, returned by the resource.
     */
    public function find(int $id): Movie
    {
        return Movie::query()
            ->withCount('reviews')
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->with(['reviews' => function ($q) {
                $q->with('user:id,name')->latest();
            }])
            ->findOrFail($id);
    }
}
