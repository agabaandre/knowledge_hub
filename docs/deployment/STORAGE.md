# Storage management

The Knowledge Hub separates **application code** from **persistent data**. Publication PDFs, cover images, forum attachments, and SQL backups should survive container rebuilds and deployments.

**Admin UI:** Settings → **Storage Management** (`/admin/storage-management`)

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
| Automatic daily backup | Storage Management → enable; runs at 01:30 via scheduler |
| Manual backup | Admin → Storage Management → Incremental / Full |
| Restore | Select backup folder; **empty tables only** recommended |
| Tables included | Users, authors, geography, publications, forums, communities, approvals, pivots |

Backups are **not** stored in the cloud file driver unless you copy them manually.

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

1. Configure the target driver in Storage Management and **Test connection**.
2. Use **Queue migration** or **Run now** to copy existing uploads from internal storage.
3. Verify files in the admin file browser before removing old copies.

---

## Legacy installs

If uploads still exist under `storage/app/public` and `/var/khubdata` is empty, the hub **continues using the legacy path** until you explicitly configure a new root or migrate. This avoids breaking existing production sites on upgrade.

---

## Key files (developers)

| Area | Path |
|------|------|
| Service | `app/Services/HubStorageService.php` |
| SQL backups | `app/Services/HubDatabaseBackupService.php` |
| SharePoint adapter | `app/Filesystem/SharePointGraphAdapter.php` |
| Driver registration | `app/Providers/HubStorageServiceProvider.php` |
| Config | `config/hub_storage.php` |
| Admin UI | `resources/views/admin/storage/index.blade.php` |
| Media route | `GET /hub-media/{path}` → `HubMediaController` |
| Helpers | `hub_storage()`, `hub_storage_path()`, `storage_link()` in `app/Helpers/UtilsHelper.php` |
