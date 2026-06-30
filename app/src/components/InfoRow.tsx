/** InfoRow — a label/value line used in the booking summary, confirmation and ticket cards. */
import { StyleSheet, Text, View } from 'react-native';

import { colors, space, type as typeScale } from '../theme';

export function InfoRow({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <View style={styles.row}>
      <Text style={styles.label}>{label}</Text>
      <Text style={[styles.value, mono && styles.mono]} numberOfLines={1}>
        {value}
      </Text>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', gap: space['3'] },
  label: { ...typeScale.caption, color: colors.text.muted, width: 72 },
  value: { ...typeScale.body, color: colors.text.primary, flex: 1 },
  mono: { ...typeScale.bodyBold, letterSpacing: 1 },
});
