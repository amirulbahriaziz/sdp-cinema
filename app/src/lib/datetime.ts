/**
 * Small date/time formatters for the booking flow. Showtime timestamps are ISO 8601 with a
 * fixed UTC offset (e.g. `2026-07-02T19:30:00+08:00`); we render them in the device locale.
 */

/** "19:30" — 24h clock time of a showtime. */
export function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
}

/** "Thu, 2 Jul" — compact day label for the date strip / summary. */
export function formatDateShort(iso: string): string {
  return new Date(iso).toLocaleDateString([], { weekday: 'short', day: 'numeric', month: 'short' });
}

/** Weekday abbreviation + day-of-month, for the stacked date-strip pills. */
export function dateParts(isoDate: string): { weekday: string; day: string; month: string } {
  const d = new Date(`${isoDate}T00:00:00`);
  return {
    weekday: d.toLocaleDateString([], { weekday: 'short' }),
    day: d.toLocaleDateString([], { day: 'numeric' }),
    month: d.toLocaleDateString([], { month: 'short' }),
  };
}
