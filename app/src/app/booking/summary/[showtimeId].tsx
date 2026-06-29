/**
 * Booking Summary — step 4. A ticket card (movie / cinema / hall / time / seats) plus the line
 * items: tickets, F&B, service charge, and an optional promo discount, ending in Total Payable.
 * Totals are the draft's advisory figures (`selectTotals`, integer minor units) — the API
 * recomputes them on confirm. "Proceed to Payment" hands off to the payment slice.
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeBack } from '@/lib/use-safe-back';
import { useMemo, useState } from 'react';
import { StyleSheet, Text, TextInput, View } from 'react-native';

import { useFoodItems, useMovie } from '@/api/hooks';
import { PrimaryButton, Screen, StepHeader, SummaryRow } from '@/components';
import { formatDateShort, formatTime } from '@/lib/datetime';
import { useBookingStore, useBookingTotals } from '@/store/booking';
import { colors, formatMoney, radius, space, type as typeScale } from '@/theme';

export default function BookingSummaryScreen() {
  const router = useRouter();
  const goBack = useSafeBack();
  const { showtimeId } = useLocalSearchParams<{ showtimeId: string }>();

  const showtime = useBookingStore((s) => s.showtime);
  const seats = useBookingStore((s) => s.seats);
  const food = useBookingStore((s) => s.food);
  const promoCode = useBookingStore((s) => s.promoCode);
  const setPromoCode = useBookingStore((s) => s.setPromoCode);
  const totals = useBookingTotals();

  const currency = showtime?.tier.currency ?? 'RM';
  const { data: movie } = useMovie(showtime?.movie_id ?? 0);
  const { data: foodItems } = useFoodItems();

  const [promoInput, setPromoInput] = useState(promoCode ?? '');

  // Map draft F&B (id -> qty/unitPrice) to named line items for display.
  const foodLines = useMemo(() => {
    const names = new Map((foodItems ?? []).map((f) => [f.id, f.name]));
    return Object.entries(food).map(([id, f]) => ({
      id: Number(id),
      name: names.get(Number(id)) ?? `Item #${id}`,
      qty: f.qty,
      lineTotal: f.qty * f.unitPrice,
    }));
  }, [food, foodItems]);

  const promoApplied = promoCode != null && totals.discount > 0;
  const promoInvalid = promoCode != null && totals.discount === 0;

  return (
    <Screen
      header={<StepHeader title="Booking Summary" onBack={goBack} />}
      footer={
        <PrimaryButton
          label="Proceed to Payment"
          disabled={seats.length === 0}
          onPress={() => router.push(`/booking/payment/${showtimeId}`)}
        />
      }>
      <View style={styles.content}>
        {/* Ticket card */}
        <View style={styles.card}>
          <Text style={styles.movie}>{movie?.title ?? 'Your Movie'}</Text>
          {showtime ? (
            <>
              <Row icon="Cinema" value={`${showtime.cinema.name} · ${showtime.hall.name}`} />
              <Row
                icon="When"
                value={`${formatDateShort(showtime.starts_at)} · ${formatTime(showtime.starts_at)}`}
              />
              <Row icon="Tier" value={showtime.tier.name} />
            </>
          ) : null}
          <Row icon="Seats" value={seats.map((s) => s.seat_code).join(', ') || '—'} />
        </View>

        {/* Line items */}
        <View style={styles.card}>
          <SummaryRow
            label={`Tickets (${seats.length})`}
            value={formatMoney(totals.subtotal, currency)}
          />
          {foodLines.map((line) => (
            <SummaryRow
              key={line.id}
              label={`${line.name} × ${line.qty}`}
              value={formatMoney(line.lineTotal, currency)}
            />
          ))}
          <SummaryRow label="Service charge" value={formatMoney(totals.serviceCharge, currency)} />
          {totals.discount > 0 ? (
            <SummaryRow
              label={`Promo (${promoCode})`}
              value={`- ${formatMoney(totals.discount, currency)}`}
              accent
            />
          ) : null}
          <SummaryRow label="Total Payable" value={formatMoney(totals.total, currency)} emphasis />
        </View>

        {/* Promo code */}
        <View style={styles.card}>
          <Text style={styles.promoLabel}>Promo code</Text>
          <View style={styles.promoRow}>
            <TextInput
              style={styles.promoInput}
              placeholder="e.g. WELCOME10"
              placeholderTextColor={colors.text.muted}
              autoCapitalize="characters"
              autoCorrect={false}
              value={promoInput}
              onChangeText={setPromoInput}
            />
            <PrimaryButton
              label="Apply"
              variant="ghost"
              style={styles.promoBtn}
              onPress={() => setPromoCode(promoInput.trim() ? promoInput : null)}
            />
          </View>
          {promoApplied ? (
            <Text style={styles.promoOk}>Promo applied.</Text>
          ) : promoInvalid ? (
            <Text style={styles.promoErr}>That code isn&apos;t valid.</Text>
          ) : null}
        </View>
      </View>
    </Screen>
  );
}

function Row({ icon, value }: { icon: string; value: string }) {
  return (
    <View style={styles.row}>
      <Text style={styles.rowLabel}>{icon}</Text>
      <Text style={styles.rowValue}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  content: { gap: space['4'], paddingTop: space['4'] },
  card: {
    padding: space['4'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    gap: space['2'],
  },
  movie: { ...typeScale.title, color: colors.text.primary, marginBottom: space['1'] },
  row: { flexDirection: 'row', gap: space['3'] },
  rowLabel: { ...typeScale.caption, color: colors.text.muted, width: 56 },
  rowValue: { ...typeScale.body, color: colors.text.primary, flex: 1 },
  promoLabel: { ...typeScale.captionBold, color: colors.text.muted },
  promoRow: { flexDirection: 'row', gap: space['3'], alignItems: 'center' },
  promoInput: {
    flex: 1,
    height: 48,
    paddingHorizontal: space['3'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.elevated,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    color: colors.text.primary,
    ...typeScale.body,
  },
  promoBtn: { height: 48, paddingHorizontal: space['5'] },
  promoOk: { ...typeScale.caption, color: colors.state.success },
  promoErr: { ...typeScale.caption, color: colors.state.error },
});
