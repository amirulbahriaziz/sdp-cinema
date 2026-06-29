<?php

namespace App\Services;

use App\Models\Showtime;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeService
{
    /**
     * List screenings, optionally filtered.
     *
     * @param  array{movie_id?:int,cinema_id?:int,hall_id?:int,tier_id?:int,date?:string,from?:string,to?:string}  $filters
     */
    public function list(array $filters = []): Collection
    {
        return Showtime::query()
            ->with(['movie', 'hall.cinema', 'tier.seatTypePrices'])
            ->when(
                isset($filters['movie_id']),
                fn ($q) => $q->where('movie_id', $filters['movie_id'])
            )
            ->when(
                isset($filters['hall_id']),
                fn ($q) => $q->where('hall_id', $filters['hall_id'])
            )
            ->when(
                isset($filters['tier_id']),
                fn ($q) => $q->where('tier_id', $filters['tier_id'])
            )
            ->when(
                isset($filters['cinema_id']),
                fn ($q) => $q->whereHas('hall', fn ($h) => $h->where('cinema_id', $filters['cinema_id']))
            )
            ->when(
                isset($filters['date']),
                fn ($q) => $q->whereDate('starts_at', $filters['date'])
            )
            ->when(
                isset($filters['from']),
                fn ($q) => $q->where('starts_at', '>=', $filters['from'])
            )
            ->when(
                isset($filters['to']),
                fn ($q) => $q->where('starts_at', '<=', $filters['to'])
            )
            ->orderBy('starts_at')
            ->get();
    }
}
