/**
 * React Query hooks — the only way screens read server data. Every `queryFn` calls the
 * active data adapter (`data`), so hooks are source-agnostic (live or mock + fallback).
 *
 * The seat-map query polls (`refetchInterval`) as the realtime fallback: if the WebSocket
 * drops, polling still converges the seat grid. (The socket patcher lives in a later slice.)
 */
import {
  useMutation,
  useQuery,
  useQueryClient,
  type UseQueryOptions,
} from '@tanstack/react-query';

import { data } from '../data';
import type {
  BookingRequest,
  BookingResult,
  FoodQuery,
  MovieQuery,
  ShowtimeQuery,
} from '../data/types';
import { queryKeys } from './query-client';

export function useMovies(query?: MovieQuery) {
  return useQuery({
    queryKey: queryKeys.movies(query),
    queryFn: () => data.getMovies(query),
  });
}

export function useMovie(id: number, options?: Partial<UseQueryOptions<Awaited<ReturnType<typeof data.getMovie>>>>) {
  return useQuery({
    queryKey: queryKeys.movie(id),
    queryFn: () => data.getMovie(id),
    enabled: id > 0,
    ...options,
  });
}

export function useCinemas(city?: string) {
  return useQuery({
    queryKey: queryKeys.cinemas(city),
    queryFn: () => data.getCinemas(city),
  });
}

export function useShowtimes(query?: ShowtimeQuery) {
  return useQuery({
    queryKey: queryKeys.showtimes(query),
    queryFn: () => data.getShowtimes(query),
  });
}

export function useSeatMap(showtimeId: number, opts?: { socketConnected?: boolean }) {
  // Polling is the realtime fallback: while the Reverb socket is connected, live
  // `SeatStatusChanged` events patch the cache directly, so we stop polling. The moment
  // the socket drops (`socketConnected` false), polling resumes and re-converges the grid.
  const socketConnected = opts?.socketConnected ?? false;
  return useQuery({
    queryKey: queryKeys.seatMap(showtimeId),
    queryFn: () => data.getSeatMap(showtimeId),
    enabled: showtimeId > 0,
    refetchInterval: socketConnected ? 15000 : 5000,
  });
}

export function useFoodItems(query?: FoodQuery) {
  return useQuery({
    queryKey: queryKeys.foodItems(query),
    queryFn: () => data.getFoodItems(query),
  });
}

export function useLockSeat(showtimeId: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (seatCode: string) => data.lockSeat(showtimeId, seatCode),
    onSettled: () => qc.invalidateQueries({ queryKey: queryKeys.seatMap(showtimeId) }),
  });
}

export function useReleaseSeat(showtimeId: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: (seatCode: string) => data.releaseSeat(showtimeId, seatCode),
    onSettled: () => qc.invalidateQueries({ queryKey: queryKeys.seatMap(showtimeId) }),
  });
}

/** Cancel the in-progress booking — release all of the user's holds for the showtime. */
export function useCancelHolds(showtimeId: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: () => data.cancelHolds(showtimeId),
    onSettled: () => qc.invalidateQueries({ queryKey: queryKeys.seatMap(showtimeId) }),
  });
}

export function useCreateBooking() {
  return useMutation({
    mutationFn: (payload: BookingRequest) => data.createBooking(payload),
  });
}

/** My Tickets — the caller's bookings, newest first. */
export function useBookings() {
  return useQuery({ queryKey: queryKeys.bookings(), queryFn: () => data.getBookings() });
}

/** One booking (the ticket). `initialData` shows the just-made booking instantly. */
export function useBooking(id: number, initialData?: BookingResult) {
  return useQuery({
    queryKey: queryKeys.booking(id),
    queryFn: () => data.getBooking(id),
    enabled: id > 0,
    initialData,
  });
}
