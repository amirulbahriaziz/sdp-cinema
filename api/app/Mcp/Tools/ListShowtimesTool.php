<?php

namespace App\Mcp\Tools;

use App\Services\ShowtimeService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

/**
 * Read-only AI-dev tool: list cinema showtimes (optionally filtered by movie or
 * cinema) so an assistant can reason about the seed data without hand-writing
 * SQL. Delegates to the same ShowtimeService the HTTP API uses.
 */
#[Description('List cinema showtimes, optionally filtered by movie_id or cinema_id. Returns id, movie title, hall, cinema and start time.')]
class ListShowtimesTool extends Tool
{
    public function __construct(private readonly ShowtimeService $showtimes) {}

    public function handle(Request $request): Response
    {
        $filters = array_filter([
            'movie_id' => $request->get('movie_id'),
            'cinema_id' => $request->get('cinema_id'),
        ], fn ($v) => $v !== null);

        $showtimes = $this->showtimes->list($filters)->map(fn ($s) => [
            'id' => $s->id,
            'movie' => $s->movie?->title,
            'hall' => $s->hall?->name,
            'cinema' => $s->hall?->cinema?->name,
            'tier_id' => $s->tier_id,
            'starts_at' => $s->starts_at?->toIso8601String(),
        ])->all();

        return Response::json([
            'count' => count($showtimes),
            'showtimes' => $showtimes,
        ]);
    }

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'movie_id' => $schema->integer()
                ->description('Filter to a single movie id.'),
            'cinema_id' => $schema->integer()
                ->description('Filter to a single cinema id.'),
        ];
    }
}
