<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category', 'name', 'description', 'price', 'discount_price', 'currency', 'image_url',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount_price' => 'integer',
    ];

    public function bookingFoodItems(): HasMany
    {
        return $this->hasMany(BookingFoodItem::class);
    }
}
