/**
 * Runtime config sourced from Expo public env (`.env`, EXPO_PUBLIC_* are inlined at build).
 * The data layer reads `dataSource` to pick the `live` (HTTP -> Laravel) or `mock`
 * (bundled mock/*.json) adapter; on a live failure it falls back to `mock`.
 *
 * `reverb` configures the laravel-echo + pusher-js client for the per-showtime seat
 * channel; `devAuth` holds the demo credentials the app auto-logs-in with under `live`
 * (lock/release/booking are Sanctum-gated, and there is no login screen in this build).
 */
export type DataSource = 'live' | 'mock';

const rawSource = process.env.EXPO_PUBLIC_DATA_SOURCE;

export const config = {
  apiUrl: process.env.EXPO_PUBLIC_API_URL ?? 'http://127.0.0.1:8000',
  dataSource: (rawSource === 'live' ? 'live' : 'mock') as DataSource,
  reverb: {
    key: process.env.EXPO_PUBLIC_REVERB_KEY ?? '',
    host: process.env.EXPO_PUBLIC_REVERB_HOST ?? '127.0.0.1',
    port: Number(process.env.EXPO_PUBLIC_REVERB_PORT ?? 8080),
    scheme: process.env.EXPO_PUBLIC_REVERB_SCHEME ?? 'http',
  },
  devAuth: {
    email: process.env.EXPO_PUBLIC_DEMO_EMAIL ?? 'demo@sdpcinema.test',
    password: process.env.EXPO_PUBLIC_DEMO_PASSWORD ?? 'password',
  },
} as const;
