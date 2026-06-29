# SDP Cinema — Online Cinema Booking App

A movie-discovery and ticket-booking app whose defining feature is **real-time, first-come-first-serve
(FCFS) seat locking**: the instant one user selects a seat, every other user viewing that showtime's
seating plan sees the seat lock and can no longer take it. Built as an interview assignment covering a
Laravel API, a React Native (Expo) client, and supporting architecture/UX docs.

This is a **monorepo** of three independent deliverables:

```
sdp-cinema/
├── api/         Laravel 12 REST API — stateless, Sanctum auth, Reverb WebSockets, Scribe docs, MCP
├── app/         Expo (React Native) app — every flowchart screen; live | mock data source
├── documents/   Docs site generated from the ai-context Markdown (Mermaid ERD / state / flow)
└── README.md    ← you are here
```

> **Seat locks use a DB `UNIQUE(showtime_id, seat_id)` constraint — not Redis.** The insert is atomic:
> the first writer wins, every other concurrent request gets `409`. No Redis is required to run anything.
>
> **Money** is stored as integer **minor units + a currency code** (MYR / **RM**) on every money row —
> never floats.

---

## Prerequisites

| Tool       | Version     | Notes                                            |
| ---------- | ----------- | ------------------------------------------------ |
| PHP        | 8.4         | with the usual Laravel extensions                |
| Composer   | 2.x         | PHP dependency manager                           |
| MySQL      | 8.x         | running on `127.0.0.1:3306`                      |
| Node.js    | 24.x        | for the Expo app (and optional API asset build)  |
| npm        | 10+         | ships with Node                                  |

A MySQL database named **`sdp_cinema`** must exist, reachable as user **`root`** with **no password**
on `127.0.0.1` (matches the committed `api/.env.example`):

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS sdp_cinema;"
```

---

## 1) API — `api/` (Laravel 12)

A stateless REST API: route → controller → service → model; validation and authorization live only in
Form Requests; all business logic in services. Sanctum bearer tokens, Reverb broadcasting, Scribe docs,
and a dev-only Laravel MCP server.

### Setup

```bash
cd api

# 1. Install PHP dependencies
composer install

# 2. Create the env file and generate the app key
cp .env.example .env
php artisan key:generate

# 3. Confirm the DB block in .env (defaults already match the assignment):
#      DB_CONNECTION=mysql
#      DB_HOST=127.0.0.1
#      DB_PORT=3306
#      DB_DATABASE=sdp_cinema
#      DB_USERNAME=root
#      DB_PASSWORD=
#    Broadcasting is preconfigured: BROADCAST_CONNECTION=reverb, QUEUE_CONNECTION=database,
#    and REVERB_* credentials are already filled in.

# 4. Run migrations and seed demo data (movies, cinemas, halls, seats, showtimes, food, prices)
php artisan migrate --seed
```

### Run

The API needs up to **three** processes for the full real-time experience. Run each in its own terminal
from `api/`:

```bash
# a) HTTP API  → http://127.0.0.1:8000
php artisan serve

# b) Reverb WebSocket server  → ws://localhost:8080  (real-time seat-lock push)
php artisan reverb:start

# c) Queue worker — REQUIRED for broadcasts to fire.
#    SeatStatusChanged implements ShouldBroadcast (queued), and QUEUE_CONNECTION=database,
#    so without a worker, lock/release/book events never reach connected clients.
php artisan queue:listen --tries=1
```

> Steps **b** and **c** are only needed for real-time. The HTTP API, seat locking, and the app's
> polling fallback all work with just `php artisan serve` — clients still converge on seat state via
> React Query's `refetchInterval`, just without the sub-second push.

### API documentation — Scribe

Browsable HTML docs (with try-it-out), plus an OpenAPI spec and a Postman collection, are generated
from the routes, Form Requests, and PHPDoc:

```bash
php artisan scribe:generate
```

Then open **http://127.0.0.1:8000/docs** while `php artisan serve` is running.

### Laravel MCP (dev tooling)

The API exposes a **dev-only** MCP server (read-only tools over the seed data) for AI-assisted
development and testing. It is registered **only outside `production`**, so it never ships live:

- Endpoint: **http://127.0.0.1:8000/mcp/cinema-dev** (Streamable HTTP MCP transport).

Point an MCP client (e.g. Claude) at that URL while the API is served. See `api/routes/ai.php`.

### Tests

The graded core (FCFS concurrency, atomic booking, broadcasts) is covered by the suite:

```bash
php artisan test
```

---

## 2) App — `app/` (Expo / React Native)

Every screen from the flowchart, built with Expo Router. **React Query** owns server data (and the
polling fallback); **Zustand** owns the booking draft. The data layer is **pluggable**: a `live` adapter
(HTTP → Laravel) or a `mock` adapter (bundled `mock/*.json`), selected at runtime, with **automatic
fallback to `mock`** when the API is unreachable — so the app is always demoable offline.

### Setup

```bash
cd app

# 1. Install dependencies
npm install

# 2. Create the env file
cp .env.example .env
```

`.env` (Expo inlines `EXPO_PUBLIC_*` at build time):

```bash
# Base URL of the Laravel API (no trailing slash).
# iOS simulator / web: 127.0.0.1 is fine.
# Android emulator: use http://10.0.2.2:8000
# Physical device: use your Mac's LAN IP (e.g. http://192.168.100.247:8000) for BOTH lines below.
EXPO_PUBLIC_API_URL=http://127.0.0.1:8000

# Data source: `live` (HTTP → Laravel) or `mock` (bundled JSON). Default: mock.
EXPO_PUBLIC_DATA_SOURCE=live
```

### Run

```bash
npx expo start
# or: npm start
```

Then press **`w`** (web), **`i`** (iOS simulator), or **`a`** (Android emulator), or scan the QR code
with the Expo Go app on a physical device.

### Switching the data source — `live | mock`

The source is read from `EXPO_PUBLIC_DATA_SOURCE` in `app/.env`:

- **`mock`** — reads bundled `app/mock/*.json`, no network, fully offline. (The seat map is static —
  real-time locking is a live-only feature.)
- **`live`** — calls the Laravel API. **Strictly live: failures surface to the UI; it does NOT
  silently fall back to mock.** (Default when `EXPO_PUBLIC_DATA_SOURCE` is unset is `mock`.)

Edit the value in `.env`, then **restart Expo** (stop and re-run `npx expo start`) so the new value is
inlined.

**Physical device:** set `EXPO_PUBLIC_API_URL` and `EXPO_PUBLIC_REVERB_HOST` to your Mac's LAN IP
(e.g. `192.168.100.247`), run `php artisan serve --host=0.0.0.0` and `php artisan reverb:start`,
keep phone + Mac on the same Wi-Fi, and allow the macOS firewall prompt for `php`.

### Demo: real-time seat lock across two clients

This shows the FCFS lock propagating live (Success Criteria #1 and #2):

1. Start the API with **all three** processes (`serve` + `reverb:start` + `queue:listen`) and seeded data.
2. In `app/.env`, set `EXPO_PUBLIC_DATA_SOURCE=live` (and an `EXPO_PUBLIC_API_URL` both clients can reach).
3. `npx expo start`, then open **two clients** on the **same showtime's seat screen** — e.g. press
   `w` for a web client and `i`/`a` for a simulator (or scan with a second device).
4. On **client A**, tap seat **A3**. Within ~1s, **client B** shows A3 as held (greyed, unselectable).
5. Have both clients tap the **same free seat** at once → exactly **one** succeeds, the other gets a
   `409` and the seat shows held. Releasing or letting the 5-minute TTL expire frees it again — and the
   release re-broadcasts as available.

> If Reverb or the queue worker is not running, the same convergence still happens via the **polling
> fallback**, just a few seconds slower instead of sub-second.

---

## 3) Documents — `documents/`

A self-contained docs site rendering the `ai-context` Markdown (the single source of truth) with Mermaid
diagrams — the **ERD**, the **seat state machine**, and the **booking flow**. No build step.

```bash
# from the repo root — pick any static server
npx serve documents                          # → http://localhost:3000
# or
python3 -m http.server 8088 -d documents     # → http://localhost:8088
```

Open the printed URL. Serve over HTTP (not `file://`) so the viewer can fetch the local Markdown. See
`documents/README.md` for details and how to re-sync from `ai-context`.

---

## Quick start (all three, from a clean checkout)

```bash
# DB
mysql -u root -e "CREATE DATABASE IF NOT EXISTS sdp_cinema;"

# API
cd api && composer install && cp .env.example .env && php artisan key:generate \
  && php artisan migrate --seed
php artisan serve            # terminal 1
php artisan reverb:start     # terminal 2  (real-time)
php artisan queue:listen     # terminal 3  (real-time)

# App
cd ../app && npm install && cp .env.example .env   # set EXPO_PUBLIC_DATA_SOURCE=live
npx expo start

# Docs
cd .. && npx serve documents
```

---

## Architecture at a glance

- **Stateless API** — no server session/memory; every request reconstructs state from the DB + bearer token.
- **Seat status is derived per showtime**, never a flag on the seat row: a seat is `booked` if a
  `booking_seats` row exists, `held` if a live non-expired `seat_locks` row exists, else `available`.
- **FCFS locking** — `UNIQUE(showtime_id, seat_id)` on `seat_locks` makes acquisition atomic; the loser
  gets `409`. Holds carry a ~5-minute TTL and auto-release.
- **Confirm = atomic transaction** — booking → confirmed, `booking_seats` insert, and `seat_locks`
  delete all succeed or all roll back; `booking_seats` is the only source of truth for *sold* seats.
- **Pricing (Option B)** — a seat's price = `lookup(showtime.tier, seat.type)` from `seat_type_prices`;
  a tier card's range is the MIN..MAX of that tier's seat-type prices. Currency **RM (MYR)**.

Full ERD, state machine, flowchart, and invariants live in **`documents/`** (section *Architecture &
Diagrams*).
