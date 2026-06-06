# Docker deployment

Production-oriented PHP/MySQL tuning follows the [PHP Laravel enterprise optimisation guide](https://github.com/agabaandre/PHP_laravel_Codeigniter_wordpress_server_optimisation_enterprise). Docker uses scaled-down configs under `docker/php/` and `docker/mysql/`.

---

## Quick start

```bash
cp .env.docker.example .env
docker compose up -d --build
```

Open **http://localhost:8080/install** and complete all **six steps**:

| Step | Topic |
|------|--------|
| 1 | Prerequisites |
| 2 | Database (host `mysql`, user `root`, password `password`) |
| 3 | **Storage** — mount `/var/khubdata` (see below) |
| 4 | Site settings |
| 5 | Mail |
| 6 | Administrator |

Full installer details: [../installation/WEB_INSTALLER.md](../installation/WEB_INSTALLER.md)

---

## Persistent storage volume

Uploads and SQL backups must **not** live inside the container filesystem. Mount a host directory:

```yaml
services:
  app:
    volumes:
      - ./khubdata:/var/khubdata
  queue:
    volumes:
      - ./khubdata:/var/khubdata
```

On the host before install:

```bash
mkdir -p khubdata
```

The installer defaults to `/var/khubdata/{site-id}/files` and `.../backups/sql` (site ID from `APP_URL`). The container entrypoint runs `php artisan hub:link-storage` so `public/storage` points at the files root. See [STORAGE.md](STORAGE.md).

Add to `.env` (set automatically by installer):

```env
HUB_SITE_ID=localhost-8080
HUB_FILES_ROOT=/var/khubdata/localhost-8080/files
HUB_SQL_BACKUP_ROOT=/var/khubdata/localhost-8080/backups/sql
```

---

## Services

| Service | URL / port |
|---------|------------|
| Web (nginx) | http://localhost:8080 |
| MySQL | localhost:3307 (root / password) |
| Redis | localhost:6380 |
| Meilisearch | http://localhost:7700 (API key: `masterKey` by default) |

After install, import searchable models:

```bash
docker compose exec app php artisan scout:import "App\\Models\\Publication"
```

The **`queue`** service processes Scout index jobs when `SCOUT_QUEUE=true`, and KPI / OWID background tasks. See [../features/KPI_INDICATORS_OWID.md](../features/KPI_INDICATORS_OWID.md).

---

## Database schema

- Migrations plus `database/schema/mysql-schema.dump` for fast fresh installs.
- Baseline **roles, permissions, and settings** from `database/install/baseline_seed.sql` when empty after migrate.

---

## Existing installations

If the app is already deployed, the web installer is **blocked**. Mark installed:

```bash
docker compose exec app php artisan khub:mark-installed
```

See [../installation/EXISTING_DEPLOYMENTS.md](../installation/EXISTING_DEPLOYMENTS.md).

**Skip migrations** on the database step, or:

```bash
docker compose exec app php artisan khub:install --skip-migrate \
  --email=admin@example.com --password='your-secure-password'
```

---

## CLI install (container)

```bash
docker compose exec app php artisan khub:install \
  --email=admin@example.com \
  --password=secret \
  --files-root=/var/khubdata/files \
  --sql-backup-root=/var/khubdata/backups/sql
```

Options: [../installation/CLI.md](../installation/CLI.md)

---

## Production notes

- Set `APP_DEBUG=false` and `APP_ENV=production` in `.env`.
- In `docker/php/opcache.ini`, set `opcache.validate_timestamps=0` and reload PHP-FPM after each deploy.
- Scale `docker/php/www.conf` `pm.max_children` using the tier tables in the optimisation repo.
- Run queue workers: `php artisan queue:work redis --sleep=3` (Scout + KPI jobs).
- KPI weekly OWID refresh: **Admin → KPIs → Indicator data management**.
- Set a strong `MEILISEARCH_KEY` in production.

---

## PHP performance files

| File | Purpose |
|------|---------|
| `docker/php/php.ini` | Memory, uploads, security, realpath cache |
| `docker/php/opcache.ini` | OPcache sizing (dev: timestamps on) |
| `docker/php/www.conf` | PHP-FPM dynamic pool |
| `docker/mysql/my.cnf` | InnoDB buffer pool for containers |
