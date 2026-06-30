/**
 * `live` data adapter — HTTP calls to the Laravel API (EXPO_PUBLIC_API_URL + `/api`).
 *
 * Unwraps the `{ data }` success envelope into plain domain objects so screens/hooks stay
 * source-agnostic. Auth-required calls (lock/release/createBooking/login) attach the Sanctum
 * bearer token via an axios request interceptor. Any thrown error here lets the fallback
 * wrapper drop to the `mock` adapter so the app stays demoable offline.
 */
import axios, { type AxiosInstance } from 'axios';

import { config } from '../config';
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

let authToken: string | null = null;

/** Set/clear the Sanctum bearer token used for authenticated requests. */
export function setAuthToken(token: string | null): void {
  authToken = token;
}

const http: AxiosInstance = axios.create({
  baseURL: `${config.apiUrl}/api`,
  timeout: 8000,
  headers: { Accept: 'application/json' },
});

http.interceptors.request.use((reqConfig) => {
  if (authToken) {
    reqConfig.headers.set?.('Authorization', `Bearer ${authToken}`);
  }
  return reqConfig;
});

/** Drop query keys whose value is null/undefined so axios omits them. */
function params(obj?: object): Record<string, unknown> | undefined {
  if (!obj) return undefined;
  return Object.fromEntries(Object.entries(obj).filter(([, v]) => v != null));
}

export const liveAdapter: DataAdapter = {
  async getMovies(query?: MovieQuery) {
    const res = await http.get<{ data: MovieSummary[] }>('/movies', { params: params(query) });
    return res.data.data;
  },

  async getMovie(id: number) {
    const res = await http.get<{ data: MovieDetail }>(`/movies/${id}`);
    return res.data.data;
  },

  async getCinemas(city?: string) {
    const res = await http.get<{ data: Cinema[] }>('/cinemas', { params: params({ city }) });
    return res.data.data;
  },

  async getShowtimes(query?: ShowtimeQuery) {
    const res = await http.get<{ data: Showtime[] }>('/showtimes', { params: params(query) });
    return res.data.data;
  },

  async getSeatMap(showtimeId: number) {
    const res = await http.get<{ data: SeatMap }>(`/showtimes/${showtimeId}/seats`);
    return res.data.data;
  },

  async lockSeat(showtimeId: number, seatCode: string) {
    const res = await http.post<{ data: LockResult }>(
      `/showtimes/${showtimeId}/seats/${seatCode}/lock`,
    );
    return res.data.data;
  },

  async releaseSeat(showtimeId: number, seatCode: string) {
    const res = await http.delete<{ data: LockResult }>(
      `/showtimes/${showtimeId}/seats/${seatCode}/lock`,
    );
    return res.data.data;
  },

  async cancelHolds(showtimeId: number) {
    const res = await http.delete<{ data: { showtime_id: number; released: string[] } }>(
      `/showtimes/${showtimeId}/holds`,
    );
    return { released: res.data.data.released };
  },

  async getFoodItems(query?: FoodQuery) {
    const res = await http.get<{ data: FoodItem[] }>('/food-items', { params: params(query) });
    return res.data.data;
  },

  async createBooking(payload: BookingRequest) {
    const res = await http.post<{ data: BookingResult }>('/bookings', payload);
    return res.data.data;
  },

  async getBookings() {
    const res = await http.get<{ data: BookingResult[] }>('/bookings');
    return res.data.data;
  },

  async getBooking(id: number) {
    const res = await http.get<{ data: BookingResult }>(`/bookings/${id}`);
    return res.data.data;
  },

  async login(email: string, password: string) {
    const res = await http.post<{ data: AuthResult }>('/auth/login', { email, password });
    return res.data.data;
  },

  async register(input) {
    const res = await http.post<{ data: AuthResult }>('/auth/register', input);
    return res.data.data;
  },
};
