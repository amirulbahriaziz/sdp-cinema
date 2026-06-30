/**
 * SeatLegend — color key for the seat grid: available / selected / held by other / booked.
 */
import { StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

const ITEMS: { label: string; color: string }[] = [
  { label: 'Available', color: colors.seat.available },
  { label: 'Selected', color: colors.seat.selected },
  { label: 'Held', color: colors.seat.held },
  { label: 'Booked', color: colors.seat.booked },
];

export function SeatLegend() {
  return (
    <View style={styles.row}>
      {ITEMS.map((item) => (
        <View key={item.label} style={styles.item}>
          <View style={[styles.swatch, { backgroundColor: item.color }]} />
          <Text style={styles.label}>{item.label}</Text>
        </View>
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', flexWrap: 'wrap', gap: space['4'], justifyContent: 'center' },
  item: { flexDirection: 'row', alignItems: 'center', gap: space['2'] },
  swatch: { width: 14, height: 14, borderRadius: radius.sm },
  label: { ...typeScale.caption, color: colors.text.muted },
});
