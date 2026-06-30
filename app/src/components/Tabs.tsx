/**
 * Tabs — segmented control used for in-screen tab switches (Movie Details / Ratings & Reviews;
 * F&B Combo / Food-Snacks / Beverages). Controlled: parent owns the active value.
 */
import { Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface TabOption<T extends string> {
  value: T;
  label: string;
}

interface TabsProps<T extends string> {
  options: TabOption<T>[];
  value: T;
  onChange: (value: T) => void;
}

export function Tabs<T extends string>({ options, value, onChange }: TabsProps<T>) {
  return (
    <View style={styles.bar}>
      {options.map((opt) => {
        const active = opt.value === value;
        return (
          <Pressable
            key={opt.value}
            style={[styles.tab, active && styles.tabActive]}
            onPress={() => onChange(opt.value)}>
            <Text style={[styles.label, active && styles.labelActive]}>{opt.label}</Text>
          </Pressable>
        );
      })}
    </View>
  );
}

const styles = StyleSheet.create({
  bar: {
    flexDirection: 'row',
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    padding: space['1'],
    gap: space['1'],
  },
  tab: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: space['2'],
    borderRadius: radius.sm,
  },
  tabActive: { backgroundColor: colors.bg.elevated },
  label: { ...typeScale.captionBold, color: colors.text.muted },
  labelActive: { color: colors.text.primary },
});
