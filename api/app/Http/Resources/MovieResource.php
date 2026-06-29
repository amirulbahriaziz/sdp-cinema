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
            // Rating summary + reviews are only present on the detail endpoint (reviews loaded).
            'rating_summary' => $this->when(
                $this->relationLoaded('reviews'),
                fn () => [
                    'average' => round((float) $this->reviews_avg_rating, 1),
                    'count' => (int) ($this->reviews_count ?? $this->reviews->count()),
                    'breakdown' => (object) collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($s) => [
                        (string) $s => $this->reviews->where('rating', $s)->count(),
                    ])->all(),
                ]
            ),
            'reviews' => MovieReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
