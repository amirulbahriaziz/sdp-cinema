/**
 * Seat Selection — step 2. Renders the curved Screen + seat grid (rows A–H) via `<SeatMap>`,
 * each cell colored by its live status (available / held / booked) or the client-only
 * `selected`. Tapping an available seat toggles it in the booking draft and updates the
 * SUB-TOTAL (integer minor units, RM). Booked/held seats are non-interactive.
 *
 * Realtime: `useSeatChannel` subscribes to the showtime's Reverb channel and patches the
 * React Query seat-map cache on every broadcast, so other clients' holds/bookings appear
 * live; `useSeatMap` polls only while that socket is down (fallback). In live mode a tap
 * also acquires/releases the FCFS server lock — the loser of a race gets a 409 and the
 * optimistic selection rolls back.
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeBack } from '@/lib/use-safe-back';
import { useEffect, useMemo } from 'react';
import { ActivityIndicator, Alert, StyleSheet, Text, View } from 'react-native';

import { useLockSeat, useReleaseSeat, useSeatMap } from '@/api/hooks';
import { PriceTotalBar, Screen, SeatLegend, SeatMap, StepHeader, WizardFooter } from '@/components';
import { isLiveSource } from '@/data';
import type { Seat } from '@/data/types';
import { useSeatChannel } from '@/realtime/use-seat-channel';
import { useBookingStore } from '@/store/booking';
import { colors, space, type as typeScale } from '@/theme';

export default function SeatSelectionScreen() {
  const router = useRouter();
  const goBack = useSafeBack();
  const { showtimeId: param } = useLocalSearchParams<{ showtimeId: string }>();
  const showtimeId = Number(param);

  const draftShowtimeId = useBookingStore((s) => s.showtimeId);
  const startBooking = useBookingStore((s) => s.startBooking);
  const seats = useBookingStore((s) => s.seats);
  const toggleSeat = useBookingStore((s) => s.toggleSeat);

  // Deep-link / refresh safety: ensure the draft targets this showtime.
  useEffect(() => {
    if (draftShowtimeId !== showtimeId) startBooking(showtimeId);
  }, [draftShowtimeId, showtimeId, startBooking]);

  // Realtime channel patches the seat-map cache; while it is connected, stop polling.
  const { connected } = useSeatChannel(showtimeId);
  const { data: seatMap, isLoading, isError } = useSeatMap(showtimeId, {
    socketConnected: connected,
  });

  const lockSeat = useLockSeat(showtimeId);
  const releaseSeat = useReleaseSeat(showtimeId);

  const selectedCodes = useMemo(() => seats.map((s) => s.seat_code), [seats]);
  const subtotal = useMemo(() => seats.reduce((sum, s) => sum + s.price, 0), [seats]);

  const onToggleSeat = (seat: Seat) => {
    const wasSelected = selectedCodes.includes(seat.seat_code);
    const draftSeat = { seat_code: seat.seat_code, type: seat.type, price: seat.price };

    // Optimistic: flip the draft immediately so the UI feels instant.
    toggleSeat(draftSeat);

    if (!isLiveSource) return; // mock mode: draft-local only, no server lock.

    if (wasSelected) {
      // Deselect -> release the FCFS hold (TTL would also free it; release is immediate).
      releaseSeat.mutate(seat.seat_code);
    } else {
      // Select -> acquire the FCFS hold; the loser of a race rolls the selection back.
      lockSeat.mutate(seat.seat_code, {
        onError: () => {
          toggleSeat(draftSeat); // undo the optimistic add
          Alert.alert('Seat unavailable', 'Someone just took that seat. Please pick another.');
        },
      });
    }
  };

  return (
    <Screen
      header={<StepHeader title="Select Seats" onBack={goBack} />}
      contentStyle={styles.content}
      footer={
        <WizardFooter showtimeId={showtimeId}>
          <PriceTotalBar
            label="Sub-total"
            amount={subtotal}
            currency={seatMap?.currency ?? 'RM'}
            caption={
              seats.length ? `${seats.length} seat${seats.length > 1 ? 's' : ''} selected` : 'No seats yet'
            }
            ctaLabel="Proceed"
            disabled={seats.length === 0}
            onPress={() => router.push(`/booking/food/${showtimeId}`)}
          />
        </WizardFooter>
      }>
      {isLoading ? (
        <ActivityIndicator color={colors.accent.primary} style={styles.loader} />
      ) : isError || !seatMap ? (
        <View style={styles.center}>
          <Text style={styles.error}>Couldn&apos;t load the seat map.</Text>
        </View>
      ) : (
        <View style={styles.body}>
          <SeatLegend />
          <SeatMap seatMap={seatMap} selected={selectedCodes} onToggleSeat={onToggleSeat} />
        </View>
      )}
    </Screen>
  );
}

const styles = StyleSheet.create({
  content: { alignItems: 'center' },
  body: { gap: space['8'], alignItems: 'center', paddingTop: space['4'] },
  loader: { marginTop: space['12'] },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: space['6'] },
  error: { ...typeScale.body, color: colors.text.muted },
});
