/**
 * Seat Selection — step 2. Renders the curved Screen + seat grid (rows A–H) via `<SeatMap>`,
 * each cell colored by its live status (available / held / booked) or the client-only
 * `selected`. Tapping an available seat toggles it in the booking draft and updates the
 * SUB-TOTAL (integer minor units, RM). Booked/held seats are non-interactive.
 *
 * The seat map is read through React Query (`useSeatMap`, with a polling fallback). FCFS
 * server-side locking is wired in the realtime slice; here selection is draft-local.
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useMemo } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useSeatMap } from '@/api/hooks';
import { PriceTotalBar, Screen, SeatLegend, SeatMap, StepHeader } from '@/components';
import type { Seat } from '@/data/types';
import { useBookingStore } from '@/store/booking';
import { colors, space, type as typeScale } from '@/theme';

export default function SeatSelectionScreen() {
  const router = useRouter();
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

  const { data: seatMap, isLoading, isError } = useSeatMap(showtimeId);

  const selectedCodes = useMemo(() => seats.map((s) => s.seat_code), [seats]);
  const subtotal = useMemo(() => seats.reduce((sum, s) => sum + s.price, 0), [seats]);

  const onToggleSeat = (seat: Seat) =>
    toggleSeat({ seat_code: seat.seat_code, type: seat.type, price: seat.price });

  return (
    <Screen
      header={<StepHeader title="Select Seats" onBack={() => router.back()} />}
      contentStyle={styles.content}
      footer={
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
