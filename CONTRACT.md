# SDP Cinema — API Contract

**Single source of truth** for the REST contract that both `api/` (Laravel 12) and `app/` (Expo)
build against. The `app/mock/*.json` fixtures mirror these exact response shapes so the `mock` data
source is byte-compatible with `live`.

## Conventions

- **Base URL:** `/api` (e.g. `http://127.0.0.1:8000/api`). All paths below are relative to it.
- **Success envelope:** `{ "data": ... }`. **Error envelope:** `{ "message": string, "errors"?: { field: string[] } }`.
- **Auth:** Sanctum bearer token — `Authorization: Bearer <token>`. Stateless; no server session.
- **Money:** every money field is an **integer in minor units** (cents) paired with a sibling
  `currency` string. Currency is always `"RM"` (MYR). Example: `RM 18.00` → `1800`. **No floats.**
- **Seat status enum (shared vocabulary):** `available | held | booked`. (`selected` is client-only,
  never sent by the API.)
- **Seat price (Option B):** `price = lookup(showtime.tier, seat.type)` from `seat_type_prices`.
  A tier card's range is `MIN..MAX` of that tier's seat-type prices.
- **Timestamps:** ISO-8601 with offset, e.g. `2026-07-02T19:30:00+08:00`.
- **Standard status codes:** `200` OK, `201` Created, `204` No Content, `401` Unauthenticated,
  `403` Forbidden, `404` Not Found, `409` Conflict (seat race), `422` Unprocessable (validation).

---

## Endpoint Index

| # | Method | Path | Auth | Purpose |
|---|--------|------|------|---------|
| 1 | POST | `/auth/register` | – | Create account, return token |
| 2 | POST | `/auth/login` | – | Exchange credentials for token |
| 3 | POST | `/auth/logout` | ✓ | Revoke current token |
| 4 | GET | `/movies` | – | List movies (search / section filter) |
| 5 | GET | `/movies/{id}` | – | Movie details + reviews |
| 6 | GET | `/cinemas` | – | List cinemas + their halls |
| 7 | GET | `/showtimes` | – | List showtimes (filter movie/cinema/date) |
| 8 | GET | `/showtimes/{id}/seats` | – | Live seat map for a showtime |
| 9 | POST | `/showtimes/{id}/seats/{seatCode}/lock` | ✓ | Acquire FCFS seat hold |
| 10 | DELETE | `/showtimes/{id}/seats/{seatCode}/lock` | ✓ | Release own seat hold |
| 11 | GET | `/food-items` | – | F&B catalog (combo / food_snacks / beverages) |
| 12 | POST | `/bookings` | ✓ | Confirm booking (atomic) |

---

## 1. POST /auth/register

**Request**
```json
{ "name": "Aiman Tan", "email": "aiman@example.com", "password": "secret123", "password_confirmation": "secret123" }
```
**201 Created**
```json
{ "data": { "user": { "id": 1, "name": "Aiman Tan", "email": "aiman@example.com" }, "token": "1|abcDEF..." } }
```
**422** — validation (e.g. email taken): `{ "message": "The email has already been taken.", "errors": { "email": ["The email has already been taken."] } }`

## 2. POST /auth/login

**Request** `{ "email": "aiman@example.com", "password": "secret123" }`
**200 OK** — same `data` shape as register (`{ user, token }`).
**401** — `{ "message": "Invalid credentials." }`
**422** — missing fields.

## 3. POST /auth/logout  *(Bearer)*

**204 No Content** — token revoked.
**401** — `{ "message": "Unauthenticated." }`

---

## 4. GET /movies

**Query (all optional):** `search` (matches title or cinema), `section` = `new_releases | popular | recommended`.
**200 OK**
```json
{ "data": [
  { "id": 1, "title": "Venom: The Last Dance", "poster_url": "https://...", "age_rating": "P13",
    "imdb_rating": 7.1, "duration_min": 109, "release_date": "2026-06-12",
    "genres": ["Action", "Sci-Fi"], "sections": ["new_releases", "popular"] }
] }
```
List items are **summaries**; full metadata comes from `GET /movies/{id}`.

## 5. GET /movies/{id}

**200 OK**
```json
{ "data": {
  "id": 1, "title": "Venom: The Last Dance", "synopsis": "Eddie and Venom...",
  "duration_min": 109, "release_date": "2026-06-12", "age_rating": "P13",
  "imdb_rating": 7.1, "poster_url": "https://...", "trailer_url": "https://...",
  "genres": ["Action", "Sci-Fi"], "director": "Kelly Marcel",
  "casts": ["Tom Hardy", "Chiwetel Ejiofor"], "writers": ["Kelly Marcel", "Tom Hardy"],
  "rating_summary": { "average": 4.2, "count": 128,
    "breakdown": { "5": 70, "4": 35, "3": 15, "2": 5, "1": 3 } },
  "reviews": [
    { "id": 10, "user_name": "Sofia", "rating": 5, "title": "Best yet", "body": "...",
      "created_at": "2026-06-20T10:00:00+08:00" }
  ]
} }
```
**404** — `{ "message": "Movie not found." }`

## 6. GET /cinemas

**Query (optional):** `city`.
**200 OK**
```json
{ "data": [
  { "id": 1, "name": "TGV Suria KLCC", "city": "Kuala Lumpur",
    "address": "Level 3, Suria KLCC, Kuala Lumpur City Centre",
    "halls": [ { "id": 1, "name": "Hall 1" } ] }
] }
```

## 7. GET /showtimes

**Query (optional filters):** `movie_id`, `cinema_id`, `date` (`YYYY-MM-DD`, local KL date).
**200 OK**
```json
{ "data": [
  { "id": 1, "movie_id": 1, "starts_at": "2026-07-02T19:30:00+08:00",
    "ends_at": "2026-07-02T21:19:00+08:00",
    "cinema": { "id": 1, "name": "TGV Suria KLCC", "city": "Kuala Lumpur" },
    "hall": { "id": 1, "name": "Hall 1" },
    "tier": { "id": 1, "name": "Classic", "currency": "RM", "price_min": 1800, "price_max": 2500 },
    "seats_available": 74 }
] }
```
`price_min`/`price_max` are the tier card's `MIN..MAX` range (minor units).

## 8. GET /showtimes/{id}/seats

The live seat map: hall seats LEFT-joined to this showtime's `booking_seats` (→ `booked`) and active
non-expired `seat_locks` (→ `held`); everything else `available`. Price is resolved per seat type
against the showtime's tier.
**200 OK**
```json
{ "data": {
  "showtime_id": 1, "currency": "RM",
  "tier": { "id": 1, "name": "Classic", "currency": "RM", "price_min": 1800, "price_max": 2500 },
  "rows": ["A","B","C","D","E","F","G","H"], "cols": 10,
  "seat_type_prices": [
    { "seat_type": "standard", "price": 1800 },
    { "seat_type": "premium", "price": 2500 }
  ],
  "seats": [
    { "seat_code": "A1", "row_label": "A", "col_num": 1, "type": "standard", "status": "booked",  "price": 1800 },
    { "seat_code": "C5", "row_label": "C", "col_num": 5, "type": "standard", "status": "held",     "price": 1800 },
    { "seat_code": "G3", "row_label": "G", "col_num": 3, "type": "premium",  "status": "available", "price": 2500 }
  ]
} }
```
**404** — `{ "message": "Showtime not found." }`
> Note: in `live` mode this is patched in real time by the `SeatStatusChanged` broadcast; in `mock`
> mode the map is static.

## 9. POST /showtimes/{id}/seats/{seatCode}/lock  *(Bearer)*

Atomic FCFS hold. Server `INSERT`s a `seat_locks` row guarded by `UNIQUE(showtime_id, seat_id)` —
**first writer wins**; the loser gets `409`. TTL ≈ 5 minutes.
**201 Created**
```json
{ "data": { "showtime_id": 1, "seat_code": "D4", "status": "held",
  "holder": "you", "expires_at": "2026-07-02T19:05:00+08:00", "ttl_seconds": 300 } }
```
**409 Conflict** — seat already held by another / already booked:
```json
{ "message": "Seat is no longer available.", "errors": { "seat": ["This seat is already held or booked."] } }
```
**404** — showtime or seat code not found. **401** — unauthenticated.

## 10. DELETE /showtimes/{id}/seats/{seatCode}/lock  *(Bearer)*

Release a hold owned by the caller (only the `holder` may release; otherwise TTL expires it).
**200 OK**
```json
{ "data": { "showtime_id": 1, "seat_code": "D4", "status": "available" } }
```
**403** — `{ "message": "You do not hold this seat." }`
**404** — no active lock for that seat. **401** — unauthenticated.

## 11. GET /food-items

**Query (optional):** `category` = `combo | food_snacks | beverages`.
**200 OK**
```json
{ "data": [
  { "id": 1, "category": "combo", "name": "Sweet Couple Combo",
    "description": "2 medium popcorn + 2 regular drinks", "price": 3900, "discount_price": 3200,
    "currency": "RM", "image_url": "https://..." }
] }
```
`discount_price` is `null` when there is no discount; the effective unit price is
`discount_price ?? price`.

## 12. POST /bookings  *(Bearer)*

Confirms a booking in a single atomic DB transaction: validates that every `seat_code` is still
**held by the caller**, inserts `booking_seats` + `booking_food_items`, deletes the holds, attaches a
stub `payment`, and returns the order. Totals are computed **server-side** (client totals are advisory).

**Request**
```json
{ "showtime_id": 1,
  "seat_codes": ["D4", "D5"],
  "food": [ { "food_item_id": 1, "qty": 1 }, { "food_item_id": 7, "qty": 2 } ],
  "promo_code": "WELCOME10",
  "payment_method": "card" }
```
`food` and `promo_code` are optional. `payment_method` ∈ `card | bank | crypto` (payment is stubbed).

**201 Created**
```json
{ "data": {
  "id": 501, "reference": "SDP-2026-000501", "status": "confirmed", "currency": "RM",
  "showtime": { "id": 1, "starts_at": "2026-07-02T19:30:00+08:00",
    "movie": { "id": 1, "title": "Venom: The Last Dance" },
    "cinema": { "id": 1, "name": "TGV Suria KLCC" }, "hall": { "id": 1, "name": "Hall 1" } },
  "seats": [
    { "seat_code": "D4", "type": "standard", "unit_price": 1800 },
    { "seat_code": "D5", "type": "standard", "unit_price": 1800 }
  ],
  "food": [
    { "food_item_id": 1, "name": "Sweet Couple Combo", "qty": 1, "unit_price": 3200, "line_total": 3200 },
    { "food_item_id": 7, "name": "Regular Coke", "qty": 2, "unit_price": 850, "line_total": 1700 }
  ],
  "subtotal": 3600, "food_total": 4900, "service_charge": 425,
  "promo_code": "WELCOME10", "discount": 360, "total": 8565,
  "payment": { "method": "card", "amount": 8565, "currency": "RM", "status": "paid", "reference": "PAY-000501" }
} }
```
**Totals contract:** `subtotal` = Σ seat `unit_price`; `food_total` = Σ food `line_total`;
`service_charge` is computed server-side (demo: 5% of `subtotal + food_total`, rounded to minor
units); `discount` from `promo_code` (e.g. `WELCOME10` = 10% of `subtotal`); `total` =
`subtotal + food_total + service_charge - discount`. All integer minor units.

**409 Conflict** — a requested seat is no longer held by the caller (expired/taken):
`{ "message": "One or more seats are no longer available.", "errors": { "seat_codes": ["D5 is no longer held by you."] } }`
**422** — validation (no seats, unknown food item, etc). **404** — showtime not found. **401** — unauthenticated.
