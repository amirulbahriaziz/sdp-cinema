/**
 * PriceTierCard — one of the two price-tier options (Classic / Premium) on the Ticket Booking
 * screen. Shows the tier name and its real MIN..MAX seat-price range (from `seat_type_prices`,
 * surfaced on the showtime tier). Selecting a tier filters the available time slots.
 */
import { Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, formatMoney, radius, space, type as typeScale } from '../theme';

interface PriceTierCardProps {
  name: string;
  priceMin: number;
  priceMax: number;
  currency: string;
  selected?: boolean;
  onPress?: () => void;
}

export function PriceTierCard({
  name,
  priceMin,
  priceMax,
  currency,
  selected,
  onPress,
}: PriceTierCardProps) {
  const range =
    priceMin === priceMax
      ? formatMoney(priceMin, currency)
      : `${formatMoney(priceMin, currency)} – ${formatMoney(priceMax, currency)}`;
  return (
    <Pressable
      accessibilityRole="button"
      onPress={onPress}
      style={[styles.card, selected && styles.cardActive]}>
      <View style={styles.head}>
        <Text style={styles.name}>{name}</Text>
        <View style={[styles.dot, selected && styles.dotActive]} />
      </View>
      <Text style={styles.rangeLabel}>Seats from</Text>
      <Text style={[styles.range, selected && styles.rangeActive]}>{range}</Text>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  card: {
    flex: 1,
    padding: space['4'],
    borderRadius: radius.md,
    gap: space['1'],
    backgroundColor: colors.bg.surface,
    borderWidth: 1,
    borderColor: colors.border.default,
  },
  cardActive: { borderColor: colors.accent.primary, backgroundColor: colors.bg.elevated },
  head: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  name: { ...typeScale.subtitle, color: colors.text.primary },
  dot: {
    width: 16,
    height: 16,
    borderRadius: radius.pill,
    borderWidth: 2,
    borderColor: colors.border.default,
  },
  dotActive: { borderColor: colors.accent.primary, backgroundColor: colors.accent.primary },
  rangeLabel: { ...typeScale.caption, color: colors.text.muted, marginTop: space['1'] },
  range: { ...typeScale.bodyBold, color: colors.text.primary },
  rangeActive: { color: colors.accent.primary },
});
