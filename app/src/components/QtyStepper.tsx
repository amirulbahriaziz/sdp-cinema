/**
 * QtyStepper — minus / value / plus control for F&B quantities. Clamped at `min` (default 0).
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface QtyStepperProps {
  value: number;
  onChange: (next: number) => void;
  min?: number;
  max?: number;
}

export function QtyStepper({ value, onChange, min = 0, max = 99 }: QtyStepperProps) {
  const dec = () => onChange(Math.max(min, value - 1));
  const inc = () => onChange(Math.min(max, value + 1));
  return (
    <View style={styles.wrap}>
      <Pressable
        style={[styles.btn, value <= min && styles.btnDisabled]}
        disabled={value <= min}
        onPress={dec}
        hitSlop={6}>
        <Ionicons name="remove" size={18} color={colors.text.primary} />
      </Pressable>
      <Text style={styles.value}>{value}</Text>
      <Pressable
        style={[styles.btn, value >= max && styles.btnDisabled]}
        disabled={value >= max}
        onPress={inc}
        hitSlop={6}>
        <Ionicons name="add" size={18} color={colors.text.primary} />
      </Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: { flexDirection: 'row', alignItems: 'center', gap: space['3'] },
  btn: {
    width: 32,
    height: 32,
    borderRadius: radius.sm,
    backgroundColor: colors.bg.elevated,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
    alignItems: 'center',
    justifyContent: 'center',
  },
  btnDisabled: { opacity: 0.4 },
  value: { ...typeScale.bodyBold, color: colors.text.primary, minWidth: 20, textAlign: 'center' },
});
