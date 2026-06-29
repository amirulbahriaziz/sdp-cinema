/**
 * Screen — safe-area dark wrapper every screen sits inside. Optionally scrollable, with a
 * fixed bottom slot for the sticky CTA bar (Cancel/Proceed, Book Ticket) used across the flow.
 */
import type { ReactNode } from 'react';
import { ScrollView, StyleSheet, View, type ViewStyle } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';

import { colors, space } from '../theme';

interface ScreenProps {
  children: ReactNode;
  /** Fixed header (e.g. StepHeader) above the scroll area. */
  header?: ReactNode;
  /** Render content inside a ScrollView (default true). */
  scroll?: boolean;
  /** Sticky bottom bar (e.g. PrimaryButton / PriceTotalBar) outside the scroll area. */
  footer?: ReactNode;
  /** Horizontal padding on the content (default on). */
  padded?: boolean;
  contentStyle?: ViewStyle;
}

export function Screen({ children, header, scroll = true, footer, padded = true, contentStyle }: ScreenProps) {
  const inner = padded ? [styles.padded, contentStyle] : contentStyle;
  return (
    <SafeAreaView style={styles.safe} edges={['top', 'left', 'right']}>
      {header}
      {scroll ? (
        <ScrollView
          style={styles.flex}
          contentContainerStyle={[styles.scrollContent, inner]}
          keyboardShouldPersistTaps="handled">
          {children}
        </ScrollView>
      ) : (
        <View style={[styles.flex, inner]}>{children}</View>
      )}
      {footer ? <View style={styles.footer}>{footer}</View> : null}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: colors.bg.base },
  flex: { flex: 1 },
  scrollContent: { paddingBottom: space['8'], flexGrow: 1 },
  padded: { paddingHorizontal: space['4'] },
  footer: {
    paddingHorizontal: space['4'],
    paddingTop: space['3'],
    paddingBottom: space['4'],
    backgroundColor: colors.bg.surface,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: colors.border.default,
  },
});
