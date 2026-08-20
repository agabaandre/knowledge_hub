# Federated Knowledge Hubs

The Knowledge Hub supports a **continental ↔ country** federation model for sharing public content, branding, and lookup metadata between instances.

---

## Roles

| Hub type | `admin_units_enabled` | Role |
|----------|----------------------|------|
| **Continental** (central) | `false` | Provider + consumer: exposes federation API; registers country hubs to browse/sync public publications & forums |
| **Country / regional** | `true` | Provider + consumer: exposes federation API; can **pull branding & metadata** from the central hub |

Configure hub type under **Admin → System Configurations → Hub deployment**.

---

## Federation API (provider)

Base path: `{APP_URL}/api/federation`

| Endpoint | Auth | Description |
|----------|------|-------------|
| `GET /lookup` | Open | Index of all federation URLs |
| `GET /manifest` | Token if set | Hub identity, type, owner country/region |
| `GET /lookup/settings` | Token if set | Branding & mobile settings (colors, theme, logos, feature flags) |
| `GET /lookup/metadata` | Token if set | Lookup tables: health themes, tags, resource types, publication categories, licenses, static links |
| `GET /public/publications` | Token if set | Approved public publications (paginated) |
| `GET /public/forums` | Token if set | Approved public forums (paginated) |
| `POST /auth/token` | Open | Issue OAuth access/refresh tokens for child hubs |

When `federation_api_token` is set (System Configurations or `FEDERATION_API_TOKEN` in `.env`), remote callers must send `Authorization: Bearer {token}` or `?token=`.

### OAuth tokens (child ↔ central)

Country hubs exchange the parent hub's **registration token** once via `POST /api/federation/auth/token`:

```json
{ "grant_type": "registration", "site_id": "kenya-khub", "registration_token": "…" }
```

The response includes `access_token`, `refresh_token`, and `expires_in`. Child hubs store these in the `setting` table and **refresh automatically** before expiry (or on HTTP 401) using:

```json
{ "grant_type": "refresh_token", "refresh_token": "…" }
```

Scheduled: `php artisan federation:refresh-central-token` (hourly on country hubs).

### Mobile branding

Child hubs import branding via `GET /api/federation/lookup/settings`. This mirrors the fields served locally by `GET /api/lookup/settings` (used by the mobile app for theme switching and basic functionality).

Imported fields include colors, `site_theme`, navigation/footer styles, section titles, feature toggles, and resolved logo/favicon/banner URLs. Per-theme overrides in `theme_settings` are included when present.

### Lookup metadata

`GET /api/federation/lookup/metadata` exports read-only taxonomy rows for publications and forums:

- `thematic_areas`, `sub_thematic_areas`
- `tags`
- `resource_types` (data categories)
- `publication_categories`
- `licenses`
- `static_links`

Import is **additive**: existing rows matched by name/text are skipped.

### Central approval of partner content

When a continental hub syncs public data from country hubs (`federation:sync` or **Sync public data** in admin), items are staged in `federated_content_items`. They **do not appear** in search or `/federated` until a central administrator approves them under **Settings → Federated Knowledge Hubs → Review pending content** (`/admin/federated-content/pending`).

Country hubs must still approve content locally and mark it `public_availability = 1` before it can be synced.

---

## Admin UI

**Settings → Federated Knowledge Hubs** (`/admin/federated-hubs`)

Tabs:

1. **Provider API** — endpoint reference and local manifest preview
2. **Central hub connection** (country hubs only) — test connection and sync branding/metadata from continental hub
3. **Remote hubs** — register peer instances; connect and sync public publications/forums
4. **Provision country hub** (continental only, when `FEDERATION_PROVISION_ENABLED=true`) — auto-create a bare-metal path-alias country instance (`https://khub.africacdc.org/{slug}`), dedicated MySQL DB, Apache Alias, verified admin, branding/metadata copy, and remote hub registration

See `docs/superpowers/specs/2026-08-20-federated-hub-provision-design.md` for the full provision design.

During provision, each country hub gets a dedicated MySQL database and a **site-scoped host data tree** under `/var/khubdata/{site-id}/` (derived from `APP_URL`, e.g. `https://khub.africacdc.org/ghana` → `khub-africacdc-org-ghana`), with `HUB_SITE_ID`, `HUB_FILES_ROOT`, and `HUB_SQL_BACKUP_ROOT` written into the country `.env`. Application runtime dirs (`storage`, `bootstrap/cache`, `public/uploads`) are also created and owned by the web user.

---

## Installation (country hubs)

**Step 5 of 7 — Connect to central Knowledge Hub** (`/install/central-hub`)

During installation, country instances can:

1. Enter the central hub URL (e.g. `https://khub.africacdc.org`)
2. Optionally provide an API token
3. Import branding and/or lookup metadata before completing setup

Skip this step to configure manually later in admin.

Environment variables written when connected:

```env
CENTRAL_HUB_URL=https://khub.africacdc.org
CENTRAL_HUB_API_TOKEN=
```

---

## Scheduled sync (continental)

```bash
php artisan federation:sync          # hubs with auto_sync enabled
php artisan federation:sync --all    # all active hubs
php artisan federation:sync --hub=1  # single hub by ID
```

Runs daily at **02:45** when the hub is a continental consumer (`federation_consumer_enabled()`).

## Scheduled token refresh (country)

```bash
php artisan federation:refresh-central-token
```

Runs **hourly** on country hubs to refresh the parent connection before the access token expires.

---

## Database

**`federated_knowledge_hubs`** — registered remote hubs (continental consumer)

**`setting` columns (country ↔ central):**

- `central_hub_url`
- `central_hub_api_token` (current OAuth access token)
- `central_hub_refresh_token`
- `central_hub_token_expires_at`
- `central_hub_site_id`
- `central_hub_connected_at`
- `central_metadata_synced_at`

---

## Key files

| Path | Purpose |
|------|---------|
| `app/Services/FederatedHubService.php` | Manifest, remote connect, publication/forum sync |
| `app/Services/FederatedHubLookupService.php` | Branding/metadata export & import |
| `app/Http/Controllers/Api/FederatedHubApiController.php` | Federation API |
| `app/Http/Controllers/Admin/FederatedHubsController.php` | Admin UI |
| `config/federation.php` | Branding keys and metadata table list |
