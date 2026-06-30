/**
 * PaymentMethodRow — a selectable payment option (Debit card / Bank Transfer / Crypto) on the
 * Payment Method screen. Leading icon, title + subtitle, trailing radio indicator. Pure and
 * theme-driven; selection state is owned by the screen (the booking draft).
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface PaymentMethodRowProps {
  icon: keyof typeof Ionicons.glyphMap;
  title: string;
  subtitle: string;
  selected: boolean;
  onPress: () => void;
}

export function PaymentMethodRow({
  icon,
  title,
  subtitle,
  selected,
  onPress,
}: PaymentMethodRowProps) {
  return (
    <Pressable
      accessibilityRole="radio"
      accessibilityState={{ selected }}
      onPress={onPress}
      style={({ pressed }) => [
        styles.row,
        selected && styles.rowSelected,
        pressed && styles.pressed,
      ]}>
      <View style={styles.iconBox}>
        <Ionicons name={icon} size={20} color={colors.text.primary} />
      </View>
      <View style={styles.textCol}>
        <Text style={styles.title}>{title}</Text>
        <Text style={styles.subtitle}>{subtitle}</Text>
      </View>
      <Ionicons
        name={selected ? 'radio-button-on' : 'radio-button-off'}
        size={22}
        color={selected ? colors.accent.primary : colors.text.muted}
      />
    </Pressable>
  );
}

const styles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: space['3'],
    padding: space['4'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.surface,
    borderWidth: 1,
    borderColor: colors.border.default,
  },
  rowSelected: { borderColor: colors.accent.primary },
  pressed: { opacity: 0.85 },
  iconBox: {
    width: 40,
    height: 40,
    borderRadius: radius.sm,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.bg.elevated,
  },
  textCol: { flex: 1, gap: 2 },
  title: { ...typeScale.bodyBold, color: colors.text.primary },
  subtitle: { ...typeScale.caption, color: colors.text.muted },
});
