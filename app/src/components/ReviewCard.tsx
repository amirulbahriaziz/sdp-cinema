/**
 * ReviewCard — a single user review: avatar initial, name + date, star rating, title and body.
 * Presentational; rendered in the movie detail "Ratings & Reviews" list.
 */
import { StyleSheet, Text, View } from 'react-native';

import type { Review } from '../data/types';
import { colors, radius, space, type as typeScale } from '../theme';
import { RatingStars } from './RatingStars';

interface ReviewCardProps {
  review: Review;
}

/** "2026-06-20T10:00:00+08:00" -> "20 Jun 2026". */
function formatDate(iso: string): string {
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return '';
  return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

export function ReviewCard({ review }: ReviewCardProps) {
  const initial = review.user_name.trim().charAt(0).toUpperCase() || '?';
  return (
    <View style={styles.card}>
      <View style={styles.head}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{initial}</Text>
        </View>
        <View style={styles.headText}>
          <Text style={styles.name} numberOfLines={1}>
            {review.user_name}
          </Text>
          <Text style={styles.date}>{formatDate(review.created_at)}</Text>
        </View>
        <RatingStars rating={review.rating} size={13} />
      </View>
      {review.title ? <Text style={styles.title}>{review.title}</Text> : null}
      <Text style={styles.body}>{review.body}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    padding: space['4'],
    gap: space['2'],
  },
  head: { flexDirection: 'row', alignItems: 'center', gap: space['3'] },
  avatar: {
    width: 36,
    height: 36,
    borderRadius: radius.pill,
    backgroundColor: colors.bg.elevated,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: { ...typeScale.bodyBold, color: colors.text.primary },
  headText: { flex: 1 },
  name: { ...typeScale.bodyBold, color: colors.text.primary },
  date: { ...typeScale.caption, color: colors.text.muted },
  title: { ...typeScale.bodyBold, color: colors.text.primary },
  body: { ...typeScale.body, color: colors.text.muted },
});
