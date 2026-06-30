/**
 * BottomNav — the app's primary tab bar (Home / Tickets / Saved / Profile). Presentational:
 * the parent passes the active key and an onChange. (The router may also drive these tabs;
 * this component keeps the styling consistent with the dark theme.)
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { colors, space, type as typeScale } from '../theme';

export type NavKey = 'home' | 'tickets' | 'saved' | 'profile';

const ITEMS: { key: NavKey; label: string; icon: keyof typeof Ionicons.glyphMap }[] = [
  { key: 'home', label: 'Home', icon: 'home' },
  { key: 'tickets', label: 'Tickets', icon: 'ticket' },
  { key: 'saved', label: 'Saved', icon: 'bookmark' },
  { key: 'profile', label: 'Profile', icon: 'person' },
];

interface BottomNavProps {
  active: NavKey;
  onChange: (key: NavKey) => void;
}

export function BottomNav({ active, onChange }: BottomNavProps) {
  const insets = useSafeAreaInsets();
  return (
    <View style={[styles.bar, { paddingBottom: insets.bottom || space['2'] }]}>
      {ITEMS.map((item) => {
        const isActive = item.key === active;
        const color = isActive ? colors.accent.primary : colors.text.muted;
        return (
          <Pressable key={item.key} style={styles.item} onPress={() => onChange(item.key)}>
            <Ionicons name={isActive ? item.icon : (`${item.icon}-outline` as keyof typeof Ionicons.glyphMap)} size={24} color={color} />
            <Text style={[styles.label, { color }]}>{item.label}</Text>
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
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: colors.border.default,
    paddingTop: space['2'],
  },
  item: { flex: 1, alignItems: 'center', gap: 2 },
  label: { ...typeScale.caption },
});
