# mock/

Bundled JSON fixtures for the `mock` data source (`EXPO_PUBLIC_DATA_SOURCE=mock`).

These mirror the live Laravel API response shapes (`{ data }` envelopes) so screens
never know which source served them. The data layer also falls back here when a `live`
request fails, keeping the app demoable offline.

Fixtures are populated by the Contract agent — keep filenames aligned with API endpoints
(e.g. `movies.json`, `showtimes.json`, `seatmap.json`, `food.json`).
