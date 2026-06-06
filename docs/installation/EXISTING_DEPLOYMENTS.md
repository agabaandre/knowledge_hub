# Existing deployments

Use this guide when the Knowledge Hub is **already running** or when you are **restoring** a database dump — not for a greenfield install.

---

## When the web installer is blocked

Visiting `/install` returns **403 Forbidden** when **both** are true:

1. `vendor/autoload.php` exists (Composer dependencies installed)
2. The configured database has tables with data

This prevents accidental re-installation on production.

### Mark the application as installed

```bash
php artisan khub:mark-installed
```

This sets:

- `APP_INSTALLED=true` and `INSTALLER_DISABLED=true` in `.env`
- `storage/app/installed.lock`
- `installer_locked=1` on the active **setting** row

The installer stays disabled even if someone removes the lock file without updating the database flag.

---

## Reconnecting an existing database via installer

If you need the installer UI (non-production only):

1. Import your SQL dump into MySQL.
2. Open `/install` (if not blocked) or temporarily set `APP_INSTALLED=false` on a **dev** machine only.
3. On the database step, check **Skip migrations**.
4. Complete storage, site, mail, and admin steps as needed.

**Blocked?** Run `khub:mark-installed` and configure remaining items in the admin UI.

---

## CLI equivalent

```bash
php artisan khub:install --skip-migrate \
  --email=admin@example.com \
  --password='your-secure-password'
```

---

## Unlock installer (development only)

On a **non-production** machine only:

1. Set `APP_INSTALLED=false` in `.env`
2. Remove `storage/app/installed.lock`
3. Clear `installer_locked` on the **setting** row

Never do this on a live production hub without a maintenance window and backup.

---

## Storage on existing sites

If uploads still live under `storage/app/public`, the hub continues using that path until you configure **Settings → Storage Management** or set `HUB_FILES_ROOT`. See [../deployment/STORAGE.md](../deployment/STORAGE.md) for migration to `/var/khubdata` or cloud storage.
