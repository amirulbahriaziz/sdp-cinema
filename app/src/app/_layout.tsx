import { DarkTheme, ThemeProvider } from 'expo-router';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';

import { AppProviders } from '@/api/provider';
import { AnimatedSplashOverlay } from '@/components/animated-icon';
import { colors } from '@/theme';

/**
 * Root layout. Mounts app-wide providers (React Query + safe-area) over a native Stack and
 * forces the dark theme — SDP Cinema is dark-only (see ui-context.md).
 *
 * The Stack hosts the `(tabs)` group (Home / Explore bottom nav) and the pushed detail routes
 * (`movie/[id]`, `booking/[movieId]`), mirroring the wireframe flow Home -> Movie info -> Book.
 */
export default function RootLayout() {
  return (
    <AppProviders>
      <ThemeProvider value={DarkTheme}>
        <StatusBar style="light" />
        <AnimatedSplashOverlay />
        <Stack
          screenOptions={{
            headerShown: false,
            contentStyle: { backgroundColor: colors.bg.base },
          }}>
          <Stack.Screen name="(tabs)" />
          <Stack.Screen name="movie/[id]" />
          <Stack.Screen name="booking/[movieId]" />
          <Stack.Screen name="booking/seats/[showtimeId]" />
          <Stack.Screen name="booking/food/[showtimeId]" />
          <Stack.Screen name="booking/summary/[showtimeId]" />
        </Stack>
      </ThemeProvider>
    </AppProviders>
  );
}
