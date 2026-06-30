/**
 * Carousel — a titled horizontal scroller ("New Releases ... view all") plus a generic
 * horizontal list. SectionHeader is exported separately for reuse on other screens.
 */
import type { ReactElement } from 'react';
import { FlatList, Pressable, StyleSheet, Text, View } from 'react-native';

import { colors, space, type as typeScale } from '../theme';

export function SectionHeader({
  title,
  onViewAll,
}: {
  title: string;
  onViewAll?: () => void;
}) {
  return (
    <View style={styles.header}>
      <Text style={styles.title}>{title}</Text>
      {onViewAll ? (
        <Pressable hitSlop={8} onPress={onViewAll}>
          <Text style={styles.viewAll}>View all</Text>
        </Pressable>
      ) : null}
    </View>
  );
}

interface CarouselProps<T> {
  title: string;
  data: T[];
  renderItem: (item: T) => ReactElement;
  keyExtractor: (item: T) => string;
  onViewAll?: () => void;
}

export function Carousel<T>({ title, data, renderItem, keyExtractor, onViewAll }: CarouselProps<T>) {
  return (
    <View style={styles.section}>
      <SectionHeader title={title} onViewAll={onViewAll} />
      <FlatList
        horizontal
        data={data}
        keyExtractor={keyExtractor}
        renderItem={({ item }) => renderItem(item)}
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.listContent}
        ItemSeparatorComponent={() => <View style={{ width: space['3'] }} />}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  section: { gap: space['3'] },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  title: { ...typeScale.title, color: colors.text.primary },
  viewAll: { ...typeScale.caption, color: colors.accent.primary },
  listContent: { paddingRight: space['4'] },
});
