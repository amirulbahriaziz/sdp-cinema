# SDP Cinema — Presentation Deck Outline

Slide-by-slide content for the interview PPT, grounded in the actual build. Three parts:
**Part A — the Cinema App**, **Part B — Mobile Development Lead (§4.2)**, **Part C — Application
Development Manager (§4.5)**. Each `## Slide` = one slide; bullets = body.
In **Part A**, every design section has a **"what"** slide followed by a separate **"Why"** slide
(answering why we chose each thing — may span 2 pages). (§4.6 UI/UX hi-fi = separate Figma work.)

---
# PART A — The Cinema App
---

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

---
# PART B — Mobile Development Lead (§4.2)
---

## Slide B1 — Scope note
- Architecture, state management, API & data-fetch strategy → **covered in Part A**
- This part: **performance · cross-platform · Android UI/UX · risks**

## Slide B2 — Performance optimization
- **Rendering**: `FlatList` virtualization; kill re-render storms with Zustand **selectors + `useShallow`**
  (fixed a real "Maximum update depth" loop this way)
- **Hermes** engine (Expo default) — fast cold start, low memory; precompiled bytecode, no JIT cost
- **Lazy loading**: Expo Router loads route bundles on navigation; `expo-image` for posters
- **Bottlenecks**: bloated re-renders → memo/selectors; big seat grid → keyed cells; network → RQ cache + `staleTime`

## Slide B3 — Cross-platform strategy
- One codebase; **platform-specific only where needed**: `Platform.OS`, `*.web.tsx`, `Platform.select`
- Concrete: Cancel uses native `Alert` vs web `window.confirm`; realtime needs the `netinfo` native dep
- iOS/Android-only features → isolate behind a hook/component; no-op or alternative on the other side

## Slide B4 — UI/UX: Android fragmentation
- **Density-independent layout**: flex + safe-area context, no hardcoded pixels
- **Theme tokens** (`theme.ts`) — one source for color / spacing / radius / type
- Responsive seat grid + scroll; tested across simulator sizes + a physical device
- Accessibility: roles/labels on pressables, hit-slop on small targets

## Slide B5 — Risks & mitigations (from experience)
- **Seat-lock race** → DB unique index arbitrates (not app code) → 409
- **Realtime outage** → best-effort broadcast + **polling fallback**
- **Abandoned holds** → TTL auto-expiry
- **API/app contract drift** → typed contract; align resources (hit + fixed several this build)
- **Scale** → stateless API (horizontal) + separate WS tier

---
# PART C — Application Development Manager (§4.5)
---

## Slide C1 — Monolith vs microservices
- **Recommend: modular monolith** (Laravel) for this scope
- Why: single domain, small team, faster delivery, local transactions, simpler ops
- Realtime (Reverb) already a **separate tier**; extract payments/notifications/catalog later **if** load/teams demand

## Slide C2 — Tech proposal + rationale (manager lens)
- **Backend** Laravel 12 · **Mobile** React Native/Expo (hybrid, one codebase) · **DB** MySQL · **Realtime** Reverb
- Per-choice rationale → Part A; trade-offs acknowledged (Django / Flutter / native viable)
- Optimizes realtime + delivery speed + team fit

## Slide C3 — Cloud & infrastructure
- **API**: container/VM or serverless (stateless REST scales well)
- **Realtime**: an **always-on instance** — WebSocket needs a persistent process (poor serverless fit)
- **Services**: managed MySQL · object storage + CDN (posters) · TLS
- **VM vs serverless → hybrid**: serverless/containers for API, always-on for WS

## Slide C4 — Data model (ERD)
- Paste the **Mermaid ERD** (from `documents/` Architecture page)
- Core: movies · cinemas · halls · seats · price_tiers · seat_type_prices · showtimes ·
  **seat_locks** (transient hold) · bookings · booking_seats (sold) · booking_food_items · payments
- Key: status derived per (showtime×seat); hold→sold = the two-table FCFS mechanism

## Slide C5 — Headcount, cost & timeline (indicative)
- **Team**: 1 BE (Laravel) · 1 Mobile (RN) · 0.5 UI/UX · 0.5 QA · 0.5 PM/Lead
- **Timeline (~8–10 wks)**: design/ERD/contract (1–2) · build API+app (3–6) · realtime+integrate (7) ·
  hardening/test (8) · UAT + store submission (9–10)
- **Cost drivers**: team (bulk) · modest cloud (1 API + 1 DB + 1 WS) · app-store fees

## Slide C6 — Summary
- Met §1.0: real-time FCFS seat locking, end to end
- Stateless API · component-based app · practical state management · realtime + fallback
- Clear scale path: modular monolith → extract; stateless API; separate WS tier
