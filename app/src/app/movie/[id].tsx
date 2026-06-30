/**
 * Movie Details — hero trailer placeholder, title / tags / rating, and two tabs:
 * "Movie Details" (synopsis + cast/crew) and "Ratings & Reviews" (breakdown + review cards).
 * A sticky "Book Ticket" CTA pushes the booking flow (wireframe: Movie info -> Book Ticket).
 *
 * Data is read through React Query (`useMovie`) so the screen is source-agnostic (live | mock).
 */
import { Ionicons } from '@expo/vector-icons';
import { Image } from 'expo-image';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeBack } from '@/lib/use-safe-back';
import { useState } from 'react';
import { ActivityIndicator, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import * as WebBrowser from 'expo-web-browser';

import { useMovie } from '@/api/hooks';
import { PrimaryButton, RatingBreakdown, RatingStars, ReviewCard } from '@/components';
import type { MovieDetail } from '@/data/types';
import { colors, radius, space, type as typeScale } from '@/theme';

type TabKey = 'details' | 'reviews';

/** Minutes -> "1h 49m". */
function formatRuntime(min: number): string {
  const h = Math.floor(min / 60);
  const m = min % 60;
  return h > 0 ? `${h}h ${m}m` : `${m}m`;
}

export default function MovieDetailScreen() {
  const router = useRouter();
  const goBack = useSafeBack();
  const { id } = useLocalSearchParams<{ id: string }>();
  const movieId = Number(id);
  const [tab, setTab] = useState<TabKey>('details');
  const { data: movie, isLoading, isError } = useMovie(movieId);

  return (
    <SafeAreaView style={styles.safe} edges={['top', 'left', 'right']}>
      <Header onBack={goBack} />

      {isLoading ? (
        <ActivityIndicator color={colors.accent.primary} style={styles.loader} />
      ) : isError || !movie ? (
        <View style={styles.center}>
          <Text style={styles.error}>Couldn&apos;t load this movie.</Text>
        </View>
      ) : (
        <>
          <ScrollView
            style={styles.flex}
            contentContainerStyle={styles.scroll}
            showsVerticalScrollIndicator={false}>
            <Trailer movie={movie} />

            <View style={styles.body}>
              <Text style={styles.title}>{movie.title}</Text>
              <View style={styles.metaRow}>
                <View style={styles.ratingPill}>
                  <Ionicons name="star" size={14} color={colors.accent.primary} />
                  <Text style={styles.ratingText}>{movie.imdb_rating.toFixed(1)}</Text>
                </View>
                <Text style={styles.metaText}>{movie.age_rating}</Text>
                <Text style={styles.metaDot}>·</Text>
                <Text style={styles.metaText}>{formatRuntime(movie.duration_min)}</Text>
                <Text style={styles.metaDot}>·</Text>
                <Text style={styles.metaText}>{movie.release_date.slice(0, 4)}</Text>
              </View>

              <View style={styles.tags}>
                {movie.genres.map((g) => (
                  <View key={g} style={styles.tag}>
                    <Text style={styles.tagText}>{g}</Text>
                  </View>
                ))}
              </View>

              <View style={styles.tabBar}>
                <TabButton label="Movie Details" active={tab === 'details'} onPress={() => setTab('details')} />
                <TabButton label="Ratings & Reviews" active={tab === 'reviews'} onPress={() => setTab('reviews')} />
              </View>

              {tab === 'details' ? <DetailsTab movie={movie} /> : <ReviewsTab movie={movie} />}
            </View>
          </ScrollView>

          <View style={styles.footer}>
            <PrimaryButton label="Book Ticket" onPress={() => router.push(`/booking/${movie.id}`)} />
          </View>
        </>
      )}
    </SafeAreaView>
  );
}

function Header({ onBack }: { onBack: () => void }) {
  return (
    <View style={styles.header}>
      <Pressable style={styles.iconBtn} hitSlop={8} onPress={onBack}>
        <Ionicons name="chevron-back" size={24} color={colors.text.primary} />
      </Pressable>
      <Text style={styles.headerTitle}>Details</Text>
      <Pressable style={styles.iconBtn} hitSlop={8}>
        <Ionicons name="bookmark-outline" size={22} color={colors.text.primary} />
      </Pressable>
    </View>
  );
}

function Trailer({ movie }: { movie: MovieDetail }) {
  const openTrailer = () => {
    if (movie.trailer_url) WebBrowser.openBrowserAsync(movie.trailer_url).catch(() => {});
  };
  return (
    <Pressable style={styles.trailer} onPress={openTrailer}>
      <Image source={{ uri: movie.poster_url }} style={styles.trailerImg} contentFit="cover" transition={150} />
      <View style={styles.trailerScrim} />
      <View style={styles.playBtn}>
        <Ionicons name="play" size={28} color={colors.accent.onPrimary} />
      </View>
      <Text style={styles.trailerLabel}>Watch Trailer</Text>
    </Pressable>
  );
}

function TabButton({ label, active, onPress }: { label: string; active: boolean; onPress: () => void }) {
  return (
    <Pressable style={styles.tab} onPress={onPress}>
      <Text style={[styles.tabLabel, active && styles.tabLabelActive]}>{label}</Text>
      <View style={[styles.tabUnderline, active && styles.tabUnderlineActive]} />
    </Pressable>
  );
}

function Field({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.field}>
      <Text style={styles.fieldLabel}>{label}</Text>
      <Text style={styles.fieldValue}>{value}</Text>
    </View>
  );
}

function DetailsTab({ movie }: { movie: MovieDetail }) {
  return (
    <View style={styles.tabContent}>
      <Text style={styles.synopsis}>{movie.synopsis}</Text>
      <Field label="Director" value={movie.director} />
      <Field label="Cast" value={movie.casts.join(', ')} />
      <Field label="Writers" value={movie.writers.join(', ')} />
      <Field label="Release date" value={movie.release_date} />
    </View>
  );
}

function ReviewsTab({ movie }: { movie: MovieDetail }) {
  return (
    <View style={styles.tabContent}>
      <RatingBreakdown summary={movie.rating_summary} />
      <View style={styles.reviewsHead}>
        <Text style={styles.sectionTitle}>Reviews</Text>
        <RatingStars rating={movie.rating_summary.average} size={14} />
      </View>
      {movie.reviews.map((r) => (
        <ReviewCard key={r.id} review={r} />
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg.base },
  flex: { flex: 1 },
  scroll: { paddingBottom: space['8'] },
  loader: { marginTop: space['12'] },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  error: { ...typeScale.body, color: colors.text.muted },

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

  trailer: {
    marginHorizontal: space['4'],
    height: 200,
    borderRadius: radius.md,
    overflow: 'hidden',
    backgroundColor: colors.bg.surface,
    alignItems: 'center',
    justifyContent: 'center',
  },
  trailerImg: { ...StyleSheet.absoluteFill, opacity: 0.5 },
  trailerScrim: { ...StyleSheet.absoluteFill, backgroundColor: 'rgba(0,0,0,0.35)' },
  playBtn: {
    width: 60,
    height: 60,
    borderRadius: radius.pill,
    backgroundColor: colors.accent.primary,
    alignItems: 'center',
    justifyContent: 'center',
  },
  trailerLabel: { ...typeScale.captionBold, color: colors.text.primary, marginTop: space['2'] },

  body: { paddingHorizontal: space['4'], paddingTop: space['5'], gap: space['3'] },
  title: { ...typeScale.display, color: colors.text.primary },
  metaRow: { flexDirection: 'row', alignItems: 'center', gap: space['2'] },
  ratingPill: { flexDirection: 'row', alignItems: 'center', gap: space['1'] },
  ratingText: { ...typeScale.captionBold, color: colors.text.primary },
  metaText: { ...typeScale.caption, color: colors.text.muted },
  metaDot: { color: colors.text.muted },
  tags: { flexDirection: 'row', flexWrap: 'wrap', gap: space['2'] },
  tag: {
    borderWidth: 1,
    borderColor: colors.border.default,
    borderRadius: radius.pill,
    paddingHorizontal: space['3'],
    paddingVertical: space['1'],
  },
  tagText: { ...typeScale.caption, color: colors.text.primary },

  tabBar: {
    flexDirection: 'row',
    gap: space['6'],
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: colors.border.default,
    marginTop: space['2'],
  },
  tab: { paddingBottom: space['2'], gap: space['2'] },
  tabLabel: { ...typeScale.bodyBold, color: colors.text.muted },
  tabLabelActive: { color: colors.text.primary },
  tabUnderline: { height: 2, borderRadius: radius.pill, backgroundColor: 'transparent' },
  tabUnderlineActive: { backgroundColor: colors.accent.primary },

  tabContent: { gap: space['4'], paddingTop: space['4'] },
  synopsis: { ...typeScale.body, color: colors.text.muted },
  field: { gap: space['1'] },
  fieldLabel: { ...typeScale.caption, color: colors.text.muted },
  fieldValue: { ...typeScale.body, color: colors.text.primary },

  reviewsHead: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  sectionTitle: { ...typeScale.subtitle, color: colors.text.primary },

  footer: {
    paddingHorizontal: space['4'],
    paddingTop: space['3'],
    paddingBottom: space['4'],
    backgroundColor: colors.bg.surface,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: colors.border.default,
  },
});
