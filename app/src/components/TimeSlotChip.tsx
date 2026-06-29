/**
 * TimeSlotChip — a selectable pill for a single showtime time (e.g. "19:30"). `subLabel` carries
 * a secondary line such as remaining seats. Disabled when the showtime is sold out.
 */
import { Pressable, StyleSheet, Text } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface TimeSlotChipProps {
  label: string;
  subLabel?: string;
  selected?: boolean;
  disabled?: boolean;
  onPress?: () => void;
}

export function TimeSlotChip({ label, subLabel, selected, disabled, onPress }: TimeSlotChipProps) {
  return (
    <Pressable
      accessibilityRole="button"
      disabled={disabled}
      onPress={onPress}
      style={[styles.chip, selected && styles.chipActive, disabled && styles.chipDisabled]}>
      <Text style={[styles.label, selected && styles.labelActive]}>{label}</Text>
      {subLabel ? (
        <Text style={[styles.sub, selected && styles.subActive]}>{subLabel}</Text>
      ) : null}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  chip: {
    paddingVertical: space['2'],
    paddingHorizontal: space['4'],
    borderRadius: radius.sm,
    alignItems: 'center',
    gap: 2,
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
  },
  chipActive: { backgroundColor: colors.accent.primary, borderColor: colors.accent.primary },
  chipDisabled: { opacity: 0.4 },
  label: { ...typeScale.bodyBold, color: colors.text.primary },
  labelActive: { color: colors.accent.onPrimary },
  sub: { ...typeScale.caption, color: colors.text.muted },
  subActive: { color: colors.accent.onPrimary },
});
