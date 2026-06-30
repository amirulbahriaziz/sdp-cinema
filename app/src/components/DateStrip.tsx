/**
 * DateStrip — horizontal scroller of selectable date pills (the available showtime dates for a
 * movie). Each pill stacks weekday over day-of-month; the active date uses the accent fill.
 */
import { Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';

import { dateParts } from '../lib/datetime';
import { colors, radius, space, type as typeScale } from '../theme';

interface DateStripProps {
  /** ISO dates (YYYY-MM-DD), ascending. */
  dates: string[];
  value: string | null;
  onChange: (date: string) => void;
}

export function DateStrip({ dates, value, onChange }: DateStripProps) {
  return (
    <ScrollView
      horizontal
      showsHorizontalScrollIndicator={false}
      contentContainerStyle={styles.row}>
      {dates.map((d) => {
        const active = d === value;
        const { weekday, day } = dateParts(d);
        return (
          <Pressable
            key={d}
            accessibilityRole="button"
            style={[styles.pill, active && styles.pillActive]}
            onPress={() => onChange(d)}>
            <Text style={[styles.weekday, active && styles.textActive]}>{weekday}</Text>
            <Text style={[styles.day, active && styles.textActive]}>{day}</Text>
          </Pressable>
        );
      })}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  row: { gap: space['2'], paddingVertical: space['1'] },
  pill: {
    width: 56,
    paddingVertical: space['3'],
    borderRadius: radius.md,
    alignItems: 'center',
    gap: space['1'],
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
  },
  pillActive: { backgroundColor: colors.accent.primary, borderColor: colors.accent.primary },
  weekday: { ...typeScale.caption, color: colors.text.muted },
  day: { ...typeScale.subtitle, color: colors.text.primary },
  textActive: { color: colors.accent.onPrimary },
});
