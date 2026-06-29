/**
 * Ticket Booking — step 1 of the booking flow (Home -> Movie info -> Book Ticket).
 *
 * Lets the user pick a concrete showtime: Location + Cinema Hall dropdowns, a date strip, and
 * time-slot chips, plus the two price-tier cards (Classic / Premium) showing each tier's real
 * MIN..MAX seat-price range. A seat legend previews the seat colors. All options are derived
 * from `useShowtimes({ movie_id })` so the screen is source-agnostic (live | mock).
 *
 * Selecting a time slot + "Select Seats" snapshots the showtime into the booking draft and
 * pushes the Seat Selection screen.
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useMemo, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useShowtimes } from '@/api/hooks';
import {
  DateStrip,
  Dropdown,
  type DropdownOption,
  PriceTierCard,
  PrimaryButton,
  Screen,
  SeatLegend,
  StepHeader,
  TimeSlotChip,
} from '@/components';
import type { Showtime } from '@/data/types';
import { formatTime } from '@/lib/datetime';
import { useBookingStore } from '@/store/booking';
import { colors, space, type as typeScale } from '@/theme';

/** Stable key for a cinema+hall pair (one dropdown option). */
const hallKey = (s: Showtime) => `${s.cinema.id}-${s.hall.id}`;

export default function TicketBookingScreen() {
  const router = useRouter();
  const { movieId } = useLocalSearchParams<{ movieId: string }>();
  const beginBooking = useBookingStore((s) => s.beginBooking);
  const { data: showtimes, isLoading, isError } = useShowtimes({ movie_id: Number(movieId) });

  const [city, setCity] = useState<string | null>(null);
  const [hall, setHall] = useState<string | null>(null);
  const [date, setDate] = useState<string | null>(null);
  const [tierId, setTierId] = useState<number | null>(null);
  const [showtimeId, setShowtimeId] = useState<number | null>(null);

  const all = useMemo(() => showtimes ?? [], [showtimes]);

  // Distinct cities (Location dropdown).
  const cityOptions: DropdownOption<string>[] = useMemo(() => {
    const seen = new Map<string, string>();
    all.forEach((s) => seen.set(s.cinema.city, s.cinema.city));
    return [...seen.values()].map((c) => ({ value: c, label: c }));
  }, [all]);

  // Cinema+hall options within the chosen city.
  const hallOptions: DropdownOption<string>[] = useMemo(() => {
    const seen = new Map<string, DropdownOption<string>>();
    all
      .filter((s) => s.cinema.city === city)
      .forEach((s) =>
        seen.set(hallKey(s), {
          value: hallKey(s),
          label: s.cinema.name,
          sublabel: s.hall.name,
        }),
      );
    return [...seen.values()];
  }, [all, city]);

  // Distinct dates for the chosen city+hall.
  const dateOptions = useMemo(() => {
    const seen = new Set<string>();
    all
      .filter((s) => s.cinema.city === city && hallKey(s) === hall)
      .forEach((s) => seen.add(s.starts_at.slice(0, 10)));
    return [...seen].sort();
  }, [all, city, hall]);

  // Two tier cards (Classic / Premium) — distinct tiers across this movie's showtimes.
  const tiers = useMemo(() => {
    const seen = new Map<number, Showtime['tier']>();
    all.forEach((s) => seen.set(s.tier.id, s.tier));
    return [...seen.values()].sort((a, b) => a.id - b.id);
  }, [all]);

  // Time slots matching every active filter (tier optional).
  const slots = useMemo(
    () =>
      all
        .filter(
          (s) =>
            s.cinema.city === city &&
            hallKey(s) === hall &&
            s.starts_at.slice(0, 10) === date &&
            (tierId == null || s.tier.id === tierId),
        )
        .sort((a, b) => a.starts_at.localeCompare(b.starts_at)),
    [all, city, hall, date, tierId],
  );

  // Default the cascade once data arrives / when a parent selection changes.
  useEffect(() => {
    if (city == null && cityOptions.length) setCity(cityOptions[0].value);
  }, [cityOptions, city]);
  useEffect(() => {
    if (hallOptions.length && !hallOptions.some((o) => o.value === hall)) setHall(hallOptions[0].value);
  }, [hallOptions, hall]);
  useEffect(() => {
    if (dateOptions.length && !dateOptions.includes(date ?? '')) setDate(dateOptions[0]);
  }, [dateOptions, date]);
  // Drop a stale time-slot when filters change.
  useEffect(() => {
    if (showtimeId != null && !slots.some((s) => s.id === showtimeId)) setShowtimeId(null);
  }, [slots, showtimeId]);

  const selected = slots.find((s) => s.id === showtimeId);

  const onProceed = () => {
    if (!selected) return;
    beginBooking(selected);
    router.push(`/booking/seats/${selected.id}`);
  };

  return (
    <Screen
      header={<StepHeader title="Ticket Booking" onBack={() => router.back()} />}
      footer={
        <PrimaryButton label="Select Seats" disabled={!selected} onPress={onProceed} />
      }>
      {isLoading ? (
        <ActivityIndicator color={colors.accent.primary} style={styles.loader} />
      ) : isError || all.length === 0 ? (
        <View style={styles.center}>
          <Text style={styles.error}>No showtimes available for this movie.</Text>
        </View>
      ) : (
        <View style={styles.content}>
          <View style={styles.dropRow}>
            <Dropdown label="Location" value={city} options={cityOptions} onChange={(v) => setCity(v)} />
            <Dropdown label="Cinema Hall" value={hall} options={hallOptions} onChange={(v) => setHall(v)} />
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Price Tier</Text>
            <View style={styles.tierRow}>
              {tiers.map((t) => (
                <PriceTierCard
                  key={t.id}
                  name={t.name}
                  priceMin={t.price_min}
                  priceMax={t.price_max}
                  currency={t.currency}
                  selected={tierId === t.id}
                  onPress={() => setTierId(tierId === t.id ? null : t.id)}
                />
              ))}
            </View>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Date</Text>
            <DateStrip dates={dateOptions} value={date} onChange={setDate} />
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Showtime</Text>
            {slots.length === 0 ? (
              <Text style={styles.empty}>No slots for this selection.</Text>
            ) : (
              <View style={styles.slotRow}>
                {slots.map((s) => (
                  <TimeSlotChip
                    key={s.id}
                    label={formatTime(s.starts_at)}
                    subLabel={`${s.seats_available} left`}
                    selected={showtimeId === s.id}
                    disabled={s.seats_available === 0}
                    onPress={() => setShowtimeId(s.id)}
                  />
                ))}
              </View>
            )}
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Seat Legend</Text>
            <SeatLegend />
          </View>
        </View>
      )}
    </Screen>
  );
}

const styles = StyleSheet.create({
  loader: { marginTop: space['12'] },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: space['6'] },
  error: { ...typeScale.body, color: colors.text.muted, textAlign: 'center' },
  content: { gap: space['6'], paddingTop: space['4'] },
  dropRow: { flexDirection: 'row', gap: space['3'] },
  section: { gap: space['3'] },
  sectionTitle: { ...typeScale.subtitle, color: colors.text.primary },
  tierRow: { flexDirection: 'row', gap: space['3'] },
  slotRow: { flexDirection: 'row', flexWrap: 'wrap', gap: space['2'] },
  empty: { ...typeScale.body, color: colors.text.muted },
});
