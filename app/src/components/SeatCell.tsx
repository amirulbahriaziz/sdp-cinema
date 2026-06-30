/**
 * SeatCell — a single seat square, colored by its effective status. `booked` and `held`
 * (held by another user) are not pressable; `available`/`selected` are. The client-only
 * `selected` status comes from the booking draft, never from the API.
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';

import type { SeatStatusClient } from '../data/types';
import { colors, radius, type as typeScale } from '../theme';

export const SEAT_SIZE = 28;

const STATUS_COLOR: Record<SeatStatusClient, string> = {
  available: colors.seat.available,
  selected: colors.seat.selected,
  held: colors.seat.held,
  booked: colors.seat.booked,
};

interface SeatCellProps {
  seatCode: string;
  status: SeatStatusClient;
  onPress?: (seatCode: string) => void;
}

export function SeatCell({ seatCode, status, onPress }: SeatCellProps) {
  const interactive = status === 'available' || status === 'selected';
  const body = (
    <View style={[styles.cell, { backgroundColor: STATUS_COLOR[status] }]}>
      {status === 'booked' ? (
        <Ionicons name="close" size={14} color={colors.state.error} />
      ) : (
        <Text style={styles.code} numberOfLines={1}>
          {seatCode}
        </Text>
      )}
    </View>
  );

  if (!interactive) return body;
  return (
    <Pressable
      accessibilityRole="button"
      accessibilityLabel={`Seat ${seatCode}, ${status}`}
      onPress={() => onPress?.(seatCode)}
      style={({ pressed }) => pressed && styles.pressed}>
      {body}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  cell: {
    width: SEAT_SIZE,
    height: SEAT_SIZE,
    borderRadius: radius.sm,
    alignItems: 'center',
    justifyContent: 'center',
  },
  pressed: { opacity: 0.7 },
  code: { ...typeScale.caption, fontSize: 9, lineHeight: 11, color: colors.text.primary },
});
