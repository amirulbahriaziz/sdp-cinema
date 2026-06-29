import { DarkTheme, ThemeProvider } from 'expo-router';
import { StatusBar } from 'expo-status-bar';

import { AppProviders } from '@/api/provider';
import { AnimatedSplashOverlay } from '@/components/animated-icon';
import AppTabs from '@/components/app-tabs';

/**
 * Root layout. Mounts app-wide providers (React Query + safe-area) over the navigator and
 * forces the dark theme — SDP Cinema is dark-only (see ui-context.md).
 */
export default function RootLayout() {
  return (
    <AppProviders>
      <ThemeProvider value={DarkTheme}>
        <StatusBar style="light" />
        <AnimatedSplashOverlay />
        <AppTabs />
      </ThemeProvider>
    </AppProviders>
  );
}
