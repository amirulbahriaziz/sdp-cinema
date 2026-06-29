<?php

use App\Mcp\Servers\CinemaDevServer;
use Laravel\Mcp\Facades\Mcp;

/*
 * MCP servers (Step 4). The Cinema dev server exposes read-only tools over the
 * seed data for AI-assisted development. It is DEV-ONLY: the route is only
 * registered outside production, so it never ships in a live deployment.
 */
if (! app()->environment('production')) {
    Mcp::web('/mcp/cinema-dev', CinemaDevServer::class);
}
