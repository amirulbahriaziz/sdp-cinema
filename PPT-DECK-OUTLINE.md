# SDP Cinema — Presentation Deck Outline

Slide-by-slide content for the interview PPT, grounded in the actual build. Covers the
**Cinema App** — the project plus the design decisions. Each design section has a **"what"** slide
followed by a separate **"Why"** slide (answering why we chose each thing). Each `## Slide` = one
slide; bullets = body.

# The Cinema App

## Slide A1 — Title
- **SDP Cinema** — Online Cinema Booking App
- Real-time seat booking · Laravel 12 API + React Native (Expo)
- *(your name · role · date)*

## Slide A2 — The Problem (§1.0)
- Book cinema seats online; **lock a seat first-come-first-serve**
- While booking, **every other user sees the lock in real time** → no double-booking
- Flow: Home → Movie → Showtime → **Seats (live lock)** → F&B → Summary → Payment → Ticket
- Two needs drive everything: **concurrency** (one seat, many users) + **real-time** (push to others)

## Slide A3 — What was built
- **App** (Expo/RN) — all 14 wireframe screens, live API + realtime
- **API** (Laravel 12) — REST, stateless, Sanctum auth, Scribe docs
- **Realtime** — Reverb WebSocket + polling fallback
- **Extras** — My Tickets, cancel booking, live|mock data source, docs site
- *(insert demo / screen recording: two clients, seat goes grey live)*

---
## — Architecture —
---

## Slide A4 — Architecture (what)
- **Monorepo, 3 tiers**: Expo app ↔ Laravel REST API ↔ MySQL, with **Reverb** as a separate WebSocket tier
- **Stateless API** + DB-backed state → any instance serves any request
- **Layered** both sides — App: view → hooks → store → data · API: route → controller → service → model
- **Realtime** = WebSocket (Reverb) **primary** + REST polling **fallback**

## Slide A5 — Why: Architecture
- **Modular monolith, not microservices** — one bounded domain (bookings), small team, **atomic
  transactions**, simpler ops; service seams kept clean → extract later only if load/teams demand
- **Realtime as its own tier** — the only part that needs to scale independently (persistent sockets)
- **WebSocket over polling / HTTP2** — the lock must appear on others *instantly*; polling is laggy +
  chatty, so we need server **push**
- **Reverb** for the WebSocket — Laravel's first-party WS server, speaks the Pusher protocol, **no
  third-party account/cost**, pairs with Echo on the client
- **+ polling fallback** — if the socket drops, the grid still self-heals (resilience)

---
## — API —
---

## Slide A6 — API design (what)
- **REST** endpoints: movies, showtimes, **seat map**, lock/release, bookings, auth
- **Stateless**: Sanctum **bearer tokens**, no session; all state in MySQL
- **Layered**: route → controller → service → model; validation/authorization in **Form Requests**
- **FCFS lock**: atomic `INSERT` guarded by `UNIQUE(showtime, seat)` → loser **409**; **TTL** holds
  auto-expire; confirm promotes hold → `booking_seats` in one transaction
- **Docs**: Scribe (OpenAPI) · typed **PHP enums** for statuses

## Slide A7 — Why: API
- **Why Laravel 12** — batteries-included (Sanctum auth, Reverb WS, Eloquent, validation, queues) →
  fast, safe build with no library glue; vs Django (viable) — Laravel won on realtime fit + speed
- **Why MySQL** — relational fit for the ERD, and the **constraint *is* the concurrency design**:
  `UNIQUE(showtime, seat)` makes the DB itself guarantee one winner per seat

---
## — App —
---

## Slide A8 — App design (what)
- **Expo / React Native** — one codebase iOS / Android / **web**; **Expo Router** (file-based nav)
- **Component-based** — ~26 reusable, theme-driven components; screens just compose
- **Data layer** — source-agnostic adapters: `live` (HTTP) / `mock` (JSON), one interface
- **State split**: React Query (server) + Zustand (booking draft) + useState (local UI)
- **Optimistic** seat-select + rollback on 409; realtime cache patch + polling fallback

## Slide A9 — Why: App
- **Why Expo / RN** — single codebase across iOS/Android/web, OTA updates, huge ecosystem; vs Flutter
  (viable) — chosen for React/JS team fit + free web target
- **Why React Query (server state)** — caching, invalidation, polling fallback, realtime cache patch
  all belong to *server data*; React Query owns that so screens don't hand-roll fetch/cache
- **Why Zustand (client state)** — booking draft is *user intent*, not server data; Zustand is minimal,
  selector-based, no provider tree (`useShallow` for derived totals). **Not** Redux (boilerplate) /
  Context-everywhere (whole-tree re-renders)
