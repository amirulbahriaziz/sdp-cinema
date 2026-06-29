// SDP Cinema — runnable build workflow.
// Run via the Workflow tool:  Workflow({ scriptPath: ".../sdp-cinema/build.workflow.mjs" })
// 2 parallel tracks (api ∥ app), each a sequential pipeline; contract gate; integrate/docs/verify.

export const meta = {
  name: 'sdp-cinema-build',
  description: 'Build SDP Cinema: Laravel 12 API + Expo app + realtime FCFS seat-lock, per ai-context specs',
  phases: [
    { title: 'Scaffold', detail: 'composer create-project + create-expo-app' },
    { title: 'Contract', detail: 'API contract -> mock JSON + seed plan (gate)' },
    { title: 'Build', detail: 'api pipeline ∥ app pipeline' },
    { title: 'Integrate', detail: 'live data + Echo seat channel + polling' },
    { title: 'Docs', detail: 'README + documents/ from ai-context' },
    { title: 'Verify', detail: 'FCFS / TTL / 2-client / full-flow' },
  ],
}

const ROOT = '/Users/amirul/Documents/workspace/sdp-cinema'
const CTX  = '/Users/amirul/Documents/graminis/02-work/01-project/sdp-cinema/artifacts/ai-context'

const PRE = `You are building the SDP Cinema booking app. SPECS ARE AUTHORITATIVE — read these first:
- ${CTX}/project-overview.md
- ${CTX}/architecture.md   (ERD, seat state machine, Mermaid diagrams, invariants)
- ${CTX}/ui-context.md     (14 screens, dark theme, component inventory)
- ${CTX}/code-standards.md (Laravel route->controller->service->model; auth+validation ONLY in Form Requests; money=int minor + currency RM, no float; RQ for server data, Zustand for booking draft)
- ${CTX}/ai-workflow-rules.md
- ${CTX}/progress-tracker.md (decisions/resolutions)

BUILD INTO: ${ROOT}  (monorepo: api/ Laravel 12, app/ Expo, documents/, README.md).
ENV (already set up): PHP 8.4, Composer, Node 24. MySQL running, DB 'sdp_cinema' (root, NO password, host 127.0.0.1). NO Redis — seat locks use a DB UNIQUE(showtime_id, seat_id) constraint (atomic, FCFS), NOT Redis. Laravel 12 latest stable. Currency RM (MYR). Price model = Option B (price_tiers + seat_type_prices; seat price = lookup(showtime.tier, seats.type)).

COMMIT POLICY (assessment §5 — commit frequently): when your slice works, commit it:
  git -C ${ROOT} add -A && git -C ${ROOT} commit -m "<type>(<scope>): <verbose subject>"
Conventional Commits, small + verbose, explain the why in the body when non-obvious. NO co-author, NO AI branding. Do NOT push.

Stay within your assigned slice. Return a SHORT report (files added, what works, any blockers) — your final message is data, not prose.`

const CONTRACT_SCHEMA = {
  type: 'object',
  properties: {
    endpoints: { type: 'array', items: { type: 'object', properties: {
      method: { type: 'string' }, path: { type: 'string' }, desc: { type: 'string' } },
      required: ['method', 'path'] } },
    mockFiles: { type: 'array', items: { type: 'string' } },
    notes: { type: 'string' },
  },
  required: ['endpoints', 'mockFiles'],
}

const VERIFY_SCHEMA = {
  type: 'object',
  properties: {
    fcfs: { type: 'boolean' }, ttl: { type: 'boolean' },
    twoClient: { type: 'boolean' }, fullFlow: { type: 'boolean' },
    testsRun: { type: 'string' }, issues: { type: 'array', items: { type: 'string' } },
  },
  required: ['fcfs', 'ttl', 'fullFlow', 'issues'],
}

// ---------- Scaffold (parallel) ----------
phase('Scaffold')
const scaffold = await parallel([
  () => agent(`${PRE}

TASK — Scaffold the Laravel 12 API in ${ROOT}/api:
- composer create-project laravel/laravel api  (latest stable = 12)
- In api/: install Reverb (php artisan install:broadcasting --reverb, or composer require laravel/reverb then install), Sanctum (composer require laravel/sanctum + publish + migrate config), Scribe (composer require --dev knuckleswtf/scribe + php artisan vendor:publish for scribe config), Laravel MCP (composer require laravel/mcp — dev tooling).
- Configure .env: DB_CONNECTION=mysql, DB_DATABASE=sdp_cinema, DB_HOST=127.0.0.1, DB_USERNAME=root, DB_PASSWORD= (empty), BROADCAST_CONNECTION=reverb. Run php artisan key:generate.
- Do NOT write business migrations/controllers yet (later agents do). Just a clean, booting skeleton.
- Verify: php artisan about (or migrate on default tables) runs without error.
- Commit: chore(scaffold-api): scaffold Laravel 12 with reverb, sanctum, scribe, mcp`,
    { label: 'scaffold:api', phase: 'Scaffold' }),

  () => agent(`${PRE}

TASK — Scaffold the Expo app in ${ROOT}/app:
- npx create-expo-app@latest app  (use the default Expo Router template — file-based routing; this supersedes the 'React Navigation' mention in ui-context).
- Install deps: @tanstack/react-query, zustand, laravel-echo, pusher-js, axios. Use npx expo install for any native ones.
- Set up app/.env (or app config) with EXPO_PUBLIC_API_URL and EXPO_PUBLIC_DATA_SOURCE=mock (live|mock toggle).
- Create app/mock/ dir (fixtures filled by the Contract agent next).
- Verify the app type-checks / starts bundling (npx expo export or a quick metro start that you then stop). Do NOT leave a server running.
- Commit: chore(scaffold-app): scaffold Expo app with react-query, zustand, echo, axios`,
    { label: 'scaffold:app', phase: 'Scaffold' }),
])

// ---------- Contract (gate) ----------
phase('Contract')
const contract = await agent(`${PRE}

TASK — Define the API CONTRACT (the single source both api and app build against):
- Read the ERD + endpoints in architecture.md. Finalize the REST endpoint list (stateless):
  GET /movies, GET /movies/{id}, GET /cinemas, GET /showtimes (filter movie/cinema/date),
  GET /showtimes/{id}/seats (status: available|held|booked), POST /showtimes/{id}/seats/{seatCode}/lock,
  DELETE /showtimes/{id}/seats/{seatCode}/lock, GET /food-items, POST /bookings, auth (register/login/logout).
- Write ${ROOT}/CONTRACT.md documenting each endpoint: method, path, request, response JSON shape, status codes (incl. 409 on lock conflict). Money fields = integer minor units + currency "RM".
- Write realistic ${ROOT}/app/mock/*.json fixtures matching those response shapes (movies.json, movieDetails, cinemas, showtimes, seats, food-items, etc.) — Venom + a couple more movies, one cinema/hall, seat grid A-H, Classic/Premium tiers with seat_type_prices, food combos. These ARE the app's mock data source.
- Commit: docs(contract): define REST API contract + mock JSON fixtures
Return the endpoint list + mock file names.`,
  { label: 'contract', phase: 'Contract', schema: CONTRACT_SCHEMA })

// ---------- Build: api pipeline ∥ app pipeline ----------
phase('Build')
const [apiOut, appOut] = await parallel([
  // ===== API track (sequential — shares routes/composer) =====
  async () => {
    const a1 = await agent(`${PRE}

API STEP 1 — Schema + seeders (api/). Follow the ERD in architecture.md EXACTLY:
tables: users, movies, movie_reviews, cinemas, halls, seats(UNIQUE hall_id+seat_code, type standard|premium),
price_tiers(name, currency RM), seat_type_prices(tier_id, seat_type, price int minor),
showtimes(tier_id FK), seat_locks(UNIQUE showtime_id+seat_id, expires_at), bookings(amounts int minor + currency),
booking_seats(UNIQUE showtime+seat, unit_price), food_items, booking_food_items, payments.
- Eloquent models + relationships (models are data+relations only — no business logic).
- Factories + seeders with demo data (Venom etc., one cinema/hall, full A-H seat grid, Classic/Premium tiers + seat_type_prices, food combos). Match the mock fixtures where possible.
- Run: php artisan migrate:fresh --seed  against MySQL sdp_cinema. Must succeed.
- Commit: feat(api-schema): migrations, models, seeders for full ERD`,
      { label: 'api:schema', phase: 'Build' })

    const a2 = await agent(`${PRE}

API STEP 2 — Read endpoints (api/). Schema already migrated+seeded.
- Implement (route -> controller -> service -> model; thin controllers; API Resources for JSON):
  GET /movies, GET /movies/{id} (with reviews/casts), GET /cinemas, GET /showtimes (filters),
  GET /showtimes/{id}/seats (DERIVE status per showtime: seats LEFT JOIN booking_seats=booked, active seat_locks=held, else available; include price via lookup(tier, seat.type)),
  GET /food-items. Form Requests for any query validation. Consistent JSON {data:...}. Money int minor + RM.
- Quick smoke each route (php artisan serve in background OR a tinker/test hit), then stop server.
- Commit: feat(api-reads): movie/cinema/showtime/seat-map/food endpoints`,
      { label: 'api:reads', phase: 'Build' })

    const a3 = await agent(`${PRE}

API STEP 3 — THE GRADED CORE: seat-lock + booking (api/).
- SeatLockService: acquire(showtime, seatCode, holder) = atomic INSERT into seat_locks relying on UNIQUE(showtime_id, seat_id); on duplicate -> 409 Conflict (FCFS, first writer wins). release() deletes the hold. TTL ~5 min via expires_at; a prune (scheduled command or on-read filter) frees expired holds.
- Endpoints: POST /showtimes/{id}/seats/{seatCode}/lock (200/409), DELETE .../lock.
- BookingService::confirm() in a DB TRANSACTION: booking pending->confirmed, INSERT booking_seats (unit_price = lookup(tier, seat.type)), DELETE the seat_locks, dummy payment row (no gateway), compute totals (int minor, RM, never float). POST /bookings.
- Broadcast event SeatStatusChanged{showtime_id, seat_code, status} on a per-showtime channel via Reverb, on every lock/release/confirm.
- Add a Pest/PHPUnit test proving two concurrent locks on one seat => one 200, one 409.
- Run the test (php artisan test --filter Seat). Commit: feat(api-seatlock): FCFS seat-lock, atomic booking, Reverb broadcast + concurrency test`,
      { label: 'api:core', phase: 'Build' })

    const a4 = await agent(`${PRE}

API STEP 4 — Auth + MCP + Scribe (api/).
- Sanctum token auth: register/login/logout endpoints (Form Request validation+authorization). Protect mutating routes (lock, bookings) with auth:sanctum. Stateless — bearer token, no session.
- Laravel MCP: register a small dev-only MCP server exposing a couple of read tools (e.g. list showtimes / seat map) for AI-assisted dev. Guard so it is dev-only.
- Scribe: annotate controllers minimally, run php artisan scribe:generate, confirm docs render at /docs.
- Commit: feat(api-auth): sanctum auth, laravel-mcp dev server, scribe docs`,
      { label: 'api:auth', phase: 'Build' })

    return { a1, a2, a3, a4 }
  },

  // ===== APP track (sequential — shares navigation/theme) =====
  async () => {
    const b1 = await agent(`${PRE}

APP STEP 1 — Foundation (app/). Build against mock JSON (EXPO_PUBLIC_DATA_SOURCE=mock).
- theme.ts tokens (dark theme + seat-status colors) per ui-context.md.
- Data layer: one data interface with two adapters — live (axios -> EXPO_PUBLIC_API_URL) and mock (reads app/mock/*.json). A single switch picks the source; on a live failure FALL BACK to mock. React Query provider + query hooks call the active adapter (source-agnostic).
- Zustand booking-draft store (selected seats, F&B, totals/current step).
- Expo Router layout + shared components: Screen, MovieCard, Carousel, SearchBar, BottomNav, Tabs, SeatMap, SeatCell, SeatLegend, QtyStepper, PrimaryButton, SummaryRow.
- Commit: feat(app-foundation): theme, data layer (live|mock + fallback), RQ, zustand, components`,
      { label: 'app:foundation', phase: 'Build' })

    const b2 = await agent(`${PRE}

APP STEP 2 — Discovery screens (app/), using the foundation + mock data:
- Home: greeting, search bar, carousels New Releases / Popular / Recommended, bottom nav.
- Movie Details: trailer placeholder, title/tags/rating, tabs Movie Details | Ratings & Reviews (star breakdown + review cards), Book Ticket CTA -> navigates to Ticket Booking.
- Match ui-context.md (dark theme, component-based). Commit: feat(app-discovery): home + movie details + reviews screens`,
      { label: 'app:discovery', phase: 'Build' })

    const b3 = await agent(`${PRE}

APP STEP 3 — Booking screens (app/):
- Ticket Booking: two price-tier cards (Classic/Premium with real MIN..MAX range from seat_type_prices), Location + Cinema Hall dropdowns, date strip, time-slot chips, seat legend.
- Seat Selection: curved Screen, grid rows A-H, SeatCell colored by status (available/held/booked/selected), tapping selects + updates the Zustand draft + SUB-TOTAL (int minor, RM).
- Food & Beverage (skippable, Skip): Combo/Food-Snacks/Beverages tabs, QtyStepper, sub-total, Confirm.
- Booking Summary: ticket card, line items (tickets + F&B + service charge + promo), Total Payable.
- Commit: feat(app-booking): ticket booking, seat grid, food & beverage, summary screens`,
      { label: 'app:booking', phase: 'Build' })

    const b4 = await agent(`${PRE}

APP STEP 4 — Payment + Confirmation (app/):
- Payment method screen (Debit card / Bank Transfer / Crypto rows).
- Card payment screen (card no / expiry / CVV / Pay RM total) — dummy, no real gateway.
- Confirmation screen ("Congratulations", Main menu / View ticket).
- Wire the full draft -> POST /bookings (in live mode) / mock success; show totals consistently (int minor, RM).
- Commit: feat(app-payment): payment method, card (dummy), confirmation + checkout wiring`,
      { label: 'app:payment', phase: 'Build' })

    return { b1, b2, b3, b4 }
  },
])

// ---------- Integrate (∥ Docs) ----------
phase('Integrate')
const [integ, docs] = await parallel([
  () => agent(`${PRE}

TASK — Integrate real-time (needs api + app done):
- Switch app to live mode (EXPO_PUBLIC_DATA_SOURCE=live, EXPO_PUBLIC_API_URL to local API).
- Implement useSeatChannel(showtimeId): subscribe via laravel-echo + pusher-js to the Reverb per-showtime channel; on SeatStatusChanged patch the React Query seat-map cache (setQueryData). Polling fallback: refetchInterval on the seat-map query when the socket is down.
- Boot api (php artisan serve + reverb:start) and the app; smoke the full flow Home->Confirmation against the live API. Note anything that needs the user to run manually (two simulators for the 2-client demo).
- Commit: feat(integrate): live data source + Echo seat channel + polling fallback`,
    { label: 'integrate', phase: 'Integrate' }),

  () => agent(`${PRE}

TASK — Repo docs (independent of integration):
- Root README.md: detailed setup + run for BOTH apps (api: composer install, .env with MySQL sdp_cinema, key:generate, migrate --seed, reverb:start, serve, Scribe /docs, MCP; app: npm install, .env api url + data-source, expo start, live|mock toggle, two-client realtime demo; documents: build/serve).
- documents/ dir beside api/ and app/: a docs site generated FROM the ai-context markdown (${CTX}) with Mermaid diagrams (ERD, seat state machine, booking flow). Try Laradocs; if not an installable package, fall back to a plain Markdown docs site (NOT Larecipe) — copy/adapt the ai-context content + the Mermaid blocks.
- Commit: docs(repo): detailed README + documents/ site (from ai-context, mermaid)`,
    { label: 'docs', phase: 'Docs' }),
])

// ---------- Verify ----------
phase('Verify')
const verify = await agent(`${PRE}

TASK — Verify the build (report pass/fail honestly; fix small breakages you find):
- FCFS: run the concurrency test (two simultaneous locks on one seat -> one 200, one 409). php artisan test.
- TTL: a hold past expires_at frees the seat (test or manual).
- Full-flow smoke: boot api + reverb + app; walk Home -> Confirmation in mock and (if possible) live mode.
- 2-client note: state exactly how the user runs two clients to see A3 lock propagate.
- php artisan test must be green. Report what ran, results, and any remaining issues.
- Commit: test(verify): concurrency/TTL/full-flow verification`,
  { label: 'verify', phase: 'Verify', schema: VERIFY_SCHEMA })

return { scaffold, contract, apiOut, appOut, integ, docs, verify }
