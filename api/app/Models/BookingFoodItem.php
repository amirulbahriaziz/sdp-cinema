<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingFoodItem extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'food_item_id', 'qty', 'unit_price'];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class);
    }
}
