<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\ListShowtimesTool;
use App\Mcp\Tools\SeatMapTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

/**
 * Dev-only MCP server exposing read tools over the cinema's seed data so an AI
 * assistant can inspect showtimes and live seat maps while building the app.
 * Read-only by design — no booking or seat-lock mutations are exposed here.
 * Registration is guarded to the local environment in routes/ai.php.
 */
#[Name('SDP Cinema Dev')]
#[Version('0.1.0')]
#[Instructions('Read-only tools over the SDP Cinema database for AI-assisted development. Use list-showtimes to discover screenings, then seat-map to inspect per-seat availability and pricing for a showtime. No write operations are available.')]
class CinemaDevServer extends Server
{
    protected array $tools = [
        ListShowtimesTool::class,
        SeatMapTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
