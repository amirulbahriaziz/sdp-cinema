/**
 * Checkout — the single place that turns the booking draft into a `POST /bookings` call and
 * routes to the confirmation screen on success. Used by the payment-method screen (Bank
 * Transfer / Crypto pay inline) and the card screen (after the dummy card form).
 *
 * Source-agnostic: `useCreateBooking` calls the active data adapter, so this works in `live`
 * mode (HTTP -> Laravel) and `mock` mode (static success) identically — and inherits the
 * data layer's live->mock fallback, keeping the flow walkable offline.
 */
import { useRouter } from 'expo-router';

import { useCreateBooking } from '@/api/hooks';
import { selectBookingRequest, useBookingStore } from '@/store/booking';

export function useCheckout() {
  const router = useRouter();
  const mutation = useCreateBooking();

  const pay = () => {
    // Read the draft at submit time (not via subscription) so the request is a fresh snapshot.
    const request = selectBookingRequest(useBookingStore.getState());
    mutation.mutate(request, {
      onSuccess: (booking) => {
        useBookingStore.getState().setResult(booking);
        useBookingStore.getState().setStep('confirmation');
        // replace so the hardware back button doesn't return into the payment form
        router.replace(`/booking/confirmation/${request.showtime_id}`);
      },
    });
  };

  return { pay, isPending: mutation.isPending, isError: mutation.isError };
}
