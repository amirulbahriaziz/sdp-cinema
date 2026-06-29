/**
 * SDP Cinema design tokens — dark-only.
 *
 * Single source of truth for colors / spacing / radius / typography, derived from
 * `artifacts/ai-context/ui-context.md`. Components read from this object; never hardcode
 * a hex value or a raw pixel. There is no light mode and no CSS — this is a plain JS object
 * consumed via `StyleSheet`/styled props.
 */
import { Platform, type TextStyle } from 'react-native';

export const colors = {
  bg: {
    base: '#0B0B0F',
    surface: '#16161C',
    elevated: '#1E1E26',
  },
  text: {
    primary: '#FFFFFF',
    muted: '#9A9AA5',
  },
  accent: {
    primary: '#E50914',
    onPrimary: '#FFFFFF',
  },
  border: {
    default: '#2A2A33',
  },
  /** Per-showtime seat status colors (see seat state machine). */
  seat: {
    available: '#2A2A33',
    selected: '#E50914',
    held: '#5A5A66',
    booked: '#3A1115',
  },
  state: {
    error: '#FF5A5F',
    success: '#2ECC71',
  },
} as const;

/** Base unit = 4. `space(n)` = n * 4. Named keys mirror the ui-context scale. */
export const space = {
  unit: 4,
  '1': 4,
  '2': 8,
  '3': 12,
  '4': 16,
  '5': 20,
  '6': 24,
  '8': 32,
  '10': 40,
  '12': 48,
} as const;

export const radius = {
  sm: 8,
  md: 12,
  lg: 20,
  pill: 999,
} as const;

export const font = Platform.select({
  ios: { sans: 'system-ui', mono: 'ui-monospace' },
  android: { sans: 'normal', mono: 'monospace' },
  default: { sans: 'normal', mono: 'monospace' },
  web: { sans: 'system-ui', mono: 'monospace' },
}) as { sans: string; mono: string };

/** Type scale: display (movie title) / title (headers) / body / caption (metadata, price). */
export const type = {
  display: { fontSize: 28, lineHeight: 34, fontWeight: '700' },
  title: { fontSize: 20, lineHeight: 26, fontWeight: '700' },
  subtitle: { fontSize: 17, lineHeight: 22, fontWeight: '600' },
  body: { fontSize: 15, lineHeight: 21, fontWeight: '400' },
  bodyBold: { fontSize: 15, lineHeight: 21, fontWeight: '600' },
  caption: { fontSize: 12, lineHeight: 16, fontWeight: '400' },
  captionBold: { fontSize: 12, lineHeight: 16, fontWeight: '600' },
} satisfies Record<string, TextStyle>;

export const theme = { colors, space, radius, font, type } as const;

export type Theme = typeof theme;

/**
 * Format integer minor units (cents) + currency code into a display string.
 * Money is always integer minor units; never float-arithmetic on totals. e.g. (1800, 'RM') -> "RM 18.00".
 */
export function formatMoney(minor: number, currency = 'RM'): string {
  const sign = minor < 0 ? '-' : '';
  const abs = Math.abs(minor);
  const major = Math.floor(abs / 100);
  const cents = (abs % 100).toString().padStart(2, '0');
  return `${sign}${currency} ${major}.${cents}`;
}
