/**
 * Home — the discovery entry point: greeting, search, and the New Releases / Popular /
 * Recommended carousels (wireframe + ui-context.md). Movies come from React Query via the
 * source-agnostic data adapter; tapping a poster pushes the Movie Details route.
 *
 * Search is client-driven: a non-empty query swaps the carousels for a flat results grid.
 */
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useMemo, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useMovies } from '@/api/hooks';
import { Carousel, MovieCard, Screen, SearchBar, SectionHeader } from '@/components';
import type { MovieSection, MovieSummary } from '@/data/types';
import { colors, space, type as typeScale } from '@/theme';

const SECTIONS: { key: MovieSection; title: string }[] = [
  { key: 'new_releases', title: 'New Releases' },
  { key: 'popular', title: 'Popular' },
  { key: 'recommended', title: 'Recommended' },
];

export default function HomeScreen() {
  const router = useRouter();
  const [search, setSearch] = useState('');
  const query = search.trim();
  const { data: movies, isLoading, isError } = useMovies(query ? { search: query } : undefined);

  const bySection = useMemo(() => {
    const map: Record<MovieSection, MovieSummary[]> = {
      new_releases: [],
      popular: [],
      recommended: [],
    };
    (movies ?? []).forEach((m) => m.sections.forEach((s) => map[s]?.push(m)));
    return map;
  }, [movies]);

  const openMovie = (movie: MovieSummary) => router.push(`/movie/${movie.id}`);

  return (
    <Screen contentStyle={styles.content}>
      <View style={styles.greeting}>
        <View style={styles.greetingRow}>
          <View style={{ flex: 1 }}>
            <Text style={styles.hello}>Hello, movie lover</Text>
            <Text style={styles.headline}>What do you want to watch?</Text>
          </View>
          <View style={styles.avatar}>
            <Ionicons name="person" size={20} color={colors.text.muted} />
          </View>
        </View>
        <SearchBar value={search} onChangeText={setSearch} />
      </View>

      {isLoading ? (
        <ActivityIndicator color={colors.accent.primary} style={styles.loader} />
      ) : isError ? (
        <Text style={styles.empty}>Couldn&apos;t load movies. Pull to retry.</Text>
      ) : query ? (
        <SearchResults movies={movies ?? []} onPress={openMovie} />
      ) : (
        SECTIONS.map(({ key, title }) =>
          bySection[key].length > 0 ? (
            <Carousel
              key={key}
              title={title}
              data={bySection[key]}
              keyExtractor={(m) => String(m.id)}
              renderItem={(m) => <MovieCard movie={m} onPress={openMovie} onMenu={() => {}} />}
            />
          ) : null,
        )
      )}
    </Screen>
  );
}

function SearchResults({
  movies,
  onPress,
}: {
  movies: MovieSummary[];
  onPress: (movie: MovieSummary) => void;
}) {
  return (
    <View style={styles.results}>
      <SectionHeader title={`Results (${movies.length})`} />
      {movies.length === 0 ? (
        <Text style={styles.empty}>No movies match your search.</Text>
      ) : (
        <View style={styles.grid}>
          {movies.map((m) => (
            <MovieCard key={m.id} movie={m} onPress={onPress} width={GRID_W} />
          ))}
        </View>
      )}
    </View>
  );
}

const GRID_W = 150;

const styles = StyleSheet.create({
  content: { gap: space['6'], paddingTop: space['2'] },
  greeting: { gap: space['4'] },
  greetingRow: { flexDirection: 'row', alignItems: 'center', gap: space['3'] },
  hello: { ...typeScale.caption, color: colors.text.muted },
  headline: { ...typeScale.title, color: colors.text.primary, marginTop: 2 },
  avatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: colors.bg.surface,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loader: { marginTop: space['10'] },
  empty: { ...typeScale.body, color: colors.text.muted, marginTop: space['6'] },
  results: { gap: space['4'] },
  grid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', gap: space['4'] },
});
