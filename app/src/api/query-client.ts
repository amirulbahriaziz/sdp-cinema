/**
 * Shared React Query client. RQ owns all server truth (movies, showtimes, seat map) —
 * fetching, caching, and the polling fallback (`refetchInterval`) for realtime seat updates.
 */
import { QueryClient } from '@tanstack/react-query';

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
});

/** Stable query keys so caches/invalidations line up across hooks and the realtime patcher. */
export const queryKeys = {
  movies: (q?: unknown) => ['movies', q ?? null] as const,
  movie: (id: number) => ['movie', id] as const,
  cinemas: (city?: string) => ['cinemas', city ?? null] as const,
  showtimes: (q?: unknown) => ['showtimes', q ?? null] as const,
  seatMap: (showtimeId: number) => ['seatMap', showtimeId] as const,
  foodItems: (q?: unknown) => ['foodItems', q ?? null] as const,
};
