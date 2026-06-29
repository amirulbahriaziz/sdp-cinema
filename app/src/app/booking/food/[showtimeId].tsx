/**
 * Food & Beverage — step 3 (skippable). Tabs across Combo / Food & Snacks / Beverages; each item
 * has a QtyStepper bound to the booking draft. The unit price added is `discount_price ?? price`
 * (integer minor units). "Skip" or "Confirm" both advance to the Booking Summary — Skip just
 * leaves the F&B selection as-is (empty if nothing was added).
 */
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeBack } from '@/lib/use-safe-back';
import { useMemo, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useFoodItems } from '@/api/hooks';
import { FoodItemCard, PriceTotalBar, Screen, StepHeader, Tabs } from '@/components';
import type { FoodCategory } from '@/data/types';
import { useBookingStore } from '@/store/booking';
import { colors, space, type as typeScale } from '@/theme';

const TABS: { value: FoodCategory; label: string }[] = [
  { value: 'combo', label: 'Combo' },
  { value: 'food_snacks', label: 'Food & Snacks' },
  { value: 'beverages', label: 'Beverages' },
];

export default function FoodBeverageScreen() {
  const router = useRouter();
  const goBack = useSafeBack();
  const { showtimeId } = useLocalSearchParams<{ showtimeId: string }>();
  const [tab, setTab] = useState<FoodCategory>('combo');

  const food = useBookingStore((s) => s.food);
  const incFood = useBookingStore((s) => s.incFood);
  const decFood = useBookingStore((s) => s.decFood);

  const { data: items, isLoading, isError } = useFoodItems();

  const visible = useMemo(() => (items ?? []).filter((i) => i.category === tab), [items, tab]);
  const foodTotal = useMemo(
    () => Object.values(food).reduce((sum, f) => sum + f.qty * f.unitPrice, 0),
    [food],
  );

  const toSummary = () => router.push(`/booking/summary/${showtimeId}`);

  return (
    <Screen
      header={
        <StepHeader
          title="Food & Beverage"
          onBack={goBack}
          action={{ label: 'Skip', onPress: toSummary }}
        />
      }
      footer={
        <PriceTotalBar
          label="F&B Sub-total"
          amount={foodTotal}
          caption={foodTotal === 0 ? 'Optional — you can skip' : undefined}
          ctaLabel="Confirm"
          onPress={toSummary}
        />
      }>
      <View style={styles.content}>
        <Tabs options={TABS} value={tab} onChange={setTab} />
        {isLoading ? (
          <ActivityIndicator color={colors.accent.primary} style={styles.loader} />
        ) : isError ? (
          <Text style={styles.error}>Couldn&apos;t load the menu.</Text>
        ) : (
          <View style={styles.list}>
            {visible.map((item) => (
              <FoodItemCard
                key={item.id}
                item={item}
                qty={food[item.id]?.qty ?? 0}
                onInc={() => incFood(item.id, item.discount_price ?? item.price)}
                onDec={() => decFood(item.id)}
              />
            ))}
          </View>
        )}
      </View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  content: { gap: space['4'], paddingTop: space['4'] },
  list: { gap: space['3'] },
  loader: { marginTop: space['8'] },
  error: { ...typeScale.body, color: colors.text.muted, textAlign: 'center', marginTop: space['6'] },
});
