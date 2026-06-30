<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city', 'address'];

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }
}
