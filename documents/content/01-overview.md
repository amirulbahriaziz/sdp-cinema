# SDP Cinema — Online Cinema Booking App

## Overview

An online cinema booking app whose defining feature is **real-time, first-come-first-serve seat
locking**: the instant one user starts booking a seat, every other user viewing that hall's seating
plan sees the seat lock and can no longer take it. Built as an interview assignment (Sime Property)
covering multiple role tracks — a Laravel API, a React Native (Expo) mobile client, and supporting
architecture/UX docs. The product is a movie-discovery + ticket + food-and-beverage purchase flow
ending in a confirmed booking.

## Goals

1. Demonstrate a **stateless** API that handles concurrent seat booking correctly — no double-books.
2. Deliver **real-time** seat-lock propagation across clients via WebSocket, with a polling fallback.
3. Ship a navigable Expo app that walks the full flowchart end to end against a real API.

## Core User Flow

1. Open app -> **Home** (search, New Releases, Popular, Recommended).
2. Search/pick a movie -> **Movie Details** (trailer, synopsis, casts, Ratings & Reviews tab).
3. Tap **Book Ticket** -> **Ticket Booking** (location, cinema hall, date, showtime/time slot).
4. **Seat selection** — pick seats on the live grid; selecting holds the seat (others see it lock).
5. **Food & Beverage** (skippable) — add combos/snacks/beverages.
6. **Booking Summary** — tickets + F&B + service charge + promo -> total.
7. **Payment** method -> **Card payment** (stubbed).
8. **Confirmation** — "Congratulations", view ticket / main menu.

## Features

### Discovery
- Home carousels (New Releases, Popular in cinemas, Recommended), search by movie or cinema hall.
- Movie details with trailer, metadata, synopsis, casts/director/writers, ratings & reviews.

### Booking + real-time locking
- Showtime selection (cinema, hall, date, time, price tier).
- Live seat grid (rows A-H): available / held-by-other / booked / your selection.
- Two **price tiers** (Classic/Premium) priced per **seat type**; each tier card shows a real
  MIN..MAX range. Currency **RM (MYR)**.
- **Seat lock**: selecting a seat acquires a TTL-bounded hold; FCFS — first writer wins, others 409.
- Real-time push of lock/release/book events to every client on that showtime (WebSocket + polling).

### Checkout
- Food & Beverage catalog (Combo / Food-Snacks / Beverages) with qty steppers.
- Booking summary with totals, promo code, service charge.
- Payment method selection + card form (stub) -> confirmation.

## Scope

### In Scope
- Laravel 12 REST API (stateless), Reverb WebSocket broadcasting, Sanctum auth, Scribe API docs.
- Expo (React Native) app for every screen in the flowchart.
- Seat-lock concurrency + real-time propagation + TTL auto-release.
- Seed data (movies, cinemas, halls, seats, showtimes, food items).
- **Selectable data source** in the app — live API or bundled JSON — with automatic fallback to
  JSON when the API is unreachable (covers 4.1 JSON endpoint + 4.4 real API).

### Out of Scope
- Real payment gateway integration — payment is a **stub** (confirmation on simulated success).
- Production auth flows (social login, password reset), email/SMS delivery of tickets.
- Admin/CMS for managing movies and showtimes (seeders only).
- A `documents/` docs site (**Laradocs**) generated from the `ai-context` docs, with **Mermaid**
  diagrams, plus a detailed root `README.md` (setup + run for api and app).
- **Laravel MCP** (`laravel/mcp`) wired into the API for AI-assisted dev/testing.
- The doc/PPT deliverables (4.5 ADM / 4.6 UI/UX / 4.2 Lead) are produced LAST, after code.

## Success Criteria

1. Two clients on the same showtime: locking seat A3 on client 1 makes A3 show locked on client 2
   within ~1s (WebSocket), and still reflects via polling if the socket is down.
2. Two simultaneous lock requests for the same seat -> exactly one `200`, one `409` (FCFS).
3. A held seat auto-releases after its TTL and re-broadcasts as available.
4. The Expo app completes the full flow Home -> Confirmation against the live Laravel API.
5. `php artisan test` passes; Scribe generates a browsable API doc page for every endpoint.
