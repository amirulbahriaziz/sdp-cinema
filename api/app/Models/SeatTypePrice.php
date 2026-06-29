<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatTypePrice extends Model
{
    use HasFactory;

    protected $fillable = ['tier_id', 'seat_type', 'price'];

    protected $casts = [
        'price' => 'integer',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(PriceTier::class, 'tier_id');
    }
}
