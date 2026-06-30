/**
 * `mock` data adapter — reads the bundled `app/mock/*.json` fixtures, no network.
 *
 * It is a *selectable* data source (EXPO_PUBLIC_DATA_SOURCE=mock) AND the offline fallback
 * for the `live` adapter. Fixtures mirror the `{ data }` envelopes in CONTRACT.md, so the
 * shapes are byte-compatible with the API. Metro bundles JSON via static `require`, hence the
 * static imports below (dynamic paths would not be bundled). Filtering is done client-side.
 *
 * Realtime seat locking does not exist in mock mode — the seat map is static; lock/release
 * return optimistic results so the booking flow stays walkable offline.
 */
import type {
  AuthResult,
  BookingRequest,
  BookingResult,
  Cinema,
  DataAdapter,
  FoodItem,
  FoodQuery,
  LockResult,
  MovieDetail,
  MovieQuery,
  MovieSummary,
  SeatMap,
  Showtime,
  ShowtimeQuery,
} from './types';

import authJson from '../../mock/auth.json';
import bookingJson from '../../mock/booking.json';
import cinemasJson from '../../mock/cinemas.json';
import foodItemsJson from '../../mock/food-items.json';
import movie1Json from '../../mock/movie-1.json';
import movie2Json from '../../mock/movie-2.json';
import movie3Json from '../../mock/movie-3.json';
import moviesJson from '../../mock/movies.json';
import seats1Json from '../../mock/seats-1.json';
import seats2Json from '../../mock/seats-2.json';
import showtimesJson from '../../mock/showtimes.json';

const MOVIES = (moviesJson as { data: MovieSummary[] }).data;
const MOVIE_DETAILS: Record<number, MovieDetail> = {
  1: (movie1Json as { data: MovieDetail }).data,
  2: (movie2Json as { data: MovieDetail }).data,
  3: (movie3Json as { data: MovieDetail }).data,
};
const CINEMAS = (cinemasJson as { data: Cinema[] }).data;
const SHOWTIMES = (showtimesJson as { data: Showtime[] }).data;
const FOOD = (foodItemsJson as { data: FoodItem[] }).data;
const SEAT_MAPS: Record<number, SeatMap> = {
  1: (seats1Json as { data: SeatMap }).data,
  2: (seats2Json as { data: SeatMap }).data,
};
const AUTH = (authJson as { data: AuthResult }).data;
const BOOKING = (bookingJson as { data: BookingResult }).data;

/** Simulate a small network latency so loading states are exercised in mock mode. */
const delay = <T>(value: T, ms = 120): Promise<T> =>
  new Promise((resolve) => setTimeout(() => resolve(value), ms));

export const mockAdapter: DataAdapter = {
  getMovies(query?: MovieQuery) {
    let list = MOVIES;
    if (query?.section) list = list.filter((m) => m.sections.includes(query.section!));
    if (query?.search) {
      const q = query.search.toLowerCase();
      list = list.filter((m) => m.title.toLowerCase().includes(q));
    }
    return delay(list);
  },

  getMovie(id: number) {
    const detail = MOVIE_DETAILS[id] ?? MOVIE_DETAILS[1];
    return delay(detail);
  },

  getCinemas(city?: string) {
    const list = city
      ? CINEMAS.filter((c) => c.city.toLowerCase() === city.toLowerCase())
      : CINEMAS;
    return delay(list);
  },

  getShowtimes(query?: ShowtimeQuery) {
    let list = SHOWTIMES;
    if (query?.movie_id != null) list = list.filter((s) => s.movie_id === query.movie_id);
    if (query?.cinema_id != null) list = list.filter((s) => s.cinema.id === query.cinema_id);
    if (query?.date) list = list.filter((s) => s.starts_at.slice(0, 10) === query.date);
    return delay(list);
  },

  getSeatMap(showtimeId: number) {
    // Showtimes without a dedicated fixture reuse map 1, with the id patched in.
    const base = SEAT_MAPS[showtimeId] ?? SEAT_MAPS[1];
    return delay({ ...base, showtime_id: showtimeId });
  },

  lockSeat(showtimeId: number, seatCode: string): Promise<LockResult> {
    return delay({
      showtime_id: showtimeId,
      seat_code: seatCode,
      status: 'held',
      holder: 'you',
      expires_at: new Date(Date.now() + 300_000).toISOString(),
      ttl_seconds: 300,
    });
  },

  cancelHolds(): Promise<{ released: string[] }> {
    // Mock has no real holds — the draft reset in the UI handles the visual.
    return delay({ released: [] });
  },

  releaseSeat(showtimeId: number, seatCode: string): Promise<LockResult> {
    return delay({ showtime_id: showtimeId, seat_code: seatCode, status: 'available' });
  },

  getFoodItems(query?: FoodQuery) {
    const list = query?.category ? FOOD.filter((f) => f.category === query.category) : FOOD;
    return delay(list);
  },

  createBooking(payload: BookingRequest): Promise<BookingResult> {
    // Static confirmed order; echo the chosen payment method for a realistic confirmation.
    return delay({
      ...BOOKING,
      showtime: { ...BOOKING.showtime, id: payload.showtime_id },
      payment: { ...BOOKING.payment, method: payload.payment_method },
    });
  },

  login() {
    return delay(AUTH);
  },

  register() {
    return delay(AUTH);
  },
};
