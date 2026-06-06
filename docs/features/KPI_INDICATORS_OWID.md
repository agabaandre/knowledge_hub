# KPI indicators & Our World in Data (OWID)

This document describes how the Knowledge Hub loads, publishes, and displays **country indicators** for African **member states**, sourced from [Our World in Data](https://ourworldindata.org/) (CC BY 4.0). It is written for **public users**, **administrators**, and **developers**.

---

## Overview

- Indicators are grouped by **subject area** (Health, Economy, etc.).
- Country values are matched using **ISO 3166-1 alpha-3** codes (`country.iso3_code`) for member states (`region_id > 0`).
- Data is discovered via the [OWID Search API](https://docs.owid.io/projects/etl/api/search-api/), downloaded from OWID **grapher CSV** endpoints, and stored locally.
- Admins control **draft → published → recalled** workflow before indicators appear on public country pages.
- Optional **AI narrations** (OpenAI) provide short plain-language summaries per indicator and country.
- **Automatic weekly refresh** and **manual-only mode** are configurable under KPI settings.

---

## For public users

### Where to view indicators

Open any member state from **Countries** (for example `/countries/details/chad` when SEO-friendly URLs are enabled, or `/countries?state={id}` in legacy mode).

The **Country indicators** section shows:

- Indicators grouped by **subject area**
- Latest **value**, **unit**, and **year**
- Optional **AI narration** (when generated and cached)
- A link to **view chart details** on [ourworldindata.org](https://ourworldindata.org/)
- Attribution: data from Our World in Data (CC BY 4.0)

Only indicators with status **`published`** are shown.

### RCC / performance dashboards

Users with access to RCC dashboards see comparative charts (line/bar) by region, subject area, and KPI. Charts include OWID source credits linking to [ourworldindata.org](https://ourworldindata.org/).

### What users do not see

- Draft or recalled indicators
- OWID import controls (admin only)
- Raw CSV or API responses

---

## For administrators

Requires permission **`manage_kpis`** (under **KPIs** in the admin menu when member states / performance features are enabled).

### Admin navigation

| Menu item | URL | Purpose |
|-----------|-----|---------|
| Manage indicators | `/admin/kpi` | List, filter, publish, recall, edit indicators |
| Subject areas | `/admin/kpi/subject-areas` | OWID topic/search mappings for discovery |
| Review duplicates | `/admin/kpi/duplicates` | Inspect and merge duplicate indicators or subject areas |
| Country values | `/admin/kpi/data` | View or manually add/edit per-country values |

### Indicator data management panel

At the top of all three KPI admin pages, the **Indicator data management** panel provides:

- Live counts (published, draft, country values, AI summaries)
- **KPI data source settings** (see below)
- Background actions with a **progress bar**
- Quick links between indicators, subject areas, and country values

### KPI data source settings

Saved to the active **`setting`** row (not `.env`):

| Setting | Effect |
|---------|--------|
| **Enable automatic weekly fetch from Our World in Data** | When on, a scheduled job runs every **Sunday at 04:30** to discover new charts and refresh published country values. When off, no automatic OWID jobs run. |
| **Manual data entry only** | When on, OWID import buttons are hidden. Admins add indicators and country values manually. **Publish** only changes status (no OWID CSV download). |

Click **Save KPI settings** after changing checkboxes.

### Background tasks and progress bar

All OWID import actions run in the **queue** (not in the browser request). When you start a task:

1. A job is queued and a **progress bar** appears.
2. The page polls every 2 seconds for status.
3. On completion, the bar shows success or failure with a message.

**Server requirement:** a queue worker must be running in production:

```bash
php artisan queue:work
```

On Docker, the `queue` service should be enabled. With `QUEUE_CONNECTION=sync` (common locally), jobs run immediately and the bar may jump to 100% without intermediate steps.

### Admin actions

| Action | What it does |
|--------|----------------|
| **Run fetch** | Searches OWID by subject area; new charts saved as **draft** |
| **Refresh values** | Downloads latest CSV data for all **published** indicators |
| **Choose & publish (22)** | Opens a modal listing curated default indicators; uncheck any to skip, then publish selected and sync values; optional AI summaries |
| **Run full refresh** | Fetch new indicators, then refresh all published values |
| **Regenerate summaries** | Queues AI narration jobs for every published indicator |
| **Publish** (per row or bulk) | Publishes selected indicators on member state pages (queued for OWID sync) |
| **Recall** (per row or bulk) | Hides indicators from public pages without deleting (`recalled` status) |
| **Delete** (bulk) | Permanently removes selected indicators and their country values |
| **Refresh** (sync icon) | Refreshes one OWID indicator’s country values (queued) |
| **Country values** (table icon) | Opens `/admin/kpi/data?kpi_id=…` |
| **Review duplicates** | Opens `/admin/kpi/duplicates` to inspect duplicate groups |
| **Auto-merge indicators / subject areas** | Queued merge; keeps the best record and moves linked data |

Subject areas also have a **fetch** button per row (discover for that topic only).

### Duplicate prevention and cleanup

Duplicates can appear when the same OWID chart is discovered under multiple subject areas, when names differ slightly, or when subject areas are created twice.

**Automatic (on fetch):**

- During **Run fetch** and **Run full refresh**, new charts are skipped if the OWID slug, URL, or normalized name already exists.
- After fetch completes, duplicate indicators are **auto-merged** (country values and narrations move to the keeper record).

**Manual review:**

- Open **Review duplicates** from the data management panel or go to `/admin/kpi/duplicates`.
- Each group shows a suggested **keeper** (prefers published indicators with more country data).
- Use **Merge group** per group, or **Auto-merge all** for indicators or subject areas.

**Detection rules:**

| Entity | Treated as duplicate when |
|--------|---------------------------|
| Indicator | Same OWID chart slug, same OWID URL, same normalized name, or same name + subject area |
| Subject area | Same slug, same normalized name, or same OWID topic + search query |

**Database safeguards:** unique `owid_chart_slug` on indicators, unique `slug` on subject areas, and unique `(kpi_id, country_id, period)` on country values (migration `2026_05_22_120000_add_kpi_deduplication_indexes`).

### Recommended workflow

1. Review **Subject areas** (OWID topic + search query).
2. **Run fetch** to import draft indicators.
3. Review drafts on **Manage indicators**; **Publish** the ones you want live (or use **Publish recommended**).
4. Use **Refresh values** monthly or rely on **automatic weekly fetch** if enabled.
5. Optionally **Regenerate summaries** after publishing (requires OpenAI key and queue worker).

### Manual data mode workflow

When **Manual data entry only** is enabled:

1. **Add indicator** (custom KPI definition).
2. Open **Country values** → **Add indicator data** (country, period, value).
3. **Publish** the indicator when ready (no OWID sync).

### Filters on Manage indicators

- Search by name or description
- **Status:** published / draft / recalled
- **Source:** Our World in Data / manual

### Attribution

Every indicator card and chart includes links to [Our World in Data](https://ourworldindata.org/) and the [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/) license, plus a **View chart details** link when a grapher URL is known.

---

## For developers

### Architecture

```
OWID Search API  →  discoverIndicators()  →  kpi (draft)
OWID Grapher CSV →  syncIndicatorData()   →  data (values by iso3)
Admin approve    →  status=published      →  kpi_data_view
Country page     →  GraphsRepository      →  grouped by subject_area
AI (optional)    →  GenerateKpiNarrationsJob → kpi_narrations
Manual actions   →  ProcessKpiOwidActionJob  →  kpi_sync_runs (progress)
```

### Key files

| Area | Path |
|------|------|
| OWID config | `config/owid.php` |
| API client | `app/Services/Owid/OwidApiClient.php` |
| Sync / discover / approve | `app/Services/Owid/OwidIndicatorSyncService.php` |
| Background task job | `app/Jobs/ProcessKpiOwidActionJob.php` |
| AI narrations job | `app/Jobs/GenerateKpiNarrationsJob.php` |
| Narration service | `app/Services/KpiNarrationService.php` |
| Task progress | `app/Services/Kpi/KpiSyncRunService.php`, `app/Models/KpiSyncRun.php` |
| Admin controller | `app/Http/Controllers/KpiController.php` |
| Subject areas | `app/Http/Controllers/KpiSubjectAreaController.php` |
| Public country KPIs | `app/Repositories/GraphsRepository.php` |
| Country page | `app/Http/Controllers/CountriesController.php`, `resources/views/countries/details.blade.php` |
| Admin UI | `resources/views/admin/kpi/` |
| Attribution helpers | `app/Helpers/OwidAttributionHelper.php` |
| Artisan command | `app/Console/Commands/SyncOwidKpiCommand.php` |
| Scheduled refresh | `app/Console/Kernel.php` |

### Database

**Tables**

| Table | Role |
|-------|------|
| `kpi` | Indicator definition; OWID fields (`owid_chart_slug`, `owid_url`, `source`, `status`, …) |
| `data` | Country values (`kpi_id`, `country_id`, `value`, `period`) |
| `subject_areas` | Grouping; `owid_topic`, `owid_search_query`, `sort_order`, `is_active` |
| `kpi_narrations` | Cached AI text per `kpi_id` + `country_id` + `period` |
| `kpi_sync_runs` | Background task status and progress |
| `kpi_data_view` | SQL view for dashboards and country pages |

**`kpi.status`:** `draft` | `published` | `recalled`

**Setting columns** (on `setting`):

- `kpi_owid_auto_fetch_enabled` (bool, default true)
- `kpi_manual_data_only` (bool, default false)

Migration: `2026_05_21_100000_add_kpi_settings_and_sync_runs.php` (and earlier OWID KPI migrations).

### ISO country matching

`OwidIndicatorSyncService::memberStatesByIso3()` loads countries with `region_id > 0` and non-empty `iso3_code`. OWID CSV rows are filtered by the **Code** column (ISO3). Ensure member states have correct `iso3_code` values (e.g. Chad = `TCD`).

### OWID configuration (`config/owid.php`)

| Key | Purpose |
|-----|---------|
| `base_url`, `search_path` | Search API base (`https://ourworldindata.org/api/search`) |
| `charts_per_topic` | Max charts discovered per subject area per run |
| `default_subject_areas` | Seeded subject areas with OWID topic + fallback search query |
| `default_published_chart_slugs` | Curated slugs for **Publish recommended** |

Environment overrides:

```env
OWID_BASE_URL=https://ourworldindata.org
OWID_HTTP_TIMEOUT=30
OWID_CHARTS_PER_TOPIC=12
```

### Helper functions (`OwidAttributionHelper.php`)

| Function | Purpose |
|----------|---------|
| `owid_site_url()` | `https://ourworldindata.org` |
| `owid_chart_url($kpi)` | Grapher URL from `owid_url` or slug |
| `kpi_owid_auto_fetch_enabled()` | Reads `setting.kpi_owid_auto_fetch_enabled` |
| `kpi_manual_data_only()` | Reads `setting.kpi_manual_data_only` |
| `kpi_admin_stats()` | Counts for admin dashboard panel |

Blade partials: `resources/views/common/owid_attribution.blade.php`, `owid_chart_credits.blade.php`.

### Admin HTTP routes

Prefix: `/admin/kpi` (middleware `permission:manage_kpis` unless noted).

| Method | Path | Action |
|--------|------|--------|
| GET | `/` | Indicator list |
| GET | `/data` | Country values |
| GET | `/subject-areas` | Subject areas |
| POST | `/settings/save` | Save KPI toggles |
| POST | `/owid/discover` | Queue discover job |
| POST | `/owid/sync` | Queue sync job (`published_only` optional) |
| POST | `/owid/sync-one` | Queue sync for one KPI |
| POST | `/owid/fresh-fetch` | Queue discover + sync published |
| POST | `/owid/approve-defaults` | Queue publish recommended set |
| POST | `/owid/generate-narrations` | Queue narration jobs |
| GET | `/owid/task/{id}` | JSON progress for polling |
| POST | `/approve`, `/recall` | Publish / recall single indicator |

Queued POST endpoints return JSON `{ success, run_id, message }` when called with `Accept: application/json` or `X-Requested-With: XMLHttpRequest`.

### Background jobs

**`ProcessKpiOwidActionJob`** — actions: `discover`, `sync`, `fresh_fetch`, `approve_defaults`, `generate_narrations`, `sync_one`, `approve`.

Updates `kpi_sync_runs.progress` (0–100), `step`, `message`, `status` (`queued` → `running` → `completed` | `failed`).

**`GenerateKpiNarrationsJob`** — one KPI; loops countries with data; uses `KpiNarrationService` + `config('ai.open_api_key')`.

### Artisan commands

```bash
# Discover only
php artisan kpi:sync-owid --discover

# Sync published indicators only
php artisan kpi:sync-owid --sync --published-only

# Discover + sync (same as scheduled job when auto-fetch enabled)
php artisan kpi:sync-owid --discover --sync --published-only

# Publish curated default set (CLI)
php artisan kpi:approve-owid-defaults
php artisan kpi:approve-owid-defaults --narrations
```

### Scheduler

In `app/Console/Kernel.php`, when **both** `kpi_owid_auto_fetch_enabled()` is true and `kpi_manual_data_only()` is false:

```php
$schedule->command('kpi:sync-owid --discover --sync --published-only')->weeklyOn(0, '04:30');
```

Ensure `cron` runs `php artisan schedule:run` every minute on the server.

### Queue worker (production)

```bash
php artisan queue:work --sleep=3 --tries=3
```

Docker Compose: enable the `queue` service. Without a worker, tasks stay in `queued` and the admin progress bar does not advance.

### Discover resilience

`OwidApiClient::searchChartsForSubject()` tries topic filter first, then query-only fallback, because some OWID topic names return HTTP 400. Discovery skips non-`chart` OWID result types (explorers without CSV).

### Extending the curated set

Edit `default_published_chart_slugs` in `config/owid.php`, then run **Publish recommended** in admin or:

```bash
php artisan kpi:approve-owid-defaults
```

### AI narrations

- Triggered on single **Publish** (queued) or **Publish recommended** when checkbox is on.
- Stored in `kpi_narrations`; displayed on country pages by `CountriesController`.
- Requires valid OpenAI configuration; invalid responses are not saved.
- **Regenerate summaries** re-queues jobs for all published KPIs.

### Permissions

- **`manage_kpis`** — full KPI admin (import, settings, data)
- **`view_performance`** / **`view_rcc_dashboard`** — RCC dashboard charts (related read paths)

### Related migrations (reference)

- `2026_05_20_140000_rebuild_kpi_owid_integration.php` — OWID columns, `kpi_narrations`, view refresh
- `2026_05_20_140001_add_owid_search_query_to_subject_areas.php`
- `2026_05_21_100000_add_kpi_settings_and_sync_runs.php` — settings toggles + task tracking

---

## Troubleshooting

| Issue | Check |
|-------|--------|
| Progress bar stuck at 0% | Queue worker running? `QUEUE_CONNECTION` not blocking jobs? |
| No indicators on country page | KPI **published**? Country has values in `data`? `iso3_code` set? |
| Discover returns 0 | Subject area `owid_search_query` / `owid_topic`; OWID API availability |
| Sync errors for some charts | OWID slug may be explorer-only (404/403 on CSV); remove or recall |
| No AI narrations | OpenAI key in admin AI settings; queue worker; check `kpi_narrations` |
| Automatic refresh not running | `kpi_owid_auto_fetch_enabled` on? Manual-only off? Cron + scheduler? |

---

## Further reading

- [OWID Search API documentation](https://docs.owid.io/projects/etl/api/search-api/)
- [Our World in Data — CC BY 4.0](https://creativecommons.org/licenses/by/4.0/)
- [../deployment/DOCKER.md](../deployment/DOCKER.md) — queue workers in Docker
