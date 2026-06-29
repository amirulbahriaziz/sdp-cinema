/**
 * Payment Method — step 5. Three selectable methods (Debit card / Bank Transfer / Crypto) bound
 * to the booking draft. The total payable mirrors the summary's advisory figure (integer minor
 * units, RM) — the API recomputes it on confirm.
 *
 * Routing: "Debit card" continues to the dummy card form; "Bank Transfer" / "Crypto" are
 * dummy too, so they pay inline (checkout -> confirmation) without a second screen.
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeBack } from '@/lib/use-safe-back';
import { StyleSheet, Text, View } from 'react-native';

import { PaymentMethodRow, PriceTotalBar, Screen, StepHeader } from '@/components';
import type { PaymentMethod } from '@/data/types';
import { useCheckout } from '@/lib/checkout';
import { selectTotals, useBookingStore } from '@/store/booking';
import { colors, formatMoney, space, type as typeScale } from '@/theme';

const METHODS: {
  value: PaymentMethod;
  icon: 'card-outline' | 'business-outline' | 'logo-bitcoin';
  title: string;
  subtitle: string;
}[] = [
  { value: 'card', icon: 'card-outline', title: 'Debit / Credit Card', subtitle: 'Visa, Mastercard' },
  { value: 'bank', icon: 'business-outline', title: 'Bank Transfer', subtitle: 'FPX online banking' },
  { value: 'crypto', icon: 'logo-bitcoin', title: 'Crypto', subtitle: 'BTC, ETH, USDT' },
];

export default function PaymentMethodScreen() {
  const router = useRouter();
  const goBack = useSafeBack();
  const { showtimeId } = useLocalSearchParams<{ showtimeId: string }>();

  const showtime = useBookingStore((s) => s.showtime);
  const seats = useBookingStore((s) => s.seats);
  const paymentMethod = useBookingStore((s) => s.paymentMethod);
  const setPaymentMethod = useBookingStore((s) => s.setPaymentMethod);
  const totals = useBookingStore(selectTotals);

  const currency = showtime?.tier.currency ?? 'RM';
  const { pay, isPending, isError } = useCheckout();

  const proceed = () => {
    if (paymentMethod === 'card') {
      router.push(`/booking/card/${showtimeId}`);
    } else {
      pay(); // Bank Transfer / Crypto are dummy — go straight to checkout.
    }
  };

  const ctaLabel =
    paymentMethod === 'card' ? 'Continue' : `Pay ${formatMoney(totals.total, currency)}`;

  return (
    <Screen
      header={<StepHeader title="Payment Method" onBack={goBack} />}
      footer={
        <PriceTotalBar
          label="Total Payable"
          amount={totals.total}
          currency={currency}
          ctaLabel={ctaLabel}
          onPress={proceed}
          disabled={seats.length === 0}
          loading={isPending}
        />
      }>
      <View style={styles.content}>
        <Text style={styles.hint}>How would you like to pay?</Text>
        <View style={styles.list}>
          {METHODS.map((m) => (
            <PaymentMethodRow
              key={m.value}
              icon={m.icon}
              title={m.title}
              subtitle={m.subtitle}
              selected={paymentMethod === m.value}
              onPress={() => setPaymentMethod(m.value)}
            />
          ))}
        </View>
        {isError ? (
          <Text style={styles.error}>Payment couldn&apos;t be completed. Please try again.</Text>
        ) : null}
      </View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  content: { gap: space['4'], paddingTop: space['4'] },
  hint: { ...typeScale.body, color: colors.text.muted },
  list: { gap: space['3'] },
  error: { ...typeScale.caption, color: colors.state.error, textAlign: 'center' },
});
