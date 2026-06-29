/**
 * RatingStars — a row of accent stars for a 0..5 rating, with half-star support.
 * Presentational; used in MovieCard meta, the rating breakdown header, and review cards.
 */
import { Ionicons } from '@expo/vector-icons';
import { StyleSheet, View } from 'react-native';

import { colors, space } from '../theme';

interface RatingStarsProps {
  /** Rating value on a 0..max scale. */
  rating: number;
  /** Icon size in px (default 16). */
  size?: number;
  /** Number of stars (default 5). */
  max?: number;
}

export function RatingStars({ rating, size = 16, max = 5 }: RatingStarsProps) {
  return (
    <View style={styles.row}>
      {Array.from({ length: max }, (_, i) => {
        const name =
          rating >= i + 1 ? 'star' : rating >= i + 0.5 ? 'star-half' : 'star-outline';
        return <Ionicons key={i} name={name} size={size} color={colors.accent.primary} />;
      })}
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', alignItems: 'center', gap: space['1'] / 2 },
});
