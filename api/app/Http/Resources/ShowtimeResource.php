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
            'tier' => new PriceTierResource($this->whenLoaded('tier')),
        ];
    }
}
