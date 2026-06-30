/**
 * SummaryRow — a label/value line for the Booking Summary and totals. `emphasis` renders the
 * grand-total row (larger, primary text). Money values should be pre-formatted via formatMoney.
 */
import { StyleSheet, Text, View } from 'react-native';

import { colors, space, type as typeScale } from '../theme';

interface SummaryRowProps {
  label: string;
  value: string;
  emphasis?: boolean;
  /** Muted value (e.g. discounts in accent / negatives). */
  accent?: boolean;
}

export function SummaryRow({ label, value, emphasis, accent }: SummaryRowProps) {
  return (
    <View style={[styles.row, emphasis && styles.rowEmphasis]}>
      <Text style={[styles.label, emphasis && styles.labelEmphasis]}>{label}</Text>
      <Text
        style={[
          styles.value,
          emphasis && styles.valueEmphasis,
          accent && { color: colors.accent.primary },
        ]}>
        {value}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: space['1'],
  },
  rowEmphasis: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: colors.border.default,
    paddingTop: space['3'],
    marginTop: space['1'],
  },
  label: { ...typeScale.body, color: colors.text.muted },
  labelEmphasis: { ...typeScale.bodyBold, color: colors.text.primary },
  value: { ...typeScale.body, color: colors.text.primary },
  valueEmphasis: { ...typeScale.title, color: colors.text.primary },
});
