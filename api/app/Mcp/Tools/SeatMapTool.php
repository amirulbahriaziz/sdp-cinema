<?php

namespace App\Mcp\Tools;

use App\Models\Showtime;
use App\Services\SeatMapService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

/**
 * Read-only AI-dev tool: derive the live seat map for one showtime — per-seat
 * status (available | held | booked) and price (int minor units, RM). Shares
 * SeatMapService with the HTTP API so the AI sees exactly what the app sees.
 */
#[Description('Get the seat map for a showtime: each seat with its status (available|held|booked) and price in RM minor units. Requires showtime_id.')]
class SeatMapTool extends Tool
{
    public function __construct(private readonly SeatMapService $seatMap) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
        ]);

        $showtime = Showtime::findOrFail($validated['showtime_id']);

        return Response::json($this->seatMap->forShowtime($showtime));
    }

    /**
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'showtime_id' => $schema->integer()
                ->description('The showtime id to map seats for.')
                ->required(),
        ];
    }
}
