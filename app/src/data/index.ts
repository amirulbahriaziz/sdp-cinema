/**
 * The active data source. One interface, two interchangeable adapters:
 *
 *   - `mock`  -> bundled JSON, no network.
 *   - `live`  -> HTTP to Laravel, with **automatic fallback to `mock`** on any failure
 *                (API down/unreachable) so the app stays fully demoable offline.
 *
 * The source is selected at runtime from `config.dataSource` (EXPO_PUBLIC_DATA_SOURCE).
 * Screens and React Query hooks call `data` — they never know which adapter served them.
 */
import { config } from '../config';
import { liveAdapter, setAuthToken } from './live';
import { mockAdapter } from './mock';
import type { DataAdapter } from './types';

export { setAuthToken } from './live';
export * from './types';

/** Whether the most recent call was served by the mock fallback (for a dev/offline banner). */
let usingFallback = false;
export const isUsingFallback = (): boolean => usingFallback;

/**
 * Wrap every `live` method so a thrown error transparently retries against `mock`.
 * Genuine API error responses (404/409/422) are NOT swallowed — only network/unreachable
 * failures (no `error.response`) fall through, so real validation/conflict errors still surface.
 */
type AnyFn = (...args: any[]) => Promise<any>;

function withFallback(primary: DataAdapter, fallback: DataAdapter): DataAdapter {
  const wrapped: Record<string, AnyFn> = {};
  (Object.keys(primary) as (keyof DataAdapter)[]).forEach((key) => {
    const primaryFn = primary[key] as AnyFn;
    const fallbackFn = fallback[key] as AnyFn;
    wrapped[key as string] = async (...args: any[]) => {
      try {
        const result = await primaryFn(...args);
        usingFallback = false;
        return result;
      } catch (err: unknown) {
        const hasResponse =
          typeof err === 'object' && err !== null && 'response' in err && (err as any).response;
        if (hasResponse) throw err; // real HTTP error -> let the caller handle it
        usingFallback = true;
        return fallbackFn(...args);
      }
    };
  });
  return wrapped as unknown as DataAdapter;
}

export const data: DataAdapter =
  config.dataSource === 'live' ? withFallback(liveAdapter, mockAdapter) : mockAdapter;

/** True when the configured source is `live` (realtime seat locking only works here). */
export const isLiveSource = config.dataSource === 'live';

// In mock mode there is no real auth; keep the token slot clear.
if (!isLiveSource) setAuthToken(null);
