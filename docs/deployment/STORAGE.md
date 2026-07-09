# Storage management

The Knowledge Hub separates **application code** from **persistent data**. Publication PDFs, cover images, forum attachments, and SQL backups should survive container rebuilds and deployments.

**Admin UI:** Settings → **Storage Management** (`/admin/storage-management`)

Deep links use URL hashes, for example:

| Tab | URL |
|-----|-----|
| Overview | `/admin/storage-management#storage-overview` |
| Configuration | `/admin/storage-management#storage-config` |
| Migration | `/admin/storage-management#storage-migration` |
| SQL backups | `/admin/storage-management#storage-backups` |
| Browse files | `/admin/storage-management#storage-browse` |
| Server setup | `/admin/storage-management#storage-server` |

---

## Admin UI (tabs)

The Storage Management page is organised into six tabs. Most day-to-day work happens on **Overview**, **Configuration**, and **Browse files**. **Server setup** is aimed at DevOps.

### Overview

Live dashboard for storage health and server capacity:

| Metric | Description |
|--------|-------------|
| **Host disk** | Free/used space on `/var/khubdata/{site-id}` (or Windows equivalent) |
| **Upload files** | Total size of the active files root; shows legacy copy size when duplicates exist |
| **SQL backup storage** | Size of backup folder and time of last backup |
| **Memory** | System RAM on Linux (`/proc/meminfo`); falls back to PHP process memory elsewhere |
| **CPU load** | 1 / 5 / 15 minute load averages (Unix) |
| **Queue** | Pending and failed jobs, queue driver |
| **PHP & Laravel** | Versions, SAPI, upload limits, environment badge |
| **Database** | MySQL/MariaDB version, database size, table count, connection status |
| **Deployment** | Detected deployment type (Docker, bare metal, subdirectory, local dev, Windows) |
| **Application stack** | Hub file driver, cache/session/queue drivers, Scout/Meilisearch/Redis health |

**Live updates:** metrics refresh automatically every **30 seconds**. Migration progress polls every **5 seconds** while a job is running. Use **Refresh** to bypass the 5-minute server-side metrics cache (`?fresh=1`).

**Platform details** table below the cards collects the same information in one place for support tickets.

### Configuration

- **Files driver** — internal (host path) or cloud (S3, GCS, Azure Blob, SharePoint, SFTP)
- **Host files root** — where uploads live for the internal driver (default `/var/khubdata/{site-id}/files`)
- **Cloud credentials** — driver-specific fields with in-page setup notes and Composer package hints
- **Test connection** — read/write probe against the selected driver
- **SQL backup root** — separate from publication files
- **Retain backups (days)** — old SQL backup folders are pruned after this many days (default 30)
- **Automatic daily incremental SQL backup** — scheduled at 01:30 when enabled

Settings are stored in `hub_storage_settings`. Saving clears the in-memory settings cache so changes apply immediately (including under Octane/long-lived workers).

### Migration

Three-step workflow shown in order:

1. **Migrate to host files root** — copy uploads from legacy `storage/app/public` to `/var/khubdata/{site-id}/files` while keeping originals until verified. Queue or run synchronously.
2. **Remove legacy copies** — after host migration, delete verified duplicates from `storage/app/public` (size-matched against host path). Blocked if any file is missing or mismatched on the host.
3. **Migrate to cloud / external storage** — after switching the files driver, copy existing uploads to S3, Azure, GCS, SharePoint, or SFTP.

Progress bars update live while `migration_status` is `running`.

### SQL backups & restore

- Run **incremental** or **full** backup from the admin UI
- **Restore** from a backup folder with per-table checkboxes grouped by domain (publications, forums, users, etc.)
- **Only restore into empty tables** (recommended) or force into populated tables
- **In backup only** quick-select for tables present in the chosen snapshot
- KPI / OWID tables are excluded from backups (re-sync from API)

### Browse files (Laravel File Manager)

The **Browse files** tab embeds [alexusmai/laravel-file-manager](https://github.com/alexusmai/laravel-file-manager) v2.5.4 (Laravel 8–compatible). It provides a full file-manager UI on the hub `filesystems.disks.hub` disk:

- Directory tree, table/grid views, upload, rename, delete, download, preview
- Image preview; zip/unzip on **local** internal disks only
- **Content area** dropdown switches the root folder between configured prefixes (publications, forum attachments, summaries, etc.)

**Linked publications panel:** below the file manager, a table lists files in the **current folder** that are referenced in the database, with publication title, link type (main file, cover, attachment, summary, forum attachment), and an **edit** link to the admin publication or forum record.

Publication linking is built from:

| Source | Field |
|--------|-------|
| `publication` | `publication` (main PDF/file), `cover` |
| `publication_attachments` | `file` |
| `publication_summaries` | `file_path` |
| `custom_attachments` | `path` (forum attachments) |

The index is cached for five minutes. `.pd` / `.pdf` filename variants are normalised so legacy mis-saved extensions still match.

**Package assets:** `public/vendor/file-manager/` (CSS/JS). After `composer install`, publish with:

```bash
php artisan vendor:publish --tag=fm-config
php artisan vendor:publish --tag=fm-assets
```

Configuration: `config/file-manager.php` — disk `hub`, middleware `web` + `auth` + `hub.storage.disk`, default path `uploads/publications`.

File-manager API routes are prefixed `/file-manager/` (e.g. `/file-manager/content`). Access requires an authenticated admin session.

### Server setup

Developer reference: path layout, `.env` overrides, shell helpers, Artisan commands, and link to this document (`docs/deployment/STORAGE.md`).

---

## Architecture

```
/var/khubdata/                              ← host volume (mount in Docker)
└── {site-id}/                              ← unique per hub (from APP_URL)
    ├── files/                              ← uploads (auto-created)
    │   └── uploads/
    │       ├── publications/
    │       ├── publications/summaries/
    │       ├── forums/
    │       └── forum/
    └── backups/
        └── sql/                            ← SQL dumps (auto-created)

site-id examples:
  https://khub.com/andrew  →  khub-com-andrew
  https://hub.example.com  →  hub-example-com
  http://localhost:8080      →  localhost-8080

public/storage  →  symlink/junction  →  /var/khubdata/{site-id}/files   (internal driver)
/hub-media/{path}                 ← HTTP route when using custom host paths
/storage/{path}                   ← works via symlink above
```

Application code uses `hub_storage_path()` and `storage_link()` helpers — not hard-coded `storage/app/public` paths.

---

## Default paths

| OS | Files root | SQL backups |
|----|------------|-------------|
| Linux | `/var/khubdata/{site-id}/files` | `/var/khubdata/{site-id}/backups/sql` |
| Windows | `C:\khubdata\{site-id}\files` | `C:\khubdata\{site-id}\backups\sql` |

Directories are **created automatically** on boot and during install. The site ID is stored in `hub_storage_settings.site_storage_id` on first run.

Override in `.env`:

```env
APP_URL=https://khub.com/andrew
HUB_SITE_ID=khub-com-andrew          # optional explicit ID
HUB_FILES_ROOT=                      # optional full path override
HUB_SQL_BACKUP_ROOT=
```

Cloud drivers automatically prefix blobs with `{site-id}/` so shared buckets are not mixed between hubs.

---

## Internal driver (recommended default)

Stores files on the host filesystem. Best for single-server or Docker deployments with a mounted volume.

### Setup (Linux)

```bash
sudo mkdir -p /var/khubdata
./fix-storage-permissions.sh    # creates {site-id}/files, permissions, and public/storage link
```

Or manually (replace `{site-id}` with your hub ID, e.g. `localhost-knowledge-hub`):

```bash
sudo mkdir -p /var/khubdata/{site-id}/files /var/khubdata/{site-id}/backups/sql
sudo chown -R www-data:www-data /var/khubdata
php artisan hub:link-storage
```

### Setup (Windows)

Default paths use `C:\khubdata\{site-id}\files`. From the project root in **Command Prompt** or **PowerShell**:

```bat
link-hub-storage.bat
```

```powershell
.\link-hub-storage.ps1
```

Cross-platform (recommended):

```bash
php artisan hub:link-storage
```

If linking fails, enable **Developer Mode** in Windows Settings or run the terminal **as Administrator**, then retry.

### Docker

Mount a host directory into every app/queue container:

```yaml
volumes:
  - ./khubdata:/var/khubdata
```

Configure during install step 3 or in Storage Management.

### URL linkage (`public/storage`)

For **internal** storage, `public/storage` must point at your **hub files root** (e.g. `/var/khubdata/{site-id}/files`), not Laravel’s default `storage/app/public`.

| When | What happens |
|------|----------------|
| Install step 3 (storage) | Link created automatically |
| App boot | `HubStorageService::ensurePublicStorageSymlink()` corrects a stale link |
| Docker entrypoint | Runs `php artisan hub:link-storage` |
| Manual fix | See commands below |

**Do not rely on `php artisan storage:link` alone** when using `/var/khubdata` or `C:\khubdata` — it only targets `storage/app/public` and the prerequisites check will fail.

#### Commands

| Platform | Command |
|----------|---------|
| **All (Artisan)** | `php artisan hub:link-storage` |
| **macOS / Linux** | `./link-hub-storage.sh` |
| **Windows CMD** | `link-hub-storage.bat` |
| **Windows PowerShell** | `.\link-hub-storage.ps1` |
| **Permissions + link** | `./fix-storage-permissions.sh` (runs `hub:link-storage` at the end) |

`storage_link()` returns `/storage/...` when the symlink/junction is correct, or `/hub-media/...` for custom host paths.

### Legacy installs (`storage/app/public/uploads`)

Older hubs may still have all uploads under `storage/app/public/uploads/` while Storage Management points at `/var/khubdata/{site-id}/files`. Running `fix-storage-permissions.sh` or `hub:link-storage` alone can break URLs if the host path is empty.

From current app versions:

1. **Automatic fallback** — if the configured host path has no uploads but legacy storage does, the hub serves files from legacy storage until migration completes.
2. **Migrate to host path** — Admin → Storage Management → **Migrate to host files root**, or:

```bash
php artisan hub:migrate-storage-to-host
```

3. After migration, `public/storage` is relinked to `/var/khubdata/{site-id}/files`.

4. **Remove legacy copies (after verification)** — migration keeps originals under `storage/app/public` until you confirm the host path works. Then either:

   - Admin → Storage Management → **Remove legacy upload copies**, or
   - Preview: `php artisan hub:purge-legacy-storage --dry-run`
   - Purge: `php artisan hub:purge-legacy-storage`

   Only files that exist on the host path with the same size are deleted. If any legacy file is missing or differs on the host path, purge is blocked until you re-run migration or fix the mismatch.

**Production recovery (after deploying latest code):**

```bash
cd /var/www/khub.africacdc.org   # your app root

# 1. Diagnose + fix symlink (serves from legacy when host path is empty)
php artisan hub:recover-storage

# 2. If files also exist under storage/uploads/ (old tree), merge them:
php artisan hub:recover-storage --sync-legacy

# 3. When ready, copy everything to /var/khubdata/{site-id}/files:
php artisan hub:recover-storage --sync-legacy --migrate-host
# or: php artisan hub:migrate-storage-to-host
```

Or use `./recover-hub-storage.sh --sync-legacy` (runs cache clear at the end).

**Emergency recovery (before deploy):** point `public/storage` back at legacy storage:

```bash
cd /var/www/khub.africacdc.org   # your app root
rm -f public/storage
ln -sfn "$(pwd)/storage/app/public" public/storage
```

Files only under `storage/uploads/` need `--sync-legacy` or they are served via `/hub-media/...` after deploy.

---

## Cloud drivers

Optional Composer packages are required on the app server (listed in admin when you select a driver).

| Driver | Package(s) | Notes |
|--------|------------|-------|
| **S3** | `league/flysystem-aws-s3-v3` | AWS or S3-compatible (MinIO); set custom endpoint if needed |
| **GCS** | `google/cloud-storage`, `superbalist/flysystem-google-storage` | Service account JSON key path on server (not in DB) |
| **Azure Blob** | Built-in (Guzzle REST adapter) | Connection string recommended; no extra Composer packages |
| **SharePoint** | Built-in (Guzzle + Graph API) | Azure AD app with `Sites.ReadWrite.All` (application) |
| **SFTP** | Built-in (phpseclib ^3) | SSH key or password; no extra Composer packages |

### Google Cloud Storage

1. Create a bucket and service account with **Storage Object Admin**.
2. Download JSON key to a path outside the web root (e.g. `/etc/khub/gcs-key.json`).
3. In admin: project ID, key file path, bucket, optional root prefix `khub`.

### Microsoft SharePoint (organisational)

For organisations storing data on Microsoft 365 infrastructure:

1. **Azure AD** → App registrations → new app → client secret.
2. **API permissions** → Microsoft Graph → **Sites.ReadWrite.All** (application) → grant admin consent.
3. In admin, provide:
   - Tenant ID, Client ID, Client secret
   - **Site hostname** + **site path** (e.g. `contoso.sharepoint.com` + `sites/KnowledgeHub`), **or** Graph site ID
   - Optional drive ID (defaults to the site document library)

Uses Microsoft Graph `PUT /sites/{site-id}/drive/root:/{path}:/content` for uploads.

### Azure Blob

Use the connection string from Azure Portal → Storage account → Access keys, or account name + key with container name.

### SFTP (manual — no `league/flysystem-sftp`)

This project includes a **built-in SFTP driver** (`App\Filesystem\SftpAdapter`) using **phpseclib ^3**. Do **not** run `composer require league/flysystem-sftp` — it conflicts with phpseclib 3.x already used by Laravel Passport.

Ensure phpseclib is installed (included in `composer.json`):

```bash
composer update phpseclib/phpseclib
```

Configure in **Settings → Storage Management**:

| Field | Example |
|-------|---------|
| Host | `sftp.example.org` |
| Port | `22` |
| Username | `khub` |
| Password | (or use private key below) |
| SSH private key path | `/etc/khub/keys/sftp_id_rsa` |
| Root prefix | `/khub` |

Click **Test connection** — the driver logs in, ensures the remote root exists, then writes and deletes a probe file.

---

## SQL backups

Configured separately from file storage (always on host by default).

| Feature | Location |
|---------|----------|
| Automatic daily backup | Configuration tab → enable; runs at 01:30 via scheduler (`hub:backup-database`) |
| Manual backup | Backups tab → Incremental / Full |
| Retention | Configuration tab → **Retain backups (days)**; old dated folders pruned after save |
| Restore | Backups tab → select folder, choose tables, restore |
| Tables included | Users, authors, geography, publications, forums, communities, approvals, pivots |

Backups are **not** stored in the cloud file driver unless you copy them manually.

**Artisan:** `php artisan hub:backup-database` (incremental) or `php artisan hub:backup-database --full`

### Tables included

Backups are grouped in **Settings → Storage Management** when restoring. They include:

- Site settings, languages, themes, storage settings
- Roles, permissions, access groups
- Users, authors, preferences, badges
- Taxonomy, geography, tags
- Communities, publications, forums (with attachments, comments, approvals)
- Content requests, events, facts, courses, tools
- OAuth API clients (not tokens)

**Excluded (KPI / OWID — re-sync from API instead):** `kpi`, `kpi_narrations`, `kpi_sync_runs`, `subject_areas`, `data`

### Selective restore

When restoring, choose which tables to import. Tables not present in the backup folder are skipped. Use **In backup only** to check only tables that exist in the selected backup snapshot.

---

## Migration to external storage

1. Configure the target driver on the **Configuration** tab and **Test connection**.
2. On the **Migration** tab, use **Queue migration** or **Run now** to copy existing uploads from internal storage.
3. Verify files in **Browse files** before removing old copies.

For legacy `storage/app/public` → host path migration, complete step 1 (host) and optional step 2 (purge legacy) on the **Migration** tab before moving to cloud.

---

## Admin API endpoints (authenticated)

Used by the Storage Management UI and file browser:

| Method | Path | Purpose |
|--------|------|---------|
| `GET` | `/admin/storage-management/system-metrics` | JSON system/storage metrics (`?fresh=1` bypasses cache) |
| `GET` | `/admin/storage-management/migration-status` | Migration job status, file counts, message |
| `GET` | `/admin/storage-management/browse` | Simple JSON directory listing (`area`, `path`); includes `publications` per file |
| `GET` | `/admin/storage-management/publication-references` | Map of filename → linked publications for a folder (`path`) |
| `GET` | `/admin/storage-management/browse-backups` | List SQL backup folders |
| `GET` | `/admin/storage-management/backup-tables` | Tables present in a backup directory |
| `POST` | `/admin/storage-management/test-connection` | Driver connectivity probe |

File manager (package): `GET/POST /file-manager/*` — see [laravel-file-manager routes](https://github.com/alexusmai/laravel-file-manager).

---

## Artisan commands

| Command | Purpose |
|---------|---------|
| `php artisan hub:link-storage` | Create or repair `public/storage` → hub files root |
| `php artisan hub:recover-storage` | Diagnose symlink and active files root |
| `php artisan hub:recover-storage --sync-legacy` | Merge `storage/uploads/` into `storage/app/public/uploads/` |
| `php artisan hub:recover-storage --sync-legacy --migrate-host` | Sync legacy then copy to host path |
| `php artisan hub:migrate-storage-to-host` | Copy legacy uploads to configured host files root |
| `php artisan hub:purge-legacy-storage` | Remove verified legacy copies after host migration |
| `php artisan hub:purge-legacy-storage --dry-run` | Preview purge without deleting |
| `php artisan hub:backup-database` | Incremental SQL backup |
| `php artisan hub:backup-database --full` | Full SQL backup |

Shell helpers: `./link-hub-storage.sh`, `./recover-hub-storage.sh`, `./fix-storage-permissions.sh` (Linux/macOS); `link-hub-storage.bat` / `link-hub-storage.ps1` (Windows).

---

## Legacy installs

If uploads still exist under `storage/app/public` and `/var/khubdata` is empty, the hub **continues using the legacy path** until you explicitly configure a new root or migrate. This avoids breaking existing production sites on upgrade.

---

## Staff portal ecosystem (sibling repo)

When the Africa CDC Staff portal repo is on the same host, **Storage Management → Staff ecosystem** manages uploads for CI3, APM, Helpdesk, and staff-portal.

| Setting | Purpose |
|---------|---------|
| `HUB_STAFF_STORAGE_ENABLED` | Show Staff ecosystem tab (default `true`) |
| `STAFF_REPO_ROOT` | Absolute path to staff git repo |
| `STAFF_BASE_URL` | Used to derive `STAFF_SITE_ID` (e.g. `http://localhost/staff`) |
| `STAFF_HOST_DATA_ROOT` | Default `/var/staffdata` |
| `STAFF_FILES_BACKUP_RETENTION_DAYS` | Prune staff file backups after N days |

Staff migration scripts live in `{STAFF_REPO_ROOT}/scripts/storage/`. See staff repo `docs/STORAGE.md`.

---

## Key files (developers)

| Area | Path |
|------|------|
| Core storage service | `app/Services/HubStorageService.php` |
| System metrics | `app/Services/HubStorageMetricsService.php` |
| Publication ↔ file index | `app/Services/HubStoragePublicationIndexService.php` |
| SQL backups | `app/Services/HubDatabaseBackupService.php` |
| Admin controller | `app/Http/Controllers/Admin/StorageManagementController.php` |
| Hub disk middleware | `app/Http/Middleware/RegisterHubStorageDisk.php` |
| SharePoint adapter | `app/Filesystem/SharePointGraphAdapter.php` |
| SFTP adapter | `app/Filesystem/SftpAdapter.php` |
| Driver registration | `app/Providers/HubStorageServiceProvider.php` |
| Hub storage config | `config/hub_storage.php` |
| File manager config | `config/file-manager.php` |
| Filesystem disk stub | `config/filesystems.php` (`hub` disk; root set at runtime) |
| Settings model | `app/Models/HubStorageSetting.php` |
| Admin UI | `resources/views/admin/storage/index.blade.php` |
| Staff ecosystem service | `app/Services/StaffEcosystemStorageService.php` |
| File manager assets | `public/vendor/file-manager/` |
| Media route | `GET /hub-media/{path}` → `HubMediaController` |
| Helpers | `hub_storage()`, `hub_storage_path()`, `storage_link()` in `app/Helpers/UtilsHelper.php` |
| Commands | `app/Console/Commands/LinkHubStorageCommand.php`, `RecoverHubStorageCommand.php`, `MigrateHostStorageCommand.php`, `PurgeLegacyStorageCommand.php`, `HubDatabaseBackup.php` |
| Jobs | `app/Jobs/MigrateHostStorageJob.php`, `MigrateHubStorageJob.php` |

### Composer dependency (file browser)

```json
"alexusmai/laravel-file-manager": "2.5.4"
```

Version 3.x requires Laravel 9+. This project uses **2.5.4** on Laravel 8. Transitive dependency: `intervention/image` (thumbnails in file manager).
