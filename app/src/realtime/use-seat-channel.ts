/**
 * useSeatChannel(showtimeId) — live seat-state realtime for one showtime.
 *
 * Subscribes to the public Reverb channel `showtime.{id}` (laravel-echo + pusher-js) and, on
 * every `SeatStatusChanged` ({ showtime_id, seat_code, status }), patches that one seat in the
 * React Query seat-map cache via `setQueryData` — no refetch needed while the socket is up.
 *
 * Returns `{ connected }` so the screen can drive the polling fallback: when the socket drops,
 * `useSeatMap` re-enables `refetchInterval` and converges the grid by polling instead.
 *
 * Mock mode has no realtime (the seat map is static), so this is a no-op there and reports
 * `connected: false`, which keeps polling on harmlessly.
 */
import { useQueryClient } from '@tanstack/react-query';
import { useEffect, useState } from 'react';

import { queryKeys } from '../api/query-client';
import { isLiveSource } from '../data';
import type { SeatMap, SeatStatus } from '../data/types';
import { getConnection, getEcho } from './echo';

interface SeatStatusEvent {
  showtime_id: number;
  seat_code: string;
  status: SeatStatus;
}

export function useSeatChannel(showtimeId: number): { connected: boolean } {
  const qc = useQueryClient();
  const [connected, setConnected] = useState(false);

  useEffect(() => {
    if (!isLiveSource || showtimeId <= 0) return;

    const echo = getEcho();
    const channelName = `showtime.${showtimeId}`;

    // Reverb broadcastAs() is 'seat.status.changed' -> Echo listens with a leading dot.
    echo.channel(channelName).listen('.seat.status.changed', (e: SeatStatusEvent) => {
      qc.setQueryData<SeatMap>(queryKeys.seatMap(showtimeId), (prev) => {
        if (!prev) return prev;
        return {
          ...prev,
          seats: prev.seats.map((seat) =>
            seat.seat_code === e.seat_code ? { ...seat, status: e.status } : seat,
          ),
        };
      });
    });

    const conn = getConnection();
    const sync = () => setConnected(conn?.state === 'connected');
    if (conn) {
      sync();
      conn.bind('state_change', sync);
    }

    return () => {
      conn?.unbind('state_change', sync);
      echo.leaveChannel(channelName);
    };
  }, [qc, showtimeId]);

  return { connected };
}
