/**
 * Confirmation — step 6, the booking success screen. Reads the confirmed booking the checkout
 * stored on the draft (`result`) and shows the reference + ticket summary. "View ticket" and
 * "Main menu" both clear the draft and return to the home tabs (a dedicated ticket-detail
 * screen is a later slice). Totals come straight from the server-confirmed booking (RM, minor
 * units) — not the advisory draft figures.
 */
import { useRouter } from 'expo-router';
import { StyleSheet, Text, View } from 'react-native';

import { ResultScreen, Screen, SummaryRow } from '@/components';
import { formatDateShort, formatTime } from '@/lib/datetime';
import { useBookingStore } from '@/store/booking';
import { colors, formatMoney, radius, space, type as typeScale } from '@/theme';

export default function ConfirmationScreen() {
  const router = useRouter();
  const result = useBookingStore((s) => s.result);
  const reset = useBookingStore((s) => s.reset);

  const goHome = () => {
    reset();
    router.dismissAll();
  };

  if (!result) {
    // Defensive: a deep link / reload without a checkout has no booking to show.
    return (
      <Screen scroll={false}>
        <ResultScreen
          icon="alert-circle"
          title="No booking found"
          message="We couldn't find a confirmed booking. Please start a new one from the home screen."
          actions={[{ label: 'Main menu', onPress: goHome }]}
        />
      </Screen>
    );
  }

  const { showtime, payment, currency } = result;
  const seatList = result.seats.map((s) => s.seat_code).join(', ');

  return (
    <Screen scroll={false}>
      <ResultScreen
        title="Congratulations!"
        message={`Your booking is confirmed. Reference ${result.reference}.`}
        actions={[
          { label: 'View ticket', onPress: goHome, variant: 'primary' },
          { label: 'Main menu', onPress: goHome, variant: 'ghost' },
        ]}
        details={
          <View style={styles.ticket}>
            <Text style={styles.movie}>{showtime.movie.title}</Text>
            <Row label="Cinema" value={`${showtime.cinema.name} · ${showtime.hall.name}`} />
            <Row
              label="When"
              value={`${formatDateShort(showtime.starts_at)} · ${formatTime(showtime.starts_at)}`}
            />
            <Row label="Seats" value={seatList || '—'} />
            <Row label="Reference" value={result.reference} />
            <View style={styles.divider} />
            <SummaryRow
              label={`Paid (${payment.method})`}
              value={formatMoney(payment.amount, currency)}
              emphasis
            />
          </View>
        }
      />
    </Screen>
  );
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.row}>
      <Text style={styles.rowLabel}>{label}</Text>
      <Text style={styles.rowValue}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  ticket: {
    padding: space['4'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    gap: space['2'],
  },
  movie: { ...typeScale.title, color: colors.text.primary, marginBottom: space['1'] },
  row: { flexDirection: 'row', gap: space['3'] },
  rowLabel: { ...typeScale.caption, color: colors.text.muted, width: 72 },
  rowValue: { ...typeScale.body, color: colors.text.primary, flex: 1 },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: colors.border.default,
    marginVertical: space['1'],
  },
});
