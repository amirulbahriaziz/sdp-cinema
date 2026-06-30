<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Services\MovieService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MovieController extends Controller
{
    public function __construct(private readonly MovieService $movies) {}

    /**
     * List movies.
     *
     * Returns the movie catalog for the Home screen.
     *
     * @group Movies
     */
    public function index(): AnonymousResourceCollection
    {
        return MovieResource::collection($this->movies->list());
    }

    /**
     * Get a movie.
     *
     * Returns one movie with its casts and reviews (newest first) plus review aggregates.
     *
     * @group Movies
     *
     * @urlParam id integer required The movie id. Example: 1
     */
    public function show(int $id): MovieResource
    {
        return new MovieResource($this->movies->find($id));
    }
}
