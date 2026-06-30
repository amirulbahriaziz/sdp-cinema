# Code Standards

## General

- Keep modules small and single-purpose; one screen / one controller / one event per file.
- Fix root causes, not symptoms — the seat-lock logic lives in one service, not per-caller.
- Do not mix unrelated concerns (e.g. payment + seat release) in one function.
- Commit small and verbose (assignment 5.0): meaningful messages readable from the log alone.

## Backend — Laravel 12

- **Stateless only.** No `session()`, no in-memory caches that affect correctness. Auth via Sanctum
  bearer tokens; reconstruct all state from DB/Redis each request.
- **Request flow: route -> controller -> service -> model.** Controllers are thin (delegate to
  services); **services** hold all business logic; **models** are Eloquent data + relations only.
- **Authorization AND validation live ONLY in Form Request classes** — never in controllers or
  services. Controllers receive an already-validated, already-authorized request.
- Seat acquisition must be **atomic** — DB unique-constraint insert or `Redis SET NX EX`; never
  check-then-write (race). On conflict return `409`.
- Confirm/booking mutations run inside a **DB transaction**.
- Broadcast a domain event (`SeatStatusChanged`) on every lock/release/book; controllers never push
  sockets directly.
- Migrations define every constraint that protects an invariant (uniques, FKs, not-nulls).
- Return **consistent JSON shapes**: `{ data }` on success, `{ message, errors }` on failure; correct
  HTTP status codes (200/201/409/422/401/404).
- Document each endpoint with **Scribe** (PHPDoc-style comments; it also infers from routes +
  Form Requests). Scribe emits a browsable HTML page, an OpenAPI spec, and a Postman collection.

## Frontend — React Native (Expo)

- **Component-based**: presentational components are pure + theme-driven; screens compose them.
- **State**: **React Query** for all server data (movies, showtimes, seat map) — fetching, caching,
  and the polling fallback (`refetchInterval`). **Zustand** for client-only state — the booking
  draft (selected seats, F&B, totals, current step). RQ owns server truth; Zustand owns user intent.
- **Data source is pluggable: `live` (HTTP -> Laravel) or `mock` (bundled `mock/*.json`).** Both
  implement the same data interface; React Query `queryFn` calls the active adapter. Mode is
  selectable at runtime (config / dev toggle), and on a live failure the layer **falls back to
  `mock`** so the app stays demoable offline. Screens never know which source served them.
- Realtime: one `useSeatChannel(showtimeId)` hook subscribes via echo + patches the **React Query
  seat-map cache** (`setQueryData`); the polling fallback (`refetchInterval`) covers a dropped socket.
- No hardcoded colors/spacing — read from `theme.ts`.
- Keep navigation flat and declarative (React Navigation stack mirroring the flowchart).

## API Contract

- Validate + parse input before logic; enforce token auth before any mutation.
- Seat status enum is shared vocabulary: `available | held | booked` (+ `selected` is client-only).
- Money stored as **integer minor units + a `currency` code** (values in **MYR / RM**; amount +
  currency on each row); never float arithmetic on totals. Seat price = `lookup(tier, seat.type)`
  from `seat_type_prices`.

## Data and Storage

- Durable entities -> relational DB. Transient holds -> `seat_locks`/Redis with TTL only.
- `booking_seats` is the only source of truth for *sold* seats; `seat_locks` never is.
- Seeders populate demo data (movies, cinemas, halls, seats, showtimes, food) — no manual DB edits.

## File Organization

- `api/app/Http/Controllers/` — thin controllers (validate + delegate).
- `api/app/Services/` — seat-lock + booking logic (the FCFS core).
- `api/app/Events/` — broadcast events (`SeatStatusChanged`).
- `api/database/{migrations,seeders}/` — schema + demo data.
- `app/src/screens/` — one file per flowchart screen.
- `app/src/components/` — reusable UI (SeatMap, MovieCard, QtyStepper, ...).
- `app/src/api/` — apiClient + React Query hooks; `app/src/realtime/` — echo + useSeatChannel.
- `app/src/store/` — booking-draft store; `app/src/theme.ts` — tokens.
- `documents/` — Laradocs site generated from `artifacts/ai-context`; sibling of `api/` and `app/`.

## Documentation

- All diagrams are authored in **Mermaid** (ERD `erDiagram`, seat status `stateDiagram-v2`, booking
  `flowchart`) — never image exports; diagrams live in version control as text.
- The `ai-context` markdown is the source of truth; the `documents/` (Laradocs) site is generated
  from it. When context changes, regenerate the site.
- Root `README.md` must document setup + run for **both** apps in detail (see ai-workflow-rules).
