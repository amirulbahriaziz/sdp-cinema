<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CinemaResource;
use App\Services\CinemaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CinemaController extends Controller
{
    public function __construct(private readonly CinemaService $cinemas) {}

    /**
     * List cinemas.
     *
     * Returns cinemas with their halls.
     *
     * @group Cinemas
     */
    public function index(): AnonymousResourceCollection
    {
        return CinemaResource::collection($this->cinemas->list());
    }
}
