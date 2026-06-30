/**
 * WizardFooter — the sticky footer shared by the booking-wizard steps (Seats / Food / Summary).
 * Renders the screen's CTA (passed as children) above a ghost "Cancel booking" button that
 * releases the in-progress holds and returns Home (via useCancelBooking).
 */
import type { ReactNode } from 'react';
import { StyleSheet, View } from 'react-native';

import { useCancelBooking } from '@/lib/use-cancel-booking';
import { space } from '../theme';
import { PrimaryButton } from './PrimaryButton';

export function WizardFooter({ showtimeId, children }: { showtimeId: number; children: ReactNode }) {
  const { cancel, cancelling } = useCancelBooking(showtimeId);
  return (
    <View style={styles.col}>
      {children}
      <PrimaryButton variant="ghost" label="Cancel booking" loading={cancelling} onPress={cancel} />
    </View>
  );
}

const styles = StyleSheet.create({ col: { gap: space['2'] } });
