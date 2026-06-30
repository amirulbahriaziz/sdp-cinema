<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceTier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'currency'];

    public function seatTypePrices(): HasMany
    {
        return $this->hasMany(SeatTypePrice::class, 'tier_id');
    }

    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class, 'tier_id');
    }
}
