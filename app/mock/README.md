# mock/

Bundled JSON fixtures for the `mock` data source (`EXPO_PUBLIC_DATA_SOURCE=mock`).

These mirror the live Laravel API response shapes (`{ data }` envelopes) so screens
never know which source served them. The data layer also falls back here when a `live`
request fails, keeping the app demoable offline.

All shapes are defined in the repo-root **`CONTRACT.md`** (single source of truth). Money fields
are integer minor units paired with `currency: "RM"`.

| File | Endpoint it mirrors |
|------|---------------------|
| `movies.json` | `GET /movies` (summary list) |
| `movie-1.json` / `movie-2.json` / `movie-3.json` | `GET /movies/{id}` (full details + reviews) |
| `cinemas.json` | `GET /cinemas` |
| `showtimes.json` | `GET /showtimes` (adapter filters by movie/cinema/date client-side) |
| `seats-1.json` / `seats-2.json` | `GET /showtimes/{id}/seats` (live seat map; static in mock) |
| `food-items.json` | `GET /food-items` |
| `auth.json` | `POST /auth/register` and `POST /auth/login` (`{ user, token }`) |
| `lock.json` | `POST /showtimes/{id}/seats/{seatCode}/lock` (201) |
| `lock-conflict.json` | `409` body for a contested lock |
| `booking.json` | `POST /bookings` (201 confirmed order) |
