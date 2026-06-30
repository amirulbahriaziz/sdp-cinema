/**
 * Dropdown — a labeled select rendered as a tappable field that opens a bottom-sheet list
 * (Location, Cinema Hall on the Ticket Booking screen). Controlled: parent owns the value.
 */
import { Ionicons } from '@expo/vector-icons';
import { Modal, Pressable, StyleSheet, Text, View } from 'react-native';
import { useState } from 'react';

import { colors, radius, space, type as typeScale } from '../theme';

export interface DropdownOption<T extends string | number> {
  value: T;
  label: string;
  sublabel?: string;
}

interface DropdownProps<T extends string | number> {
  label: string;
  value: T | null;
  options: DropdownOption<T>[];
  onChange: (value: T) => void;
  placeholder?: string;
}

export function Dropdown<T extends string | number>({
  label,
  value,
  options,
  onChange,
  placeholder = 'Select…',
}: DropdownProps<T>) {
  const [open, setOpen] = useState(false);
  const selected = options.find((o) => o.value === value);

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>{label}</Text>
      <Pressable
        accessibilityRole="button"
        style={styles.field}
        onPress={() => options.length > 0 && setOpen(true)}>
        <Ionicons name="location-outline" size={18} color={colors.text.muted} />
        <Text style={[styles.fieldText, !selected && styles.placeholder]} numberOfLines={1}>
          {selected?.label ?? placeholder}
        </Text>
        <Ionicons name="chevron-down" size={18} color={colors.text.muted} />
      </Pressable>

      <Modal visible={open} transparent animationType="fade" onRequestClose={() => setOpen(false)}>
        <Pressable style={styles.backdrop} onPress={() => setOpen(false)}>
          <Pressable style={styles.sheet}>
            <Text style={styles.sheetTitle}>{label}</Text>
            {options.map((opt) => {
              const active = opt.value === value;
              return (
                <Pressable
                  key={String(opt.value)}
                  style={[styles.option, active && styles.optionActive]}
                  onPress={() => {
                    onChange(opt.value);
                    setOpen(false);
                  }}>
                  <View style={styles.optionText}>
                    <Text style={styles.optionLabel}>{opt.label}</Text>
                    {opt.sublabel ? <Text style={styles.optionSub}>{opt.sublabel}</Text> : null}
                  </View>
                  {active ? (
                    <Ionicons name="checkmark" size={18} color={colors.accent.primary} />
                  ) : null}
                </Pressable>
              );
            })}
          </Pressable>
        </Pressable>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { gap: space['2'], flex: 1 },
  label: { ...typeScale.captionBold, color: colors.text.muted },
  field: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: space['2'],
    height: 48,
    paddingHorizontal: space['3'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
  },
  fieldText: { ...typeScale.body, color: colors.text.primary, flex: 1 },
  placeholder: { color: colors.text.muted },
  backdrop: { flex: 1, backgroundColor: 'rgba(0,0,0,0.6)', justifyContent: 'flex-end' },
  sheet: {
    backgroundColor: colors.bg.elevated,
    borderTopLeftRadius: radius.lg,
    borderTopRightRadius: radius.lg,
    padding: space['5'],
    gap: space['2'],
  },
  sheetTitle: { ...typeScale.subtitle, color: colors.text.primary, marginBottom: space['2'] },
  option: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: space['3'],
    paddingHorizontal: space['3'],
    borderRadius: radius.md,
  },
  optionActive: { backgroundColor: colors.bg.surface },
  optionText: { flex: 1, gap: 2 },
  optionLabel: { ...typeScale.body, color: colors.text.primary },
  optionSub: { ...typeScale.caption, color: colors.text.muted },
});
