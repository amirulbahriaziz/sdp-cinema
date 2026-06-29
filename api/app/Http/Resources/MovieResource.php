<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'duration_min' => (int) $this->duration_min,
            'release_date' => $this->release_date?->toDateString(),
            'age_rating' => $this->age_rating,
            'imdb_rating' => $this->imdb_rating !== null ? (float) $this->imdb_rating : null,
            'poster_url' => $this->poster_url,
            'trailer_url' => $this->trailer_url,
            'genres' => $this->genres ?? [],
            'casts' => $this->casts ?? [],
            'director' => $this->director,
            'writers' => $this->writers ?? [],
            'sections' => $this->sections ?? [],
            // Aggregates + reviews are only present on the detail endpoint.
            'reviews_count' => $this->whenCounted('reviews'),
            'reviews_avg' => $this->when(
                $this->reviews_avg_rating !== null,
                fn () => round((float) $this->reviews_avg_rating, 1)
            ),
            'reviews' => MovieReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
