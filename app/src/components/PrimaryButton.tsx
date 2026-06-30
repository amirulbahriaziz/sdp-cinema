/**
 * PrimaryButton — full-width accent CTA (Book Ticket, Proceed, Confirm). Supports a disabled
 * and a loading state; `variant="ghost"` gives the outline/secondary action (e.g. Cancel).
 */
import { ActivityIndicator, Pressable, StyleSheet, Text, type ViewStyle } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface PrimaryButtonProps {
  label: string;
  onPress?: () => void;
  disabled?: boolean;
  loading?: boolean;
  variant?: 'primary' | 'ghost';
  style?: ViewStyle;
}

export function PrimaryButton({
  label,
  onPress,
  disabled,
  loading,
  variant = 'primary',
  style,
}: PrimaryButtonProps) {
  const ghost = variant === 'ghost';
  const isDisabled = disabled || loading;
  return (
    <Pressable
      accessibilityRole="button"
      disabled={isDisabled}
      onPress={onPress}
      style={({ pressed }) => [
        styles.base,
        ghost ? styles.ghost : styles.primary,
        isDisabled && styles.disabled,
        pressed && !isDisabled && styles.pressed,
        style,
      ]}>
      {loading ? (
        <ActivityIndicator color={ghost ? colors.text.primary : colors.accent.onPrimary} />
      ) : (
        <Text style={[styles.label, ghost && styles.ghostLabel]}>{label}</Text>
      )}
    </Pressable>
  );
}

const styles = StyleSheet.create({
  base: {
    height: 52,
    borderRadius: radius.md,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: space['4'],
  },
  primary: { backgroundColor: colors.accent.primary },
  ghost: { backgroundColor: 'transparent', borderWidth: 1, borderColor: colors.border.default },
  disabled: { opacity: 0.45 },
  pressed: { opacity: 0.85 },
  label: { ...typeScale.bodyBold, color: colors.accent.onPrimary },
  ghostLabel: { color: colors.text.primary },
});
