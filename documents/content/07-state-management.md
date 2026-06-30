# State Management & Components

> Maps to assignment **§4.1** — *components-based design and practical state management*.

## Component-based design

Screens **compose** small, reusable, theme-driven components — they never re-implement UI. ~26
app components live in `app/src/components/` and read tokens from one source (`app/src/theme.ts`),
so there are no hardcoded colors/spacing.

- **Discovery:** `MovieCard`, `Carousel`, `SearchBar`, `RatingStars`, `RatingBreakdown`, `ReviewCard`
- **Booking:** `SeatMap` / `SeatCell` / `SeatLegend`, `Dropdown`, `DateStrip`, `TimeSlotChip`,
  `PriceTierCard`, `QtyStepper`, `FoodItemCard`, `PriceTotalBar`, `SummaryRow`, `InfoRow`
- **Shell / shared:** `Screen`, `StepHeader`, `PrimaryButton`, `WizardFooter`, `Tabs`,
  `ResultScreen`

Example composition — the Seat screen is just `Screen( StepHeader + SeatLegend + SeatMap, footer:
WizardFooter( PriceTotalBar ) )`. Duplication was actively removed by extracting `InfoRow`,
`WizardFooter` and a `surfaceCard()` style helper.

## Practical state management — the right tool per kind of state

State is split by **who owns the truth**, not dumped in one global store:

| State kind | Tool | Lives in | Notes |
|---|---|---|---|
| **Server data** (movies, showtimes, **seat map**, bookings) | **React Query** | `src/api/hooks.ts`, `src/api/query-client.ts` | caching (`staleTime`), `onSettled` invalidation, polling fallback (`refetchInterval`), realtime patch (`setQueryData`) |
| **Client intent / booking draft** (selected seats, F&B, promo, step, totals) | **Zustand** | `src/store/booking.ts` | `useShallow` for derived totals (avoids a re-render loop) |
| **Local UI** (search text, dropdown open, active tab) | `useState` | the screen | ephemeral, component-scoped |
| **Encapsulated logic** | custom hooks | `src/lib`, `src/realtime` | `useCancelBooking`, `useSafeBack`, `useSeatChannel` |

**React Query owns "what the server says"; Zustand owns "what the user is doing."** Screens read
both and render them together (e.g. `SeatMap` overlays the client-only `selected` status on top of
the server `available/held/booked`).

## Practical patterns demonstrated

- **Optimistic UI + rollback** — selecting a seat flips it instantly, then acquires the server lock;
  a lost FCFS race (`409`) rolls the selection back with a message (`booking/seats`).
- **Invalidate-on-mutation** — lock / release / book / cancel mutations invalidate the seat-map
  query so every screen converges.
- **Realtime → cache** — `useSeatChannel` patches the React Query cache via `setQueryData`; a
  polling fallback (`refetchInterval`) covers a dropped socket.
- **Source-agnostic data layer** — one interface, two adapters (`live` HTTP, `mock` JSON) selected
  by `EXPO_PUBLIC_DATA_SOURCE`; screens/hooks never know which served them (`src/data/`).
- **No prop-drilling for cross-screen state** — the multi-step booking draft lives in the Zustand
  store, so the wizard steps stay decoupled.

See **Data Flow** for the end-to-end request → fetch → store → render loop.
