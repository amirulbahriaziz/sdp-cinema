/**
 * Laravel Echo client (Reverb protocol) for the app.
 *
 * Reverb speaks the Pusher protocol, so Echo drives it through `pusher-js`. We expose a
 * single lazily-created Echo instance — every `useSeatChannel` subscribes to the same
 * connection. `pusher-js` reads its WebSocket impl from the global, which React Native
 * provides, so it works on device/simulator as-is (no browser shim needed).
 *
 * Connection params mirror `api/.env` REVERB_* (see `config.reverb`). Locally Reverb runs
 * plain `ws` (forceTLS=false); for a hosted deploy flip the scheme to `https` and it upgrades.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

import { config } from '../config';

// pusher-js looks for a global `Pusher`; Echo's reverb broadcaster instantiates it.
(globalThis as { Pusher?: typeof Pusher }).Pusher = Pusher;

let echo: Echo<'reverb'> | null = null;

/** The shared Echo instance (created on first use). */
export function getEcho(): Echo<'reverb'> {
  if (echo) return echo;
  echo = new Echo({
    broadcaster: 'reverb',
    key: config.reverb.key,
    wsHost: config.reverb.host,
    wsPort: config.reverb.port,
    wssPort: config.reverb.port,
    forceTLS: config.reverb.scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    // No private channels here — the per-showtime seat stream is public — so no auth endpoint.
  });
  return echo;
}

/** The raw pusher connection (for binding state_change / reading `state`). */
export function getConnection(): {
  state: string;
  bind(event: string, cb: () => void): void;
  unbind(event: string, cb: () => void): void;
} | null {
  const connector = getEcho().connector as { pusher?: { connection?: any } };
  return connector.pusher?.connection ?? null;
}
