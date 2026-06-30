/**
 * SeatMap — the curved "Screen" header over the seat grid. Rows are labeled on both sides;
 * each cell's status is the API status, overridden to the client-only `selected` for seats in
 * the booking draft. Selecting/deselecting is delegated to `onToggleSeat` (which triggers the
 * FCFS lock/release in live mode). Booked/held cells are non-interactive.
 */
import { Fragment } from 'react';
import { StyleSheet, Text, View } from 'react-native';

import type { Seat, SeatMap as SeatMapData, SeatStatusClient } from '../data/types';
import { colors, radius, space, type as typeScale } from '../theme';
import { SeatCell } from './SeatCell';

interface SeatMapProps {
  seatMap: SeatMapData;
  /** seat_codes currently in the booking draft (rendered as `selected`). */
  selected: string[];
  onToggleSeat?: (seat: Seat) => void;
}

export function SeatMap({ seatMap, selected, onToggleSeat }: SeatMapProps) {
  const selectedSet = new Set(selected);

  // Group seats by row label, ordered by the map's `rows`, sorted by column.
  const byRow = new Map<string, Seat[]>();
  for (const seat of seatMap.seats) {
    const list = byRow.get(seat.row_label) ?? [];
    list.push(seat);
    byRow.set(seat.row_label, list);
  }
  for (const list of byRow.values()) list.sort((a, b) => a.col_num - b.col_num);

  return (
    <View style={styles.container}>
      <View style={styles.screen}>
        <Text style={styles.screenLabel}>SCREEN</Text>
      </View>

      <View style={styles.grid}>
        {seatMap.rows.map((row) => {
          const seats = byRow.get(row) ?? [];
          return (
            <View key={row} style={styles.row}>
              <Text style={styles.rowLabel}>{row}</Text>
              <View style={styles.rowSeats}>
                {seats.map((seat) => {
                  const status: SeatStatusClient = selectedSet.has(seat.seat_code)
                    ? 'selected'
                    : seat.status;
                  return (
                    <Fragment key={seat.seat_code}>
                      <SeatCell
                        seatCode={seat.seat_code}
                        status={status}
                        onPress={() => onToggleSeat?.(seat)}
                      />
                    </Fragment>
                  );
                })}
              </View>
              <Text style={styles.rowLabel}>{row}</Text>
            </View>
          );
        })}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { gap: space['5'], alignItems: 'center' },
  screen: {
    alignSelf: 'stretch',
    height: 36,
    backgroundColor: colors.bg.elevated,
    borderTopLeftRadius: radius.lg,
    borderTopRightRadius: radius.lg,
    borderBottomLeftRadius: radius.pill,
    borderBottomRightRadius: radius.pill,
    alignItems: 'center',
    justifyContent: 'center',
  },
  screenLabel: { ...typeScale.caption, letterSpacing: 4, color: colors.text.muted },
  grid: { gap: space['2'] },
  row: { flexDirection: 'row', alignItems: 'center', gap: space['2'] },
  rowSeats: { flexDirection: 'row', gap: space['1'] },
  rowLabel: { ...typeScale.caption, color: colors.text.muted, width: 16, textAlign: 'center' },
});
