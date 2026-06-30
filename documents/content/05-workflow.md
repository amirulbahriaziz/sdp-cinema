# AI Workflow Rules

## Approach

Build this project incrementally against the specs in these context files. The context files define
what to build (`project-overview.md`), how it is structured (`architecture.md`), how it looks
(`ui-context.md`), and the rules (`code-standards.md`). Implement against these specs — do not infer
or invent product behavior from scratch. The graded core is the real-time FCFS seat lock; everything
else serves the flowchart around it.

## Build Sequence (code first, docs last)

0. **Context docs finalized + user-confirmed** (this gate). No code until confirmed.
1. **API (4.3)** — Laravel 12 scaffold, migrations + ERD, seeders, seat-lock service + Reverb
   broadcast, Sanctum, Scribe API docs. Prove concurrency with a test.
2. **App (4.1)** — Expo scaffold; every screen against bundled `mock/*.json`; navigation, components,
   state management.
3. **Wire real-time (4.4)** — point the app at the live API; echo subscribe + polling fallback;
   demonstrate a lock propagating across two clients.
4. **Repo docs** — detailed root `README.md` (below) + `documents/` Laradocs site generated from
   `ai-context` with Mermaid diagrams.
5. **Docs last (4.5 ADM, 4.6 UI/UX, 4.2 Lead)** — architecture/ERD/cost/timeline deck, hi-fi
   prototypes + usability plan, lead deck. Per user instruction, deferred to the end.

## README (root) — required contents

Document, in detail, how to set up and run each app:

- **api/** (Laravel 12): PHP/Composer versions, `composer install`, `.env` + `APP_KEY`, **MySQL**
  database setup, `php artisan migrate --seed`, **Reverb** start (`php artisan reverb:start`),
  `php artisan serve`, how to view **Scribe** docs and the **Laravel MCP** endpoint.
- **app/** (Expo): Node version, `npm install`, `.env` (API base URL + data-source mode), `npx expo
  start`, how to switch **live | mock** data source, running two clients to demo the real-time lock.
- **documents/**: how to build/serve the Laradocs site.

## Scoping Rules

- Work one feature unit at a time; small verifiable increments over large speculative changes.
- Do not combine unrelated system boundaries (`api/` vs `app/`) in a single step.
- Commit per unit, small and verbose (assignment 5.0); open a PR on `amirulbahriaziz/sdp-cinema`
  when a slice is complete.

## When to Split Work

Split a step if it combines:

- Backend schema/logic changes and frontend UI changes.
- Multiple unrelated API resources or multiple screens.
- Behavior not yet defined in the context files.

If a change cannot be verified end to end quickly, the scope is too broad — split it.

## Handling Missing Requirements

- Do not invent product behavior absent from the context files.
- Resolve ambiguity in the relevant context file before implementing.
- Log unresolved decisions as open questions in `progress-tracker.md` before continuing.

## Protected Files

Do not modify unless explicitly instructed:

- The wireframe/flowchart source PDF in `artifacts/`.
- Third-party library internals; generated framework scaffolding you did not author.
- Cloned reference repos under `references/`.

## Keeping Docs in Sync

Update the relevant context file whenever implementation changes architecture, storage, conventions,
or scope. Update `progress-tracker.md` after every meaningful change.

## Before Moving to the Next Unit

1. The current unit works end to end within its defined scope.
2. No invariant in `architecture.md` was violated (statelessness, seat uniqueness, broadcasts).
3. `progress-tracker.md` reflects the completed work.
4. Checks pass: `php artisan test` (api) and the Expo app builds/runs (app).
