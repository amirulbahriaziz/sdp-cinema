<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'movie_id' => $this->movie_id,
            'hall_id' => $this->hall_id,
            'tier_id' => $this->tier_id,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'movie' => new MovieResource($this->whenLoaded('movie')),
            'hall' => new HallResource($this->whenLoaded('hall')),
            // Top-level cinema (sourced from the eager-loaded hall.cinema) to match the
            // API contract / app shape: showtimes nest cinema directly, not under hall.
            'cinema' => new CinemaResource($this->whenLoaded('hall', fn () => $this->hall->cinema)),
            // ponytail: per-row count (N+1 across a list) — fine for the demo's few showtimes;
            // switch to withCount on the service query if the showtime list grows large.
            'seats_available' => $this->when(
                $this->relationLoaded('hall'),
                fn () => $this->hall->seats()->where('active', true)->count()
                    - \App\Models\BookingSeat::where('showtime_id', $this->id)->count()
            ),
            'tier' => new PriceTierResource($this->whenLoaded('tier')),
        ];
    }
}
