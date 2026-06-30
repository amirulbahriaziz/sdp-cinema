/**
 * PriceTotalBar — the sticky bottom bar used across the booking flow: a left-aligned amount
 * (sub-total / total) with an optional caption, and a full-width-ish primary CTA on the right.
 * Money is passed as integer minor units + currency and formatted here.
 */
import { StyleSheet, Text, View } from 'react-native';

import { colors, formatMoney, space, type as typeScale } from '../theme';
import { PrimaryButton } from './PrimaryButton';

interface PriceTotalBarProps {
  label: string;
  amount: number;
  currency?: string;
  caption?: string;
  ctaLabel: string;
  onPress?: () => void;
  disabled?: boolean;
  loading?: boolean;
}

export function PriceTotalBar({
  label,
  amount,
  currency = 'RM',
  caption,
  ctaLabel,
  onPress,
  disabled,
  loading,
}: PriceTotalBarProps) {
  return (
    <View style={styles.row}>
      <View style={styles.amountCol}>
        <Text style={styles.label}>{label}</Text>
        <Text style={styles.amount}>{formatMoney(amount, currency)}</Text>
        {caption ? <Text style={styles.caption}>{caption}</Text> : null}
      </View>
      <PrimaryButton
        label={ctaLabel}
        onPress={onPress}
        disabled={disabled}
        loading={loading}
        style={styles.cta}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', alignItems: 'center', gap: space['4'] },
  amountCol: { gap: 2 },
  label: { ...typeScale.caption, color: colors.text.muted },
  amount: { ...typeScale.title, color: colors.text.primary },
  caption: { ...typeScale.caption, color: colors.text.muted },
  cta: { flex: 1, paddingHorizontal: space['6'] },
});
