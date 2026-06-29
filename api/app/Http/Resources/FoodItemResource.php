<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (int) $this->price,
            'discount_price' => $this->discount_price !== null ? (int) $this->discount_price : null,
            'currency' => $this->currency,
            'image_url' => $this->image_url,
        ];
    }
}
