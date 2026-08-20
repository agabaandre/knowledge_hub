# Federated country hub auto-provision (bare metal)

**Date:** 2026-08-20  
**Status:** Approved for implementation planning  
**Surface:** Continental Admin → Federated Knowledge Hubs (`/admin/federated-hubs`)

## Problem

Federation today only **registers** existing country hubs. Country instances on `khub.africacdc.org` are created manually: copy files under `/var/www/{country}`, add Apache `Alias /{country}`, create a MySQL database, install, then register the peer.

Ops need a continental admin action that provisions a country Knowledge Hub automatically on the **same bare-metal Apache host**, with an isolated database and a new admin account (no user migration from continental).

## Goals

- Provision a country hub at `https://khub.africacdc.org/{slug}` (path alias, not subdomain).
- Match existing ops pattern (`/kenya`, `/ghana`, `/namibia`, `/tunisia` → `/var/www/{slug}/public`).
- Dedicated MySQL database per country; no shared application data with continental.
- Create a verified country admin account (users are **not** copied).
- Copy all branding + lookup metadata from continental.
- Defaults: `STATES_ENABLED=false`, `ADMIN_UNITS_ENABLED=true`, `default_owner_country_id` / `HUB_OWNER_COUNTRY_ID` = selected country ID.
- Privileged credentials stored only in continental `.env` (never entered in the UI).

## Non-goals

- Docker / Compose multi-tenant orchestration.
- Subdomain provisioning (`uganda.khub.africacdc.org`).
- Migrating users, publications, forums, or private content from continental.
- Provisioning onto a remote host over SSH.
- Editing DNS or TLS certificates (reuse existing `khub.africacdc.org` vhost).

## Approach (chosen)

**Full instance copy** on the same server:

1. Copy continental DocumentRoot → `/var/www/{slug}/`.
2. Create MySQL database + user.
3. Write country `.env` and Apache `Alias` + `<Directory>`.
4. Run install/migrate; create admin; import meta; register hub on continental.

Rejected alternatives: shared codebase via symlink (coupled deploys); DB-only provision with manual Apache (not automatic).

---

## User flow & UI

Visible only when the current hub is **continental** (`admin_units_enabled` / country-hub mode is off).

New tab on `/admin/federated-hubs`: **Provision country hub**.

### Form fields

| Field | Required | Notes |
|-------|----------|--------|
| Country | Yes | From existing `countries` table |
| URL slug | Yes | Default from country name/ISO (e.g. `uganda`); live URL `https://khub.africacdc.org/{slug}` |
| Site name | No | Default `{Country} Knowledge Hub` |
| Admin first name | Yes | Country hub admin only |
| Admin last name | Yes | |
| Admin email | Yes | |
| Admin password | Yes | Min length consistent with install (8+) |

### Fixed defaults (read-only in UI)

- `STATES_ENABLED=false`
- `ADMIN_UNITS_ENABLED=true` (administrative units / country hub mode)
- Default owner country = selected country’s ID ([System Configurations → Advanced](https://khub.africacdc.org/admin/configure#advanced))
- Copy **all** branding + lookup metadata (see Metadata)

### After submit

1. Enqueue a provision job.
2. Show step status on the Provision tab: queued → copying → database → Apache → install → metadata → registered → complete (or failed).
3. On success: `FederatedKnowledgeHub` row under Remote hubs; link to `/{slug}`; connect/manifest ready.

---

## Provisioning pipeline

Background job steps (persist status after each):

1. **Validate** — slug allowed and free (no existing target dir, no existing Alias, not reserved); country exists; `FEDERATION_PROVISION_ENABLED` and required env credentials present.
2. **Copy files** — `rsync` from `FEDERATION_PROVISION_SOURCE_ROOT` (e.g. `/var/www/khub.africacdc.org`) → `{TARGET_PARENT}/{slug}/`. Exclude `.env`, `storage/logs/*`, `storage/framework/cache/*`, and other local secrets. Keep `vendor` for speed. Ensure ownership suitable for Apache (`www-data`).
3. **Create database** — via MySQL admin credentials: create `khub_{slug}` (normalized), dedicated DB user + generated password, grant all on that DB only.
4. **Write country `.env`** — from continental template / generated values:
   - `APP_URL=https://khub.africacdc.org/{slug}`
   - `ASSET_URL` aligned for subdirectory assets
   - `STATES_ENABLED=false`
   - `ADMIN_UNITS_ENABLED=true`
   - `HUB_OWNER_COUNTRY_ID={country_id}`
   - `HUB_SITE_ID={slug}`
   - New `APP_KEY`
   - New `DB_*` for the country database/user
   - `CENTRAL_HUB_URL` + continental registration/federation token so the child can connect
5. **Apache** — append to the SSL vhost file (`FEDERATION_PROVISION_APACHE_VHOST`):

   ```apache
   Alias /{slug} /var/www/{slug}/public
   <Directory /var/www/{slug}/public>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

   Then `apache2ctl configtest` and graceful reload via sudo. Abort reload if configtest fails; roll back Alias block.
6. **Install app** — in the new tree (PHP CLI / artisan):
   - Migrate + baseline seed (roles/permissions as existing install)
   - Create **verified** admin (`email_verified_at`, `is_verified`, approved, active)
   - Persist settings: `admin_units_enabled=true`, `default_owner_country_id={country_id}`
   - Mark installed / finalize as existing `InstallerService` / `khub:install` patterns
7. **Copy metadata** — branding settings + images keys and all lookup tables into the new DB (see below). No users/content.
8. **Register on continental** — create `FederatedKnowledgeHub` with `base_url=https://khub.africacdc.org/{slug}`, `mapped_country_id`, `name`; run connection/manifest test.

### Metadata copied (always)

Aligned with `config/federation.php`:

- **Branding:** `branding_setting_keys` + `branding_image_keys`. Image fields are stored as absolute continental URLs (same as existing federation branding import); no binary asset download in v1.
- **Lookup tables:** thematic areas, sub-thematic areas, tags, resource types, publication categories, licenses, static links.

Import is additive by natural key (name/text), consistent with `FederatedHubLookupService` where practical. Prefer a **direct DB-to-DB** import during provision (same host) over HTTP round-trip for reliability.

### Not copied

Users, publications, forums, OAuth clients, mail/SSO secrets, installer lock of continental, other hubs’ data.

---

## Configuration (continental `.env` only)

```env
FEDERATION_PROVISION_ENABLED=true
FEDERATION_PROVISION_SOURCE_ROOT=/var/www/khub.africacdc.org
FEDERATION_PROVISION_TARGET_PARENT=/var/www
FEDERATION_PROVISION_APACHE_VHOST=/etc/apache2/sites-available/khub.africacdc.org-le-ssl.conf
FEDERATION_PROVISION_PUBLIC_BASE_URL=https://khub.africacdc.org
FEDERATION_PROVISION_SUDO_PASSWORD=...
FEDERATION_PROVISION_MYSQL_HOST=127.0.0.1
FEDERATION_PROVISION_MYSQL_PORT=3306
FEDERATION_PROVISION_MYSQL_ADMIN_USER=root
FEDERATION_PROVISION_MYSQL_ADMIN_PASSWORD=...
```

Document these in `.env.example` / deployment docs **without** real secrets. UI never displays or accepts these values.

Optional later hardening (out of initial scope): replace sudo password with a root-owned wrapper + sudoers rule for www-data.

---

## Architecture components

| Unit | Responsibility |
|------|----------------|
| `FederatedHubProvisionController` (or methods on `FederatedHubsController`) | Authz, validate form, enqueue job, poll/status JSON |
| `FederatedHubProvisionService` | Orchestrate steps; update job status; invoke helpers |
| `ProvisionFilesystem` | rsync/copy + ownership under sudo |
| `ProvisionDatabase` | Create DB/user via admin connection; return credentials |
| `ProvisionApache` | Idempotent Alias insert; configtest; reload |
| `ProvisionAppInstaller` | Run artisan/install in target tree; admin user; hub settings |
| `ProvisionMetadataCopy` | Copy branding + lookup tables continental → country DB |
| `ProvisionJob` + `federated_hub_provisions` (or cache/DB status model) | Async progress, failure message, cleanup flags |
| Blade: provision tab on `admin/federation/index` | Form + status UI |

Reuse where possible: `InstallerService::createAdminUser` (verified), `FederatedHubLookupService` export/import shapes, `FederatedHubService::testConnection`, existing `FederatedKnowledgeHub` model.

---

## Failures, security & edge cases

### Failures / rollback

- On step failure: status `failed` with operator-facing message (no secret leakage).
- Best-effort cleanup for **this attempt only**: drop new DB/user if created; remove `/var/www/{slug}` if copied; remove Apache Alias block if added. Never modify continental app files or other country trees.
- Retry allowed after fix; same slug OK once cleanup succeeded.

### Security

- Provision UI/routes only when continental mode; Admin permission.
- Credentials only in continental env; redact in logs.
- Sudo limited to filesystem/Apache operations; MySQL admin limited to create DB/user/grants.
- Country admin password hashed only in country DB.

### Edge cases

- **Reserved slugs:** existing Aliases and paths such as `admin`, `api`, `storage`, `login`, `install`, `federated`, plus any already under `FEDERATION_PROVISION_TARGET_PARENT`.
- **Subdirectory URLs:** country `APP_URL` / `ASSET_URL` include `/{slug}`; rely on same `public/.htaccess` pattern as existing country aliases.
- **Idempotency:** block provision if target dir or hub registration already exists for a successful hub; allow only after failed+cleaned attempt.
- Queue worker must run on continental with permission to reach MySQL admin and invoke sudo as configured.

---

## Testing

- Unit: slug validation/reservation; Apache snippet insert/remove idempotency; env generation (no accidental continental secrets copy); metadata table list matches federation config.
- Feature (continental admin): form validation; job enqueue; unauthorized country-hub cannot open provision.
- Integration (staging bare metal or VM with Apache+MySQL): full provision of a throwaway slug, HTTP 200 on `/{slug}`, admin login, settings show admin units + owner country, continental remote hub row present.
- Failure injection: bad MySQL admin password → failed status + no orphan Alias; configtest failure → Alias rolled back.

---

## Success criteria

1. Continental admin can provision Uganda (example) and open `https://khub.africacdc.org/uganda`.
2. Country hub has its own DB; continental users do not exist there; new admin can sign in (verified).
3. Branding + taxonomy present; admin units on; default owner country set to Uganda’s ID.
4. Hub appears under Federated Knowledge Hubs remote list and can connect/sync.
5. Failed provision does not leave a broken Alias pointing at a missing tree (cleanup best-effort documented if partial).

## Implementation notes

- Prefer a queued job; sync HTTP will time out on large rsync.
- Confirm exact Let’s Encrypt vhost filename on production before documenting defaults.
- Extend `khub:install` or call `InstallerService` via `php artisan` in the **target** tree so migrations run against the country DB, not continental.
