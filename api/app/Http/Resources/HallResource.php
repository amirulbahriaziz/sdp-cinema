<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cinema_id' => $this->cinema_id,
            'name' => $this->name,
            'cinema' => new CinemaResource($this->whenLoaded('cinema')),
        ];
    }
}
