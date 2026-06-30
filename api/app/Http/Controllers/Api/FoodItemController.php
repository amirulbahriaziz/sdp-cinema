<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodItemResource;
use App\Services\FoodItemService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FoodItemController extends Controller
{
    public function __construct(private readonly FoodItemService $foodItems) {}

    /**
     * List food & beverage items.
     *
     * Returns the F&B catalog grouped-friendly by category.
     *
     * @group Food
     */
    public function index(): AnonymousResourceCollection
    {
        return FoodItemResource::collection($this->foodItems->list());
    }
}
