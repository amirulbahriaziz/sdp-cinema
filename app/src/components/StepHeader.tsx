/**
 * StepHeader — the back-button + centered title row shared by the booking-flow screens. An
 * optional right-side action (e.g. "Skip" on the F&B step) sits opposite the back button.
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface StepHeaderProps {
  title: string;
  onBack?: () => void;
  /** Optional right-aligned text action (label + handler), e.g. Skip. */
  action?: { label: string; onPress: () => void };
}

export function StepHeader({ title, onBack, action }: StepHeaderProps) {
  return (
    <View style={styles.header}>
      <Pressable style={styles.iconBtn} hitSlop={8} onPress={onBack}>
        <Ionicons name="chevron-back" size={24} color={colors.text.primary} />
      </Pressable>
      <Text style={styles.title}>{title}</Text>
      {action ? (
        <Pressable style={styles.action} hitSlop={8} onPress={action.onPress}>
          <Text style={styles.actionText}>{action.label}</Text>
        </Pressable>
      ) : (
        <View style={styles.iconBtn} />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: space['4'],
    paddingVertical: space['2'],
  },
  title: { ...typeScale.subtitle, color: colors.text.primary },
  iconBtn: {
    width: 40,
    height: 40,
    borderRadius: radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.bg.surface,
  },
  action: { minWidth: 40, height: 40, alignItems: 'flex-end', justifyContent: 'center' },
  actionText: { ...typeScale.bodyBold, color: colors.accent.primary },
});
