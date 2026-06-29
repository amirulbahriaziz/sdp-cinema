/**
 * Booking-draft store (Zustand) — client-only user intent for the in-progress booking:
 * selected seats, F&B quantities, promo code, payment method, and the current wizard step.
 *
 * RQ owns server truth; this store owns intent. Totals here are **advisory** — the API
 * recomputes them server-side on confirm (CONTRACT §12). All money is integer minor units.
 */
import { create } from 'zustand';
import { useShallow } from 'zustand/react/shallow';

import type {
  BookingRequest,
  BookingResult,
  PaymentMethod,
  Seat,
  Showtime,
} from '../data/types';

export type BookingStep = 'seats' | 'food' | 'summary' | 'payment' | 'confirmation';

/** Minimal seat snapshot the draft needs (subset of the live seat). */
export interface DraftSeat {
  seat_code: string;
  type: Seat['type'];
  price: number;
}

/** Demo promo codes (server is authoritative; this mirrors the contract for live preview). */
const PROMOS: Record<string, number> = { WELCOME10: 0.1 };
const SERVICE_CHARGE_RATE = 0.05;

export interface BookingTotals {
  subtotal: number;
  foodTotal: number;
  serviceCharge: number;
  discount: number;
  total: number;
}

interface BookingState {
  step: BookingStep;
  showtimeId: number | null;
  /** Snapshot of the chosen showtime (cinema/hall/time/tier) so later steps render it
   *  without re-fetching. Server truth still wins on confirm. */
  showtime: Showtime | null;
  seats: DraftSeat[];
  /** food_item_id -> { qty, unitPrice } (unitPrice = discount_price ?? price). */
  food: Record<number, { qty: number; unitPrice: number }>;
  promoCode: string | null;
  paymentMethod: PaymentMethod;
  /** The confirmed booking returned by the API (or mock) after checkout — read by the
   *  confirmation screen. Null until a successful `POST /bookings`. */
  result: BookingResult | null;

  setStep: (step: BookingStep) => void;
  startBooking: (showtimeId: number) => void;
  /** Enter the booking flow for a concrete showtime; resets the draft when the showtime changes. */
  beginBooking: (showtime: Showtime) => void;
  toggleSeat: (seat: DraftSeat) => void;
  isSeatSelected: (seatCode: string) => boolean;
  setFoodQty: (foodItemId: number, qty: number, unitPrice: number) => void;
  incFood: (foodItemId: number, unitPrice: number) => void;
  decFood: (foodItemId: number) => void;
  setPromoCode: (code: string | null) => void;
  setPaymentMethod: (method: PaymentMethod) => void;
  setResult: (result: BookingResult | null) => void;
  reset: () => void;
}

const initial = {
  step: 'seats' as BookingStep,
  showtimeId: null as number | null,
  showtime: null as Showtime | null,
  seats: [] as DraftSeat[],
  food: {} as Record<number, { qty: number; unitPrice: number }>,
  promoCode: null as string | null,
  paymentMethod: 'card' as PaymentMethod,
  result: null as BookingResult | null,
};

export const useBookingStore = create<BookingState>((set, get) => ({
  ...initial,

  setStep: (step) => set({ step }),

  startBooking: (showtimeId) =>
    set(
      get().showtimeId === showtimeId
        ? { showtimeId }
        : { ...initial, showtimeId }, // new showtime -> fresh draft
    ),

  beginBooking: (showtime) =>
    set(
      get().showtimeId === showtime.id
        ? { showtimeId: showtime.id, showtime }
        : { ...initial, showtimeId: showtime.id, showtime }, // new showtime -> fresh draft
    ),

  toggleSeat: (seat) =>
    set((s) => {
      const exists = s.seats.some((x) => x.seat_code === seat.seat_code);
      return {
        seats: exists
          ? s.seats.filter((x) => x.seat_code !== seat.seat_code)
          : [...s.seats, seat],
      };
    }),

  isSeatSelected: (seatCode) => get().seats.some((x) => x.seat_code === seatCode),

  setFoodQty: (foodItemId, qty, unitPrice) =>
    set((s) => {
      const next = { ...s.food };
      if (qty <= 0) delete next[foodItemId];
      else next[foodItemId] = { qty, unitPrice };
      return { food: next };
    }),

  incFood: (foodItemId, unitPrice) =>
    set((s) => {
      const current = s.food[foodItemId]?.qty ?? 0;
      return { food: { ...s.food, [foodItemId]: { qty: current + 1, unitPrice } } };
    }),

  decFood: (foodItemId) =>
    set((s) => {
      const current = s.food[foodItemId]?.qty ?? 0;
      const next = { ...s.food };
      if (current <= 1) delete next[foodItemId];
      else next[foodItemId] = { qty: current - 1, unitPrice: s.food[foodItemId].unitPrice };
      return { food: next };
    }),

  setPromoCode: (code) => set({ promoCode: code ? code.trim().toUpperCase() : null }),

  setPaymentMethod: (method) => set({ paymentMethod: method }),

  setResult: (result) => set({ result }),

  reset: () => set({ ...initial }),
}));

// ---- selectors (advisory totals; integer minor units, no float arithmetic on stored values) ----

export const selectSeatCodes = (s: BookingState): string[] =>
  s.seats.map((x) => x.seat_code);

export const selectTotals = (s: BookingState): BookingTotals => {
  const subtotal = s.seats.reduce((sum, x) => sum + x.price, 0);
  const foodTotal = Object.values(s.food).reduce((sum, f) => sum + f.qty * f.unitPrice, 0);
  const serviceCharge = Math.round((subtotal + foodTotal) * SERVICE_CHARGE_RATE);
  const promoRate = s.promoCode ? (PROMOS[s.promoCode] ?? 0) : 0;
  const discount = Math.round(subtotal * promoRate);
  const total = subtotal + foodTotal + serviceCharge - discount;
  return { subtotal, foodTotal, serviceCharge, discount, total };
};

/**
 * Subscribe to advisory totals with shallow equality. `selectTotals` returns a fresh object each
 * call, so a raw `useBookingStore(selectTotals)` would re-render forever (Object.is sees a new ref
 * every render). useShallow compares field-by-field.
 */
export const useBookingTotals = (): BookingTotals => useBookingStore(useShallow(selectTotals));

/**
 * Map the draft into the `POST /bookings` request body (CONTRACT §12). Seat codes + F&B
 * quantities + promo + chosen method; the server recomputes all money. `food`/`promo_code`
 * are omitted when empty so the payload stays minimal.
 */
export const selectBookingRequest = (s: BookingState): BookingRequest => {
  const food = Object.entries(s.food).map(([id, f]) => ({
    food_item_id: Number(id),
    qty: f.qty,
  }));
  return {
    showtime_id: s.showtimeId!,
    seat_codes: s.seats.map((x) => x.seat_code),
    payment_method: s.paymentMethod,
    ...(food.length ? { food } : {}),
    ...(s.promoCode ? { promo_code: s.promoCode } : {}),
  };
};
