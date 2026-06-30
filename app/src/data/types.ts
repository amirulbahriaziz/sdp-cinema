/**
 * Data-layer contract types. These mirror the response shapes in repo-root `CONTRACT.md`
 * exactly, so the `mock` and `live` adapters are byte-compatible and screens stay
 * source-agnostic. Every money field is an integer in minor units paired with a `currency`.
 */

export type MovieSection = 'new_releases' | 'popular' | 'recommended';
export type SeatStatus = 'available' | 'held' | 'booked';
/** Client-only seat state (the API never sends this). */
export type SeatStatusClient = SeatStatus | 'selected';
export type SeatType = 'standard' | 'premium';
export type FoodCategory = 'combo' | 'food_snacks' | 'beverages';
export type PaymentMethod = 'card' | 'bank' | 'crypto';

export interface MovieSummary {
  id: number;
  title: string;
  poster_url: string;
  age_rating: string;
  imdb_rating: number;
  duration_min: number;
  release_date: string;
  genres: string[];
  sections: MovieSection[];
}

export interface Review {
  id: number;
  user_name: string;
  rating: number;
  title: string;
  body: string;
  created_at: string;
}

export interface RatingSummary {
  average: number;
  count: number;
  breakdown: Record<'1' | '2' | '3' | '4' | '5', number>;
}

export interface MovieDetail {
  id: number;
  title: string;
  synopsis: string;
  duration_min: number;
  release_date: string;
  age_rating: string;
  imdb_rating: number;
  poster_url: string;
  trailer_url: string;
  genres: string[];
  director: string;
  casts: string[];
  writers: string[];
  rating_summary: RatingSummary;
  reviews: Review[];
}

export interface Hall {
  id: number;
  name: string;
}

export interface Cinema {
  id: number;
  name: string;
  city: string;
  address: string;
  halls: Hall[];
}

export interface Tier {
  id: number;
  name: string;
  currency: string;
  price_min: number;
  price_max: number;
}

export interface Showtime {
  id: number;
  movie_id: number;
  starts_at: string;
  ends_at: string;
  cinema: { id: number; name: string; city: string };
  hall: Hall;
  tier: Tier;
  seats_available: number;
}

export interface Seat {
  seat_code: string;
  row_label: string;
  col_num: number;
  type: SeatType;
  status: SeatStatus;
  price: number;
}

export interface SeatTypePrice {
  seat_type: SeatType;
  price: number;
}

export interface SeatMap {
  showtime_id: number;
  currency: string;
  tier: Tier;
  rows: string[];
  cols: number;
  seat_type_prices: SeatTypePrice[];
  seats: Seat[];
}

export interface FoodItem {
  id: number;
  category: FoodCategory;
  name: string;
  description: string;
  price: number;
  discount_price: number | null;
  currency: string;
  image_url: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface AuthResult {
  user: User;
  token: string;
}

export interface LockResult {
  showtime_id: number;
  seat_code: string;
  status: 'held' | 'available';
  holder?: string;
  expires_at?: string;
  ttl_seconds?: number;
}

export interface BookingFoodLine {
  food_item_id: number;
  name: string;
  qty: number;
  unit_price: number;
  line_total: number;
}

export interface BookingResult {
  id: number;
  reference: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  currency: string;
  showtime: {
    id: number;
    starts_at: string;
    movie: { id: number; title: string };
    cinema: { id: number; name: string };
    hall: { id: number; name: string };
  };
  seats: { seat_code: string; type: SeatType; unit_price: number }[];
  food: BookingFoodLine[];
  subtotal: number;
  food_total: number;
  service_charge: number;
  promo_code: string | null;
  discount: number;
  total: number;
  payment: {
    method: PaymentMethod;
    amount: number;
    currency: string;
    status: string;
    reference: string;
  };
}

// ---- request param shapes ----

export interface MovieQuery {
  search?: string;
  section?: MovieSection;
}

export interface ShowtimeQuery {
  movie_id?: number;
  cinema_id?: number;
  date?: string;
}

export interface FoodQuery {
  category?: FoodCategory;
}

export interface BookingRequest {
  showtime_id: number;
  seat_codes: string[];
  food?: { food_item_id: number; qty: number }[];
  promo_code?: string;
  payment_method: PaymentMethod;
}

/**
 * The single data interface every screen/hook depends on. Both adapters (`live`, `mock`)
 * implement it; the active source is chosen at runtime with automatic fallback to `mock`.
 */
export interface DataAdapter {
  getMovies(query?: MovieQuery): Promise<MovieSummary[]>;
  getMovie(id: number): Promise<MovieDetail>;
  getCinemas(city?: string): Promise<Cinema[]>;
  getShowtimes(query?: ShowtimeQuery): Promise<Showtime[]>;
  getSeatMap(showtimeId: number): Promise<SeatMap>;
  lockSeat(showtimeId: number, seatCode: string): Promise<LockResult>;
  releaseSeat(showtimeId: number, seatCode: string): Promise<LockResult>;
  /** Cancel the in-progress booking: release all of the caller's holds for the showtime. */
  cancelHolds(showtimeId: number): Promise<{ released: string[] }>;
  getFoodItems(query?: FoodQuery): Promise<FoodItem[]>;
  createBooking(payload: BookingRequest): Promise<BookingResult>;
  /** The caller's bookings (My Tickets), newest first. */
  getBookings(): Promise<BookingResult[]>;
  /** One booking (the ticket/receipt) by id. */
  getBooking(id: number): Promise<BookingResult>;
  login(email: string, password: string): Promise<AuthResult>;
  register(input: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
  }): Promise<AuthResult>;
}
