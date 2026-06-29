<?php

namespace App\Services;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Collection;

class CinemaService
{
    public function list(): Collection
    {
        return Cinema::query()
            ->with('halls')
            ->orderBy('name')
            ->get();
    }
}
