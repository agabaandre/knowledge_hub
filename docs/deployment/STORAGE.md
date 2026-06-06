# Storage management

The Knowledge Hub separates **application code** from **persistent data**. Publication PDFs, cover images, forum attachments, and SQL backups should survive container rebuilds and deployments.

**Admin UI:** Settings → **Storage Management** (`/admin/storage-management`)

---

## Architecture

```
/var/khubdata/                    ← host volume (outside container / git tree)
├── files/                        ← HUB_FILES_ROOT (internal driver)
│   └── uploads/
│       ├── publications/
│       ├── publications/summaries/
│       ├── forums/
│       └── forum/
└── backups/
    └── sql/                      ← HUB_SQL_BACKUP_ROOT

public/storage  →  symlink  →  /var/khubdata/files   (internal driver)
/hub-media/{path}                 ← HTTP route when using custom host paths
/storage/{path}                   ← works via symlink above
```

Application code uses `hub_storage_path()` and `storage_link()` helpers — not hard-coded `storage/app/public` paths.

---

## Default paths

| OS | Files root | SQL backups |
|----|------------|-------------|
| Linux | `/var/khubdata/files` | `/var/khubdata/backups/sql` |
| Windows | `C:\khubdata\files` | `C:\khubdata\backups\sql` |

Override in `.env`:

```env
HUB_FILES_ROOT=/var/khubdata/files
HUB_SQL_BACKUP_ROOT=/var/khubdata/backups/sql
```

---

## Internal driver (recommended default)

Stores files on the host filesystem. Best for single-server or Docker deployments with a mounted volume.

### Setup (Linux)

```bash
sudo mkdir -p /var/khubdata/files /var/khubdata/backups/sql
sudo chown -R www-data:www-data /var/khubdata
./fix-storage-permissions.sh
```

### Docker

Mount a host directory into every app/queue container:

```yaml
volumes:
  - ./khubdata:/var/khubdata
```

Configure during install step 3 or in Storage Management.

### URL linkage

- `public/storage` is symlinked to your files root automatically.
- `storage_link()` returns `/storage/...` for legacy `storage/app/public` installs, or `/hub-media/...` for custom host paths (both work when symlink is present).

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
