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

When `federation_api_token` is set (System Configurations or `FEDERATION_API_TOKEN` in `.env`), remote callers must send `Authorization: Bearer {token}` or `?token=`.

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

---

## Admin UI

**Settings → Federated Knowledge Hubs** (`/admin/federated-hubs`)

Tabs:

1. **Provider API** — endpoint reference and local manifest preview
2. **Central hub connection** (country hubs only) — test connection and sync branding/metadata from continental hub
3. **Remote hubs** — register peer instances; connect and sync public publications/forums

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

---

## Database

**`federated_knowledge_hubs`** — registered remote hubs (continental consumer)

**`setting` columns (country ↔ central):**

- `central_hub_url`
- `central_hub_api_token`
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
