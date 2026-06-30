/**
 * App-wide providers wrapper: React Query (server data) over the safe-area context.
 * Mounted once in the router root layout.
 *
 * Live mode also bootstraps a Sanctum session: lock/release/booking are auth-gated and this
 * build has no login screen, so we auto-log-in once with the demo credentials and stash the
 * bearer token. (Override the user per simulator via EXPO_PUBLIC_DEMO_EMAIL for a 2-client
 * FCFS demo.) A failure is non-fatal — reads still work and the data layer falls back to mock.
 */
import { QueryClientProvider } from '@tanstack/react-query';
import { type ReactNode, useEffect } from 'react';
import { SafeAreaProvider } from 'react-native-safe-area-context';

import { config } from '../config';
import { data, isLiveSource, setAuthToken } from '../data';
import { queryClient } from './query-client';

export function AppProviders({ children }: { children: ReactNode }) {
  useEffect(() => {
    if (!isLiveSource) return;
    let active = true;
    data
      .login(config.devAuth.email, config.devAuth.password)
      .then((res) => {
        if (active) setAuthToken(res.token);
      })
      .catch(() => {
        // No token: reads still work; lock/release/booking will surface auth errors.
      });
    return () => {
      active = false;
    };
  }, []);

  return (
    <SafeAreaProvider>
      <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
    </SafeAreaProvider>
  );
}
