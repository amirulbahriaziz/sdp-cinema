<?php

namespace App\Services;

use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Collection;

class FoodItemService
{
    public function list(): Collection
    {
        return FoodItem::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }
}
