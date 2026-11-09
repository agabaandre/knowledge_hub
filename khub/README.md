# Knowledge Hub — Node.js + React

Modern split of the Laravel Knowledge Hub into a **backend** (Express + knex, MySQL or PostgreSQL) and a **frontend** (React + Vite), with a shared core package and a `sites/` folder for multi-site deployments.

```
khub/
  backend/        Express API, core services, feature modules, migrations, scripts
  frontend/       React SPA (Bootstrap 5, same look as the Laravel theme1 portal)
  shared/         @khub/shared — module catalogue, site config schema, SEO and URL helpers
  sites/          One folder per hub (default, nigeria, …) — configuration only, no data
```

## Quick start

```bash
cd khub
cp .env.example .env            # set DB_CLIENT=mysql2|pg and credentials
npm install
npm run migrate                 # creates tables in the default site database
npm run dev                     # API on :4000, web on :3000 (proxies /api to the API)
```

Open http://localhost:3000. Module toggles are at `/admin/modules` (admin login required). Server metrics are at `/admin/metrics`.

## Core concepts

### Sites (multi-site)
- A site is a folder in `sites/` with a `site.json` (name, path prefix, modules, branding, database).
- `khub.org/` serves `default`; `khub.org/nigeria/…` serves `nigeria` (set `SITE_RESOLUTION=host` for `nigeria.khub.org`).
- The backend strips the prefix, so every module sees site-relative URLs. The frontend uses the same prefix.
- Sites can use different databases (`database` in `site.json`) — MySQL for one, PostgreSQL for another.
- Add a site: create `sites/<id>/site.json`, create its database, `npm run migrate -- --site=<id>`, restart.
  See [sites/README.md](sites/README.md).

### Storage (same layout as Laravel)
Data never lives inside `sites/`. For each site:

```
{HUB_DATA_ROOT}/{storageId}/files/uploads/publications
{HUB_DATA_ROOT}/{storageId}/files/uploads/forums
{HUB_DATA_ROOT}/{storageId}/backups/sql
```

`storageId` is derived from `APP_URL` exactly like Laravel's `HubSiteIdentifier` (e.g. `localhost-8081`), so an existing `/var/khubdata/localhost-8081` folder is used as-is. Files are served at `/hub-media/…`, the same URL as before. Only the internal (host path) driver is implemented; cloud drivers (S3, Azure, GCS, SharePoint, SFTP) are not yet ported.

### Modules
Feature modules live in `backend/src/modules/<name>/` and are listed in `shared/src/modules.js`.

| Module | Backend | Frontend |
|---|---|---|
| publications (core) | search, resource, home, lookups, favourites, comments | search, resource, home |
| forums | list, detail, replies, likes | /forums |
| communities | list, detail | /communities |
| health-topics | topics, emergencies | /health-topics |
| events | upcoming, detail | /events |
| faqs | list | /faqs (FAQPage JSON-LD) |
| authors | list, profile + publications | — (not yet in the React UI) |
| countries | list, profile | — (not yet in the React UI) |
| courses, federation, ai-search | — | — (not yet ported) |

A module is on when `site.json` says so **and** no admin override turns it off (`module_settings` table). The frontend reads the enabled set from `/api/site/manifest`, so navigation and routes react without a rebuild. Disabled modules return 404 from the API.

### Database
- Knex with `mysql2` or `pg`. `DB_CLIENT` picks the driver for the default site; each site can override it.
- Migrations live in `backend/migrations/` and use dialect-neutral column types. Column names match Laravel.
- Differences are isolated in `backend/src/core/db/helpers.js` (random ordering, boolean coercion, LIKE escaping).

### Auth
Sessions are signed JWTs in an httpOnly cookie (also accepted as a bearer token). Laravel `$2y$` bcrypt hashes verify unchanged, so existing users keep their passwords after migration.

### SEO
- Per-page title, description, canonical, Open Graph, Twitter cards, robots, and JSON-LD (`Seo` component).
- Server-generated `robots.txt`, `sitemap.xml` (index + sections, same section names as Laravel), `sitemaps/{section}.xml`, and `llms.txt`.
- Citation meta tags on resource pages for Google Scholar.
- **Known gap:** pages are client-rendered. Crawlers that execute JavaScript (Google, Bing) index them; crawlers that do not will see only the shell. Server-side rendering of the React tree is the next step.

### AI integration (any model, any HTTP provider)
`backend/src/core/ai/` holds a provider-agnostic chat client with three wire formats — OpenAI-compatible (also DeepSeek, Mistral, Groq, OpenRouter, Ollama, vLLM, Azure), Anthropic, and Gemini — so a new provider is a config row, not code. Admins manage providers, models, base URLs and per-feature fallback chains at **/admin/ai** (keys stored server-side, masked in the UI, test-connection button). The `ai-search` module exposes `POST /api/ai-search/insights` (grounded summary of the top matches, cached, rate-limited) and `POST /api/ai-search/chat`; the search page shows the insights panel when the module is on.

### Themes (upload, switch, guidelines)
Theme switching is managed at **/admin/themes**: upload a zip (validated `theme.json` + optional `theme.css` + assets; 10 MB cap, styling-only file types, zip-slip safe), activate, delete. Tokens map to the portal's CSS variables so most themes need no CSS; stylesheets are served at `/hub-theme/{slug}/…` and stored under `{HUB_DATA_ROOT}/{site}/themes/` (data, not code). The package format lives in [docs/THEME_GUIDELINES.md](docs/THEME_GUIDELINES.md), also served on the admin page. A worked example (`sample-emerald`) is installed on the demo stack.

### Admin portal
`/admin` (dashboard with content/pending/user stats), `/admin/approvals` (approve/reject pending publications, forum threads and comments), `/admin/configure` (branding, SEO, contact, home-section toggles — an allowlisted subset of the `setting` row), `/admin/themes`, `/admin/ai`, `/admin/modules`, `/admin/metrics`.

### Localisation (AU official languages)
All six African Union official languages ship enabled: English, French, Arabic, Spanish, Portuguese and Kiswahili. The language list comes from the **current database** (`site_languages`, seeded by migration when empty, admin rows win); dictionaries are the Laravel lang files exported to `backend/i18n/{locale}.json` with English as the fallback layer. `GET /api/i18n/{locale}` serves the merged dictionary; the locale resolves from a cookie, then `Accept-Language`, then the site default, and the brand-bar switcher persists the choice. Arabic renders fully right-to-left (`dir=rtl` + the portal's `khub-rtl.css`). Database content (publication titles, descriptions) is stored in its authored language, as on the Laravel portal.

### Metrics
- `GET /api/admin/metrics` (admin): runtime (uptime, memory, load), database status and latency, host disk, uploads per area, backups. Cached 5 minutes; `?fresh=1` bypasses.
- `GET /metrics`: Prometheus text format (HTTP latency histogram, database up gauge, Node process metrics). Set `METRICS_TOKEN` to require a bearer token.

## Moving into Docker

`scripts/move-to-docker.sh` moves a host or VM database (MySQL or PostgreSQL) and its uploads into the Docker stack (`docker-compose.yml`), adopts the schema, and verifies row counts. It works on a Laravel database as-is: the Node migrations skip existing tables and only add what is missing. See [docs/MOVE_TO_DOCKER.md](docs/MOVE_TO_DOCKER.md).

## Checking an install

```bash
npm run smoke -- --base=http://localhost:4000 --email=<admin> --password=<password>   # 25 HTTP checks, any host/VM/container
npm run verify:counts                                                                  # source vs target row counts (SOURCE_DB_* env)
npm run seed:demo -- --email=<admin> --password=<10+ chars>                            # demo data for an empty database only
```

## Migrating from the Laravel app

1. Stop writes to the Laravel site (or put it in maintenance) so the copy is consistent.
2. Create the new database and run `npm run migrate`.
3. Point the `SOURCE_DB_*` variables at the Laravel database (MySQL or PostgreSQL) and run:
   ```bash
   npm run migrate:from-laravel -- --dry-run      # counts rows per table
   npm run migrate:from-laravel                   # copies rows in foreign-key order
   ```
   The script is idempotent per table: it skips a table that already has rows unless you pass `--force`. It converts MySQL `0/1` flags to booleans and resets PostgreSQL sequences.
4. Upload files: the script reports where the Laravel uploads are and what to copy. The files are not copied automatically because they can be tens of gigabytes; use `rsync -a` onto `{HUB_DATA_ROOT}/{storageId}/files/`.
5. Start the Node API and frontend; check `/admin/metrics` and a few resource pages before switching DNS.

Tables copied: users, roles/permissions, settings, site languages, static links, storage settings, taxonomy (themes, tags, categories, file types, countries, authors, licences), publications and their relations, forums and comments, communities, events, FAQs, quotes, search logs, subscribers. Tables not yet ported: KPI/OWID data, federation, courses, Khub AI chat, RSS, approvals log, content requests, quiz, maps.

## Scripts

| Command | What it does |
|---|---|
| `npm run dev` | API and web in watch mode |
| `npm run build` | Production build of the frontend |
| `npm test` | Unit tests for shared, backend core, and modules |
| `npm run migrate [-- --site=id]` | Run migrations for a site |
| `npm run migrate:rollback [-- --site=id]` | Roll back the last batch |
| `npm run migrate:from-laravel [-- --site=id --dry-run --tables=a,b --force]` | Copy data from a Laravel database |

## Status

This is a foundation, not full parity with the Laravel app. The Laravel application has around 100 models, 40+ controllers and 200+ views; the modules above cover the public browsing path, and the admin area currently has module toggles and metrics only. Ported admin features: dashboard stats, approvals inbox, configure (branding/SEO/sections), themes, AI integration, module toggles, server metrics. Still Laravel-only: full CRUD for taxonomy/users/permissions, KPI dashboards, maps, storage-management UI, federation administration, language management, RSS, quizzes.
