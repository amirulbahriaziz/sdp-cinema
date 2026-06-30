<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceTierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Card range = MIN..MAX of this tier's seat-type prices (int minor units).
        $prices = $this->whenLoaded('seatTypePrices', fn () => $this->seatTypePrices->pluck('price'));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'currency' => $this->currency,
            'price_min' => $this->when(
                $this->relationLoaded('seatTypePrices') && $prices->isNotEmpty(),
                fn () => (int) $prices->min()
            ),
            'price_max' => $this->when(
                $this->relationLoaded('seatTypePrices') && $prices->isNotEmpty(),
                fn () => (int) $prices->max()
            ),
            'seat_type_prices' => $this->whenLoaded(
                'seatTypePrices',
                fn () => $this->seatTypePrices->map(fn ($p) => [
                    'seat_type' => $p->seat_type,
                    'price' => (int) $p->price,
                ])->values()
            ),
        ];
    }
}
