# Storage permissions

Laravel needs writable `storage/` and `bootstrap/cache/`. Uploads and SQL backups additionally need writable **host data paths** outside the application tree.

---

## Quick fix script

From the project root:

```bash
./fix-storage-permissions.sh
```

This script:

- Creates Laravel cache/session/log directories
- Creates `/var/khubdata/{site-id}/files` and `.../backups/sql` (or paths from `HUB_FILES_ROOT` / `HUB_SQL_BACKUP_ROOT`)
- Sets ownership on `storage`, `bootstrap/cache`, `public/uploads`, and `/var/khubdata`
- Runs **`php artisan hub:link-storage`** so `public/storage` points at the hub files root

Override the web-server group on Linux:

```bash
KHUB_STORAGE_GROUP=www-data ./fix-storage-permissions.sh
```

On macOS the default group is `staff`.

---

## Link `public/storage` (all platforms)

The application prerequisites page checks that `public/storage` resolves to your hub files root. Use the hub linker — **not** `php artisan storage:link` alone when data lives under `/var/khubdata` or `C:\khubdata`.

| Platform | Command |
|----------|---------|
| Artisan (recommended) | `php artisan hub:link-storage` |
| macOS / Linux | `./link-hub-storage.sh` |
| Windows CMD | `link-hub-storage.bat` |
| Windows PowerShell | `.\link-hub-storage.ps1` |

The installer (step 3) and app boot also create or repair this link automatically.

---

## Manual setup (Linux production)

```bash
# Web server user — often www-data (Debian/Ubuntu) or apache (RHEL)
WEB_USER=www-data

sudo mkdir -p /var/khubdata/files /var/khubdata/backups/sql
sudo chown -R $WEB_USER:$WEB_USER /var/khubdata
sudo chmod -R ug+rwX /var/khubdata

sudo chown -R $WEB_USER:$WEB_USER storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

Ensure cron and queue workers run as the **same user** as PHP-FPM/Apache so scheduled SQL backups and uploads succeed.

---

## Common errors

| Symptom | Cause | Fix |
|---------|-------|-----|
| `Permission denied` on `storage/logs` | Wrong user running `php artisan` | Run artisan as web user or fix ownership |
| Badge / queue jobs fail writing cache | `storage/framework/cache` not writable | `./fix-storage-permissions.sh` |
| Uploads fail silently | `/var/khubdata/files` not writable | `chown www-data /var/khubdata` |
| SQL backup fails | `/var/khubdata/backups/sql` missing | Create directory and set permissions |
| Prerequisites: `public/storage symlink` failed | Link points at `storage/app/public` instead of hub files root | `php artisan hub:link-storage` or `./link-hub-storage.sh` |
| Windows link fails | Symlink/junction needs elevated rights | Developer Mode or run CMD as Administrator, then `link-hub-storage.bat` |

---

## Docker

The container PHP user must be able to write the mounted volume. Example:

```yaml
volumes:
  - ./khubdata:/var/khubdata
```

On the host:

```bash
mkdir -p khubdata/files khubdata/backups/sql
chmod -R 777 khubdata   # development only; use proper UID/GID in production
```

Match the container `www-data` UID in production compose files.
