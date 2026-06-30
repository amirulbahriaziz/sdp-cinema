/**
 * My Tickets — the bottom-nav "Tickets" tab. Lists the signed-in user's bookings (newest first)
 * via `useBookings` (GET /bookings); tapping one opens its receipt at /booking/ticket/{id}.
 * Live data only — mock mode has no booking history.
 */
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useFocusEffect } from 'expo-router';
import { useCallback } from 'react';
import { ActivityIndicator, FlatList, Pressable, StyleSheet, Text, View } from 'react-native';

import { useBookings } from '@/api/hooks';
import { Screen } from '@/components';
import type { BookingResult } from '@/data/types';
import { formatDateShort, formatTime } from '@/lib/datetime';
import { colors, formatMoney, radius, space, type as typeScale } from '@/theme';

export default function TicketsScreen() {
  const router = useRouter();
  const { data: bookings, isLoading, isError, refetch } = useBookings();

  // Refetch when the tab regains focus (e.g. after making a new booking).
  useFocusEffect(useCallback(() => { refetch(); }, [refetch]));

  return (
    <Screen header={<Header />} scroll={false}>
      {isLoading ? (
        <View style={styles.center}>
          <ActivityIndicator color={colors.accent.primary} />
        </View>
      ) : isError ? (
        <View style={styles.center}>
          <Text style={styles.muted}>Couldn&apos;t load your tickets.</Text>
        </View>
      ) : !bookings || bookings.length === 0 ? (
        <View style={styles.center}>
          <Ionicons name="ticket-outline" size={40} color={colors.text.muted} />
          <Text style={styles.muted}>No tickets yet. Book a movie to see it here.</Text>
        </View>
      ) : (
        <FlatList
          data={bookings}
          keyExtractor={(b) => String(b.id)}
          contentContainerStyle={styles.list}
          renderItem={({ item }) => (
            <TicketRow booking={item} onPress={() => router.push(`/booking/ticket/${item.id}`)} />
          )}
        />
      )}
    </Screen>
  );
}

function TicketRow({ booking, onPress }: { booking: BookingResult; onPress: () => void }) {
  const { showtime } = booking;
  const seats = booking.seats.map((s) => s.seat_code).join(', ');
  return (
    <Pressable style={({ pressed }) => [styles.card, pressed && styles.pressed]} onPress={onPress}>
      <View style={styles.cardMain}>
        <Text style={styles.movie} numberOfLines={1}>{showtime.movie.title}</Text>
        <Text style={styles.sub} numberOfLines={1}>
          {showtime.cinema.name} · {formatDateShort(showtime.starts_at)} · {formatTime(showtime.starts_at)}
        </Text>
        <Text style={styles.sub}>Seats {seats || '—'}</Text>
      </View>
      <View style={styles.cardRight}>
        <Text style={styles.total}>{formatMoney(booking.total, booking.currency || 'RM')}</Text>
        <Text style={[styles.status, booking.status === 'cancelled' && styles.cancelled]}>
          {booking.status}
        </Text>
        <Ionicons name="chevron-forward" size={18} color={colors.text.muted} />
      </View>
    </Pressable>
  );
}

function Header() {
  return (
    <View style={styles.header}>
      <Text style={styles.headerTitle}>My Tickets</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  header: { paddingHorizontal: space['4'], paddingVertical: space['3'] },
  headerTitle: { ...typeScale.title, color: colors.text.primary },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: space['3'], padding: space['6'] },
  muted: { ...typeScale.body, color: colors.text.muted, textAlign: 'center' },
  list: { padding: space['4'], gap: space['3'] },
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: space['3'],
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    padding: space['4'],
  },
  pressed: { opacity: 0.7 },
  cardMain: { flex: 1, gap: 2 },
  movie: { ...typeScale.bodyBold, color: colors.text.primary },
  sub: { ...typeScale.caption, color: colors.text.muted },
  cardRight: { alignItems: 'flex-end', gap: 2 },
  total: { ...typeScale.bodyBold, color: colors.text.primary },
  status: { ...typeScale.caption, color: colors.state.success, textTransform: 'capitalize' },
  cancelled: { color: colors.text.muted },
});
