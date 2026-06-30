/**
 * Confirmation — step 6, the booking success screen. Reads the confirmed booking the checkout
 * stored on the draft (`result`) and shows the reference + ticket summary. "View ticket" and
 * "Main menu" both clear the draft and return to the home tabs (a dedicated ticket-detail
 * screen is a later slice). Totals come straight from the server-confirmed booking (RM, minor
 * units) — not the advisory draft figures.
 */
import { useRouter } from 'expo-router';
import { StyleSheet, Text, View } from 'react-native';

import { InfoRow, ResultScreen, Screen, SummaryRow } from '@/components';
import { formatDateShort, formatTime } from '@/lib/datetime';
import { useBookingStore } from '@/store/booking';
import { colors, formatMoney, space, surfaceCard, type as typeScale } from '@/theme';

export default function ConfirmationScreen() {
  const router = useRouter();
  const result = useBookingStore((s) => s.result);
  const reset = useBookingStore((s) => s.reset);

  const goHome = () => {
    reset();
    router.canDismiss() ? router.dismissAll() : router.replace('/');
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
          {
            label: 'View ticket',
            onPress: () => router.push(`/booking/ticket/${result.id}`),
            variant: 'primary',
          },
          { label: 'Main menu', onPress: goHome, variant: 'ghost' },
        ]}
        details={
          <View style={styles.ticket}>
            <Text style={styles.movie}>{showtime.movie.title}</Text>
            <InfoRow label="Cinema" value={`${showtime.cinema.name} · ${showtime.hall.name}`} />
            <InfoRow
              label="When"
              value={`${formatDateShort(showtime.starts_at)} · ${formatTime(showtime.starts_at)}`}
            />
            <InfoRow label="Seats" value={seatList || '—'} />
            <InfoRow label="Reference" value={result.reference} />
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

const styles = StyleSheet.create({
  ticket: { ...surfaceCard(), padding: space['4'], gap: space['2'] },
  movie: { ...typeScale.title, color: colors.text.primary, marginBottom: space['1'] },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: colors.border.default,
    marginVertical: space['1'],
  },
});
