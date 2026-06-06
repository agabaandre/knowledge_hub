# Web installer

Open **`{APP_URL}/install`** after `composer install`. Docker and bare-metal hosts are detected automatically (database host defaults to `mysql` in containers, `127.0.0.1` locally).

---

## Six steps

| Step | Route | What it configures |
|------|-------|-------------------|
| 1. Prerequisites | `/install` | PHP version, extensions, writable paths, `vendor/`, host data paths (`/var/khubdata`) |
| 2. Database | `/install/database` | `DB_*` in `.env`, migrations (optional skip), `APP_KEY`, baseline seed |
| 3. **Storage** | `/install/storage` | Files driver, host paths, SQL backup root, optional cloud credentials |
| 4. Site settings | `/install/site` | Active `setting` row (name, SEO, timezone, contact) |
| 5. Mail | `/install/mail` | SMTP or log driver in `.env` |
| 6. Administrator | `/install/admin` | First admin user; locks the installer |

---

## Step 3 — Storage (important)

Publication and forum uploads are **not** stored inside the container or git tree by default.

| Setting | Default (Linux) | Default (Windows) | Purpose |
|---------|-----------------|-------------------|--------|
| Host files root | `/var/khubdata/{site-id}/files` | `C:\khubdata\{site-id}\files` | PDFs, covers, forum attachments |
| SQL backup root | `/var/khubdata/{site-id}/backups/sql` | `C:\khubdata\{site-id}\backups\sql` | Daily incremental SQL dumps |

`{site-id}` is derived from `APP_URL` (e.g. `http://localhost/knowledge_hub` → `localhost-knowledge-hub`). Override with `HUB_SITE_ID` in `.env`.

These paths are written to `.env` as `HUB_FILES_ROOT` and `HUB_SQL_BACKUP_ROOT`.

**Docker:** mount a host volume before install:

```yaml
services:
  app:
    volumes:
      - ./khubdata:/var/khubdata
```

**Drivers:** Internal (default) keeps files on the host. Cloud drivers (S3, GCS, Azure, SharePoint, SFTP) can be selected during install or configured later in admin. See [../deployment/STORAGE.md](../deployment/STORAGE.md).

The installer creates upload subdirectories and links `public/storage` → your files root (symlink on Linux/macOS, symlink or junction on Windows) so `/storage/...` URLs keep working.

If you need to repair the link after install:

```bash
php artisan hub:link-storage
```

Platform helpers: `./link-hub-storage.sh` (macOS/Linux), `link-hub-storage.bat` or `.\link-hub-storage.ps1` (Windows). See [../deployment/STORAGE.md#url-linkage-publicstorage](../deployment/STORAGE.md#url-linkage-publicstorage).

---

## Database step — skip migrations

If the target database **already contains tables**, a checkbox appears:

**Skip migrations (use existing database schema and data)**

| Checked | Unchecked |
|---------|-----------|
| Saves `DB_*` to `.env`, generates `APP_KEY` if needed | Runs full `migrate`, baseline seed, Passport when empty |
| Does **not** run migrations | Fresh schema on empty database |
| Default when data already exists | Default on empty database |

Use when reconnecting an imported or restored database.

---

## Installer lock & existing deployments

The installer returns **403** when Composer packages exist **and** the database already has data — protecting live sites from accidental re-install.

See [EXISTING_DEPLOYMENTS.md](EXISTING_DEPLOYMENTS.md) for `khub:mark-installed` and recovery options.

---

## Post-install checks

Each request verifies database connectivity, active site settings, writable storage, and that `public/storage` points at the hub files root. Failures show a prerequisites page instead of a broken layout.

Fix a failed `public/storage symlink` check:

```bash
php artisan hub:link-storage
```

Relevant `.env` keys:

```env
APP_INSTALLED=true
INSTALLER_DISABLED=true
APP_URL=https://your-hub.example
HUB_SITE_ID=your-hub-example          # optional; auto from APP_URL
HUB_FILES_ROOT=/var/khubdata/your-hub-example/files
HUB_SQL_BACKUP_ROOT=/var/khubdata/your-hub-example/backups/sql
```
