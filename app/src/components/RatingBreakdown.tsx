/**
 * RatingBreakdown — the "Ratings & Reviews" header: a large average score with stars and
 * total count on the left, and proportional 5..1 star distribution bars on the right.
 * Presentational; fed a `RatingSummary` from the movie detail.
 */
import { StyleSheet, Text, View } from 'react-native';

import type { RatingSummary } from '../data/types';
import { colors, radius, space, type as typeScale } from '../theme';
import { RatingStars } from './RatingStars';

interface RatingBreakdownProps {
  summary: RatingSummary;
}

const STARS = ['5', '4', '3', '2', '1'] as const;

export function RatingBreakdown({ summary }: RatingBreakdownProps) {
  const total = summary.count || 1;
  return (
    <View style={styles.wrap}>
      <View style={styles.scoreCol}>
        <Text style={styles.average}>{summary.average.toFixed(1)}</Text>
        <RatingStars rating={summary.average} size={14} />
        <Text style={styles.count}>{summary.count} reviews</Text>
      </View>
      <View style={styles.bars}>
        {STARS.map((star) => {
          const value = summary.breakdown[star] ?? 0;
          const pct = Math.round((value / total) * 100);
          return (
            <View key={star} style={styles.barRow}>
              <Text style={styles.barLabel}>{star}</Text>
              <View style={styles.track}>
                <View style={[styles.fill, { width: `${pct}%` }]} />
              </View>
              <Text style={styles.barValue}>{value}</Text>
            </View>
          );
        })}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    flexDirection: 'row',
    gap: space['5'],
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    padding: space['4'],
  },
  scoreCol: { alignItems: 'center', justifyContent: 'center', gap: space['1'] },
  average: { ...typeScale.display, fontSize: 40, lineHeight: 44, color: colors.text.primary },
  count: { ...typeScale.caption, color: colors.text.muted },
  bars: { flex: 1, justifyContent: 'center', gap: space['2'] },
  barRow: { flexDirection: 'row', alignItems: 'center', gap: space['2'] },
  barLabel: { ...typeScale.caption, color: colors.text.muted, width: 10, textAlign: 'center' },
  track: {
    flex: 1,
    height: 6,
    borderRadius: radius.pill,
    backgroundColor: colors.bg.elevated,
    overflow: 'hidden',
  },
  fill: { height: '100%', borderRadius: radius.pill, backgroundColor: colors.accent.primary },
  barValue: { ...typeScale.caption, color: colors.text.muted, width: 24, textAlign: 'right' },
});
