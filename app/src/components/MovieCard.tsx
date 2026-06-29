/**
 * MovieCard — poster + title + caption (rating / age) with an optional kebab menu, used in the
 * Home carousels. Presentational: it takes a movie summary and an onPress.
 */
import { Ionicons } from '@expo/vector-icons';
import { Image } from 'expo-image';
import { Pressable, StyleSheet, Text, View } from 'react-native';

import type { MovieSummary } from '../data/types';
import { colors, radius, space, type as typeScale } from '../theme';

interface MovieCardProps {
  movie: MovieSummary;
  onPress?: (movie: MovieSummary) => void;
  onMenu?: (movie: MovieSummary) => void;
  /** Card width; posters are 2:3. Default 140. */
  width?: number;
}

export function MovieCard({ movie, onPress, onMenu, width = 140 }: MovieCardProps) {
  return (
    <Pressable
      style={({ pressed }) => [{ width }, pressed && styles.pressed]}
      onPress={() => onPress?.(movie)}>
      <View style={[styles.posterWrap, { width, height: width * 1.5 }]}>
        <Image
          source={{ uri: movie.poster_url }}
          style={styles.poster}
          contentFit="cover"
          transition={150}
        />
        {onMenu ? (
          <Pressable style={styles.kebab} hitSlop={8} onPress={() => onMenu(movie)}>
            <Ionicons name="ellipsis-vertical" size={16} color={colors.text.primary} />
          </Pressable>
        ) : null}
      </View>
      <Text style={styles.title} numberOfLines={1}>
        {movie.title}
      </Text>
      <View style={styles.metaRow}>
        <Ionicons name="star" size={12} color={colors.accent.primary} />
        <Text style={styles.meta}>
          {movie.imdb_rating.toFixed(1)} · {movie.age_rating}
        </Text>
      </View>
    </Pressable>
  );
}

const styles = StyleSheet.create({
  pressed: { opacity: 0.85 },
  posterWrap: {
    borderRadius: radius.md,
    overflow: 'hidden',
    backgroundColor: colors.bg.surface,
  },
  poster: { width: '100%', height: '100%' },
  kebab: {
    position: 'absolute',
    top: space['1'],
    right: space['1'],
    backgroundColor: 'rgba(0,0,0,0.45)',
    borderRadius: radius.pill,
    padding: space['1'],
  },
  title: { ...typeScale.bodyBold, color: colors.text.primary, marginTop: space['2'] },
  metaRow: { flexDirection: 'row', alignItems: 'center', gap: space['1'], marginTop: 2 },
  meta: { ...typeScale.caption, color: colors.text.muted },
});
