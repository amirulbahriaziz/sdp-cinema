/**
 * Ticket / Receipt — one confirmed booking as a ticket stub: movie + screening, seats, the full
 * money breakdown (tickets + F&B + service charge − discount = total) and the payment line.
 * Reached from Confirmation's "View ticket" and from the My Tickets tab. Loads the booking by id
 * (`useBooking`), using the just-made booking in the store as instant fallback. Money is integer
 * minor units in RM.
 */
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { ActivityIndicator, ScrollView, StyleSheet, Text, View } from 'react-native';

import { useBooking } from '@/api/hooks';
import { PrimaryButton, Screen, SummaryRow } from '@/components';
import { formatDateShort, formatTime } from '@/lib/datetime';
import { useBookingStore } from '@/store/booking';
import { colors, formatMoney, radius, space, type as typeScale } from '@/theme';

export default function TicketScreen() {
  const router = useRouter();
  const { bookingId } = useLocalSearchParams<{ bookingId: string }>();
  const id = Number(bookingId);
  const stored = useBookingStore((s) => s.result);
  const fromStore = stored?.id === id ? stored : undefined;
  const { data: result, isLoading } = useBooking(id, fromStore);

  const back = () => (router.canGoBack() ? router.back() : router.replace('/'));

  if (!result) {
    return (
      <Screen header={<Header />} scroll={false}>
        <View style={styles.empty}>
          {isLoading ? (
            <ActivityIndicator color={colors.accent.primary} />
          ) : (
            <>
              <Text style={styles.emptyText}>Ticket not found.</Text>
              <PrimaryButton label="Back" onPress={back} />
            </>
          )}
        </View>
      </Screen>
    );
  }

  const { showtime, payment } = result;
  const c = result.currency || 'RM';
  const seatList = result.seats.map((s) => s.seat_code).join(', ');

  return (
    <Screen header={<Header />} footer={<PrimaryButton label="Done" onPress={back} />}>
      <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        <View style={styles.statusRow}>
          <Ionicons name="checkmark-circle" size={20} color={colors.state.success} />
          <Text style={styles.statusText}>Booking {result.status}</Text>
        </View>

        <View style={styles.ticket}>
          <View style={styles.stubTop}>
            <Text style={styles.movie}>{showtime.movie.title}</Text>
            <Row label="Cinema" value={`${showtime.cinema.name} · ${showtime.hall.name}`} />
            <Row
              label="When"
              value={`${formatDateShort(showtime.starts_at)} · ${formatTime(showtime.starts_at)}`}
            />
            <Row label="Seats" value={seatList || '—'} />
          </View>

          <View style={styles.perforation}>
            <View style={[styles.notch, styles.notchLeft]} />
            <View style={styles.dashed} />
            <View style={[styles.notch, styles.notchRight]} />
          </View>

          <View style={styles.stubBottom}>
            <Row label="Reference" value={result.reference} mono />
            <Row label="Status" value={payment.status} />
          </View>
        </View>

        <View style={styles.breakdown}>
          <SummaryRow label={`Tickets (${result.seats.length})`} value={formatMoney(result.subtotal, c)} />
          {result.food.map((f) => (
            <SummaryRow key={f.food_item_id} label={`${f.name} × ${f.qty}`} value={formatMoney(f.line_total, c)} />
          ))}
          {result.food_total > 0 ? (
            <SummaryRow label="Food & Beverage" value={formatMoney(result.food_total, c)} />
          ) : null}
          <SummaryRow label="Service charge" value={formatMoney(result.service_charge, c)} />
          {result.discount > 0 ? (
            <SummaryRow
              label={`Discount${result.promo_code ? ` (${result.promo_code})` : ''}`}
              value={`− ${formatMoney(result.discount, c)}`}
            />
          ) : null}
          <View style={styles.divider} />
          <SummaryRow label="Total paid" value={formatMoney(result.total, c)} emphasis />
          <SummaryRow label={`Paid via ${payment.method}`} value={formatMoney(payment.amount, c)} />
        </View>
      </ScrollView>
    </Screen>
  );
}

function Header() {
  return (
    <View style={styles.header}>
      <Ionicons name="ticket-outline" size={22} color={colors.text.primary} />
      <Text style={styles.headerTitle}>Your Ticket</Text>
      <View style={{ width: 22 }} />
    </View>
  );
}

function Row({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <View style={styles.row}>
      <Text style={styles.rowLabel}>{label}</Text>
      <Text style={[styles.rowValue, mono && styles.mono]}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: space['4'],
    paddingVertical: space['2'],
  },
  headerTitle: { ...typeScale.subtitle, color: colors.text.primary },
  content: { padding: space['4'], gap: space['4'] },
  statusRow: { flexDirection: 'row', alignItems: 'center', gap: space['2'], justifyContent: 'center' },
  statusText: { ...typeScale.bodyBold, color: colors.state.success, textTransform: 'capitalize' },
  ticket: {
    backgroundColor: colors.bg.surface,
    borderRadius: radius.lg,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    overflow: 'hidden',
  },
  stubTop: { padding: space['4'], gap: space['2'] },
  stubBottom: { padding: space['4'], gap: space['2'] },
  movie: { ...typeScale.title, color: colors.text.primary, marginBottom: space['1'] },
  perforation: { flexDirection: 'row', alignItems: 'center', height: 20 },
  dashed: {
    flex: 1,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    borderStyle: 'dashed',
    marginHorizontal: space['1'],
  },
  notch: { width: 20, height: 20, borderRadius: 10, backgroundColor: colors.bg.base },
  notchLeft: { marginLeft: -10 },
  notchRight: { marginRight: -10 },
  row: { flexDirection: 'row', gap: space['3'] },
  rowLabel: { ...typeScale.caption, color: colors.text.muted, width: 80 },
  rowValue: { ...typeScale.body, color: colors.text.primary, flex: 1 },
  mono: { ...typeScale.bodyBold, letterSpacing: 1 },
  breakdown: {
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    padding: space['4'],
    gap: space['2'],
  },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: colors.border.default,
    marginVertical: space['1'],
  },
  empty: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: space['4'], padding: space['6'] },
  emptyText: { ...typeScale.body, color: colors.text.muted, textAlign: 'center' },
});
