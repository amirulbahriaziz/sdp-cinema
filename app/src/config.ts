/**
 * Runtime config sourced from Expo public env (`.env`, EXPO_PUBLIC_* are inlined at build).
 * The data layer reads `DATA_SOURCE` to pick the `live` (HTTP -> Laravel) or `mock`
 * (bundled mock/*.json) adapter; on a live failure it falls back to `mock`.
 */
export type DataSource = 'live' | 'mock';

const rawSource = process.env.EXPO_PUBLIC_DATA_SOURCE;

export const config = {
  apiUrl: process.env.EXPO_PUBLIC_API_URL ?? 'http://127.0.0.1:8000',
  dataSource: (rawSource === 'live' ? 'live' : 'mock') as DataSource,
} as const;
