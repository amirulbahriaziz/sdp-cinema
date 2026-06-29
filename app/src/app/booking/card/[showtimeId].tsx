/**
 * Card Payment — the dummy debit/credit card form reached from the Payment Method screen when
 * "Card" is selected. No real gateway: the inputs are validated for shape only (length), then
 * "Pay RM <total>" runs the same checkout as the other methods (POST /bookings, or mock success)
 * and routes to the confirmation screen. Card data is never sent anywhere.
 */
import { useRouter } from 'expo-router';
import { useState } from 'react';
import { StyleSheet, Text, TextInput, View } from 'react-native';

import { PriceTotalBar, Screen, StepHeader } from '@/components';
import { useCheckout } from '@/lib/checkout';
import { selectTotals, useBookingStore } from '@/store/booking';
import { colors, radius, space, type as typeScale } from '@/theme';

/** Group digits into 4s for display: "4111111111111111" -> "4111 1111 1111 1111". */
const formatCardNumber = (raw: string) =>
  raw
    .replace(/\D/g, '')
    .slice(0, 16)
    .replace(/(.{4})/g, '$1 ')
    .trim();

/** "1225" -> "12/25". */
const formatExpiry = (raw: string) => {
  const d = raw.replace(/\D/g, '').slice(0, 4);
  return d.length <= 2 ? d : `${d.slice(0, 2)}/${d.slice(2)}`;
};

export default function CardPaymentScreen() {
  const router = useRouter();
  const showtime = useBookingStore((s) => s.showtime);
  const totals = useBookingStore(selectTotals);
  const currency = showtime?.tier.currency ?? 'RM';

  const { pay, isPending, isError } = useCheckout();

  const [name, setName] = useState('');
  const [number, setNumber] = useState('');
  const [expiry, setExpiry] = useState('');
  const [cvv, setCvv] = useState('');

  const digits = number.replace(/\s/g, '');
  const valid =
    name.trim().length > 1 &&
    digits.length === 16 &&
    expiry.length === 5 &&
    cvv.length >= 3;

  return (
    <Screen
      header={<StepHeader title="Card Payment" onBack={() => router.back()} />}
      footer={
        <PriceTotalBar
          label="Total Payable"
          amount={totals.total}
          currency={currency}
          ctaLabel="Pay"
          onPress={pay}
          disabled={!valid}
          loading={isPending}
        />
      }>
      <View style={styles.content}>
        <View style={styles.card}>
          <Field label="Cardholder name">
            <TextInput
              style={styles.input}
              placeholder="ALEX TAN"
              placeholderTextColor={colors.text.muted}
              autoCapitalize="characters"
              value={name}
              onChangeText={setName}
            />
          </Field>

          <Field label="Card number">
            <TextInput
              style={styles.input}
              placeholder="4111 1111 1111 1111"
              placeholderTextColor={colors.text.muted}
              keyboardType="number-pad"
              value={number}
              onChangeText={(t) => setNumber(formatCardNumber(t))}
            />
          </Field>

          <View style={styles.row}>
            <Field label="Expiry" style={styles.half}>
              <TextInput
                style={styles.input}
                placeholder="MM/YY"
                placeholderTextColor={colors.text.muted}
                keyboardType="number-pad"
                value={expiry}
                onChangeText={(t) => setExpiry(formatExpiry(t))}
              />
            </Field>
            <Field label="CVV" style={styles.half}>
              <TextInput
                style={styles.input}
                placeholder="123"
                placeholderTextColor={colors.text.muted}
                keyboardType="number-pad"
                secureTextEntry
                maxLength={4}
                value={cvv}
                onChangeText={(t) => setCvv(t.replace(/\D/g, ''))}
              />
            </Field>
          </View>
        </View>

        <Text style={styles.note}>
          This is a demo. No real card is charged and no card data leaves your device.
        </Text>
        {isError ? (
          <Text style={styles.error}>Payment couldn&apos;t be completed. Please try again.</Text>
        ) : null}
      </View>
    </Screen>
  );
}

function Field({
  label,
  children,
  style,
}: {
  label: string;
  children: React.ReactNode;
  style?: object;
}) {
  return (
    <View style={[styles.field, style]}>
      <Text style={styles.fieldLabel}>{label}</Text>
      {children}
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
    gap: space['4'],
  },
  row: { flexDirection: 'row', gap: space['3'] },
  half: { flex: 1 },
  field: { gap: space['2'] },
  fieldLabel: { ...typeScale.captionBold, color: colors.text.muted },
  input: {
    height: 48,
    paddingHorizontal: space['3'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.elevated,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    color: colors.text.primary,
    ...typeScale.body,
  },
  note: { ...typeScale.caption, color: colors.text.muted, textAlign: 'center' },
  error: { ...typeScale.caption, color: colors.state.error, textAlign: 'center' },
});
