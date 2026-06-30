/**
 * ResultScreen — the centered success state used by the booking Confirmation screen: a large
 * badge icon, a "Congratulations" headline, supporting copy, an optional details slot (the
 * ticket card), and one or two stacked actions (View ticket / Main menu).
 */
import { Ionicons } from '@expo/vector-icons';
import type { ReactNode } from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';
import { PrimaryButton } from './PrimaryButton';

interface ResultAction {
  label: string;
  onPress: () => void;
  variant?: 'primary' | 'ghost';
}

interface ResultScreenProps {
  title: string;
  message: string;
  details?: ReactNode;
  actions: ResultAction[];
  icon?: keyof typeof Ionicons.glyphMap;
}

export function ResultScreen({
  title,
  message,
  details,
  actions,
  icon = 'checkmark-circle',
}: ResultScreenProps) {
  return (
    <View style={styles.container}>
      <ScrollView
        contentContainerStyle={styles.scroll}
        showsVerticalScrollIndicator={false}>
        <View style={styles.badge}>
          <Ionicons name={icon} size={64} color={colors.state.success} />
        </View>
        <Text style={styles.title}>{title}</Text>
        <Text style={styles.message}>{message}</Text>
        {details ? <View style={styles.details}>{details}</View> : null}
      </ScrollView>
      <View style={styles.actions}>
        {actions.map((a) => (
          <PrimaryButton
            key={a.label}
            label={a.label}
            variant={a.variant}
            onPress={a.onPress}
          />
        ))}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'space-between' },
  scroll: {
    flexGrow: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: space['8'],
    gap: space['3'],
  },
  badge: {
    width: 112,
    height: 112,
    borderRadius: radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.bg.surface,
    borderWidth: 1,
    borderColor: colors.border.default,
    marginBottom: space['2'],
  },
  title: { ...typeScale.display, color: colors.text.primary, textAlign: 'center' },
  message: {
    ...typeScale.body,
    color: colors.text.muted,
    textAlign: 'center',
    paddingHorizontal: space['6'],
  },
  details: { alignSelf: 'stretch', marginTop: space['4'] },
  actions: { gap: space['3'], paddingTop: space['4'] },
});
