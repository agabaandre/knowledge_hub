# CLI installation

For servers without browser access or automated provisioning, use `php artisan khub:install`.

---

## Basic usage

```bash
cp .env.example .env          # or .env.docker.example for container-style DB defaults
composer install
php artisan khub:install \
  --email=admin@example.com \
  --password='your-secure-password' \
  --first-name=Admin \
  --last-name=User \
  --site-name='Knowledge Hub'
```

The command runs migrations (unless skipped), configures storage, site settings, mail, creates the admin user, links `public/storage` to the hub files root, and locks the installer.

After any manual storage path change:

```bash
php artisan hub:link-storage
```

---

## Options

| Option | Description |
|--------|-------------|
| `--email` | Admin email (required) |
| `--password` | Admin password, min 8 characters (required) |
| `--first-name` | Default `Admin` |
| `--last-name` | Default `User` |
| `--site-name` | Site name for the `setting` row |
| `--mail-driver` | `log` or `smtp` (default `log`) |
| `--files-root` | Host files path (default `/var/khubdata/files`) |
| `--sql-backup-root` | SQL backup path (default `/var/khubdata/backups/sql`) |
| `--storage-driver` | `internal`, `s3`, `gcs`, `azure`, `sharepoint`, `sftp` (default `internal`) |
| `--skip-migrate` | Skip migrations when DB schema and data already exist |

---

## Examples

**Existing database (no migrations):**

```bash
php artisan khub:install --skip-migrate \
  --email=admin@example.com \
  --password='your-secure-password'
```

**Custom host paths:**

```bash
php artisan khub:install \
  --email=admin@example.com \
  --password='your-secure-password' \
  --files-root=/data/khub/files \
  --sql-backup-root=/data/khub/backups/sql
```

**Mark installed without full setup** (existing production):

```bash
php artisan khub:mark-installed
```

See [EXISTING_DEPLOYMENTS.md](EXISTING_DEPLOYMENTS.md).

---

## Storage link commands

| Command | Description |
|---------|-------------|
| `php artisan hub:link-storage` | Link `public/storage` to the hub files root (macOS, Linux, Windows) |
| `./link-hub-storage.sh` | Shell wrapper (macOS/Linux) |
| `link-hub-storage.bat` | Batch wrapper (Windows CMD) |
| `.\link-hub-storage.ps1` | PowerShell wrapper (Windows) |
| `./fix-storage-permissions.sh` | Fix Laravel + host data permissions, then run `hub:link-storage` |

Full details: [../deployment/STORAGE.md](../deployment/STORAGE.md).
