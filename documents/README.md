# SDP Cinema — `documents/`

A self-contained docs site for the SDP Cinema booking app. It renders the project's
**single source of truth** — the `ai-context` Markdown — as a navigable, dark-themed site
with **Mermaid** diagrams (ERD, seat state machine, booking flow).

## Why this and not Laradocs / Larecipe

The spec's first choice was **Laradocs**. The only real package by that name
(`petebishwhip/laradocs`) is a *Laravel* package that installs **into** an existing Laravel
app — it is not a drop-in static generator for a sibling `documents/` folder, and standing up a
second Laravel app purely to host docs is disproportionate. Per the sanctioned fallback, this is a
**plain Markdown docs site** (explicitly **not** Larecipe): a single `index.html` viewer plus the
copied `ai-context` Markdown. No build step, no framework, no `node_modules`.

## What's here

```
documents/
├── index.html          # the docs viewer (sidebar + content pane)
├── assets/
│   ├── app.js          # hash router; loads content/*.md, renders Markdown + Mermaid
│   └── styles.css      # dark theme mirroring the app's ui-context palette
├── content/            # rendered mirror of artifacts/ai-context (source of truth)
│   ├── 01-overview.md
│   ├── 02-architecture.md   # ← ERD, seat state machine, booking flow (Mermaid)
│   ├── 03-ui.md
│   ├── 04-code-standards.md
│   └── 05-workflow.md
└── README.md
```

`marked`, `mermaid`, and `highlight.js` are loaded from CDN at view time (an internet connection
is needed to render). The Markdown is fetched locally.

## Build / serve

There is nothing to build. The viewer fetches local Markdown, which browsers block on the
`file://` protocol, so **serve it over HTTP** with any static server:

```bash
# from the repo root — pick one
npx serve documents              # → http://localhost:3000
# or
python3 -m http.server 8088 -d documents   # → http://localhost:8088
# or
php -S 127.0.0.1:8088 -t documents         # → http://127.0.0.1:8088
```

Then open the printed URL. Use the sidebar to move between sections; every page is deep-linkable
(e.g. `#/architecture`).

## Keeping it in sync

`artifacts/ai-context/*.md` remains the single source of truth. When a context doc changes,
refresh the mirror:

```bash
AIC="<graminis>/02-work/01-project/sdp-cinema/artifacts/ai-context"
cp "$AIC/project-overview.md"   content/01-overview.md
cp "$AIC/architecture.md"       content/02-architecture.md
cp "$AIC/ui-context.md"         content/03-ui.md
cp "$AIC/code-standards.md"     content/04-code-standards.md
cp "$AIC/ai-workflow-rules.md"  content/05-workflow.md
```
