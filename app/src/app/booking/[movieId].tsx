/**
 * Ticket Booking — PLACEHOLDER (discovery-slice handoff).
 *
 * The discovery flow ends with "Book Ticket", which routes here (Home -> Movie info -> Book).
 * The real booking flow (showtime select -> seat map -> F&B -> summary -> payment) is built in
 * a later slice and will replace this screen. This stub only keeps navigation resolvable.
 */
import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';

import { colors, radius, space, type as typeScale } from '@/theme';

export default function BookingPlaceholderScreen() {
  const router = useRouter();
  const { movieId } = useLocalSearchParams<{ movieId: string }>();
  return (
    <SafeAreaView style={styles.safe} edges={['top', 'left', 'right']}>
      <View style={styles.header}>
        <Pressable style={styles.iconBtn} hitSlop={8} onPress={() => router.back()}>
          <Ionicons name="chevron-back" size={24} color={colors.text.primary} />
        </Pressable>
        <Text style={styles.headerTitle}>Ticket Booking</Text>
        <View style={styles.iconBtn} />
      </View>
      <View style={styles.center}>
        <Ionicons name="ticket-outline" size={48} color={colors.text.muted} />
        <Text style={styles.title}>Booking flow coming next</Text>
        <Text style={styles.sub}>Showtime, seats, F&amp;B and payment for movie #{movieId}.</Text>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg.base },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: space['4'],
    paddingVertical: space['2'],
  },
  headerTitle: { ...typeScale.subtitle, color: colors.text.primary },
  iconBtn: {
    width: 40,
    height: 40,
    borderRadius: radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.bg.surface,
  },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: space['3'], padding: space['6'] },
  title: { ...typeScale.title, color: colors.text.primary },
  sub: { ...typeScale.body, color: colors.text.muted, textAlign: 'center' },
});
