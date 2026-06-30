import { useRouter } from 'expo-router';
import { Alert, Platform } from 'react-native';

import { useCancelHolds } from '@/api/hooks';
import { isLiveSource } from '@/data';
import { useBookingStore } from '@/store/booking';

/**
 * Cancel the in-progress booking from any wizard step (Seats / Food / Summary): confirm, release
 * the held seats server-side (live broadcast frees them for everyone), clear the draft, go Home.
 * Releasing is best-effort — even on error we reset and leave; the hold TTL frees the seats anyway.
 */
export function useCancelBooking(showtimeId: number) {
  const router = useRouter();
  const reset = useBookingStore((s) => s.reset);
  const mutation = useCancelHolds(showtimeId);

  const finish = () => {
    reset();
    router.replace('/');
  };

  const proceed = () => {
    if (!isLiveSource) return finish();
    mutation.mutate(undefined, { onSettled: finish });
  };

  const cancel = () => {
    // React Native's Alert is a no-op on web, so use the browser confirm there.
    if (Platform.OS === 'web') {
      if (typeof window !== 'undefined' && window.confirm('Cancel booking? Your selected seats will be released.')) {
        proceed();
      }
      return;
    }
    Alert.alert('Cancel booking?', 'Your selected seats will be released.', [
      { text: 'Keep booking', style: 'cancel' },
      { text: 'Cancel booking', style: 'destructive', onPress: proceed },
    ]);
  };

  return { cancel, cancelling: mutation.isPending };
}
