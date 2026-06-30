/**
 * SearchBar — rounded dark input with a leading search icon and optional clear button.
 * Used on Home to search by movie or cinema hall.
 */
import { Ionicons } from '@expo/vector-icons';
import { Pressable, StyleSheet, TextInput, View } from 'react-native';

import { colors, radius, space, type as typeScale } from '../theme';

interface SearchBarProps {
  value: string;
  onChangeText: (text: string) => void;
  placeholder?: string;
  onSubmit?: () => void;
}

export function SearchBar({ value, onChangeText, placeholder = 'Search movies or cinemas', onSubmit }: SearchBarProps) {
  return (
    <View style={styles.wrap}>
      <Ionicons name="search" size={20} color={colors.text.muted} />
      <TextInput
        style={styles.input}
        value={value}
        onChangeText={onChangeText}
        onSubmitEditing={onSubmit}
        placeholder={placeholder}
        placeholderTextColor={colors.text.muted}
        returnKeyType="search"
        autoCorrect={false}
      />
      {value.length > 0 ? (
        <Pressable hitSlop={8} onPress={() => onChangeText('')}>
          <Ionicons name="close-circle" size={20} color={colors.text.muted} />
        </Pressable>
      ) : null}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: space['2'],
    backgroundColor: colors.bg.surface,
    borderRadius: radius.md,
    paddingHorizontal: space['3'],
    height: 46,
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: colors.border.default,
  },
  input: { flex: 1, ...typeScale.body, color: colors.text.primary, padding: 0 },
});
