/**
 * The active data source. One interface, two interchangeable adapters:
 *
 *   - `mock`  -> bundled JSON, no network.
 *   - `live`  -> HTTP to Laravel (the default).
 *
 * The source is selected at runtime from `config.dataSource` (EXPO_PUBLIC_DATA_SOURCE).
 * Live is STRICTLY live — failures surface to the caller (no silent mock). Mock is used only
 * when EXPO_PUBLIC_DATA_SOURCE=mock is set explicitly. Screens/hooks call `data` and never know
 * which adapter served them.
 */
import { config } from '../config';
import { liveAdapter } from './live';
import { mockAdapter } from './mock';
import type { DataAdapter } from './types';

export { setAuthToken } from './live';
export * from './types';

/** Mock only when explicitly selected; otherwise live (no fallback). */
export const data: DataAdapter = config.dataSource === 'mock' ? mockAdapter : liveAdapter;

/** True when the configured source is `live` (realtime seat locking only works here). */
export const isLiveSource = config.dataSource !== 'mock';
