/**
 * FoodItemCard — a single F&B item row (poster, name, description, price) with a QtyStepper
 * bound to the booking draft. Honors `discount_price`: the unit price added to the draft is
 * `discount_price ?? price`, and the original is struck through when discounted.
 */
import { Image } from 'expo-image';
import { StyleSheet, Text, View } from 'react-native';

import type { FoodItem } from '../data/types';
import { colors, formatMoney, radius, space, type as typeScale } from '../theme';
import { QtyStepper } from './QtyStepper';

interface FoodItemCardProps {
  item: FoodItem;
  qty: number;
  onInc: () => void;
  onDec: () => void;
}

export function FoodItemCard({ item, qty, onInc, onDec }: FoodItemCardProps) {
  const discounted = item.discount_price != null && item.discount_price < item.price;
  return (
    <View style={styles.card}>
      <Image source={{ uri: item.image_url }} style={styles.img} contentFit="cover" transition={120} />
      <View style={styles.body}>
        <Text style={styles.name} numberOfLines={1}>
          {item.name}
        </Text>
        <Text style={styles.desc} numberOfLines={2}>
          {item.description}
        </Text>
        <View style={styles.footer}>
          <View style={styles.priceRow}>
            <Text style={styles.price}>
              {formatMoney(item.discount_price ?? item.price, item.currency)}
            </Text>
            {discounted ? (
              <Text style={styles.strike}>{formatMoney(item.price, item.currency)}</Text>
            ) : null}
          </View>
          <QtyStepper value={qty} onChange={(next) => (next > qty ? onInc() : onDec())} />
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    flexDirection: 'row',
    gap: space['3'],
    padding: space['3'],
    borderRadius: radius.md,
    backgroundColor: colors.bg.surface,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
  },
  img: { width: 76, height: 76, borderRadius: radius.sm, backgroundColor: colors.bg.elevated },
  body: { flex: 1, gap: space['1'] },
  name: { ...typeScale.bodyBold, color: colors.text.primary },
  desc: { ...typeScale.caption, color: colors.text.muted },
  footer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: space['1'],
  },
  priceRow: { flexDirection: 'row', alignItems: 'center', gap: space['2'] },
  price: { ...typeScale.bodyBold, color: colors.accent.primary },
  strike: {
    ...typeScale.caption,
    color: colors.text.muted,
    textDecorationLine: 'line-through',
  },
});
