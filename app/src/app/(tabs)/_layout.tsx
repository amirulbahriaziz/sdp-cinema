import { Ionicons } from '@expo/vector-icons';
import { Tabs } from 'expo-router';

import { colors } from '@/theme';

/**
 * Tab group layout — the app's bottom nav. Uses the standard cross-platform expo-router Tabs
 * (works on web and native); the booking/detail routes push over it from the root Stack.
 *
 * Only Home is a real destination today; Tickets / Saved / Profile from the wireframe are
 * future tabs and intentionally omitted until their screens exist.
 */
export default function TabsLayout() {
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: colors.accent.primary,
        tabBarInactiveTintColor: colors.text.muted,
        tabBarStyle: {
          backgroundColor: colors.bg.surface,
          borderTopColor: colors.border.default,
        },
      }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Home',
          tabBarIcon: ({ color, size }) => (
            <Ionicons name="home" color={color} size={size} />
          ),
        }}
      />
    </Tabs>
  );
}
