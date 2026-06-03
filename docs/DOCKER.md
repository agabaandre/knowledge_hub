# Docker deployment — Knowledge Hub

Production-oriented PHP/MySQL tuning follows the [PHP Laravel enterprise optimisation guide](https://github.com/agabaandre/PHP_laravel_Codeigniter_wordpress_server_optimisation_enterprise). Docker uses scaled-down configs under `docker/php/` and `docker/mysql/`.

## Quick start

```bash
cp .env.docker.example .env
docker compose up -d --build
```

Open **http://localhost:8080/install** and complete:

1. **Prerequisites** — PHP, extensions, writable paths (Docker vs local detected automatically)  
2. **Database** — Docker: host `mysql`; local: host `127.0.0.1`, user `root`, password `password`  
3. **Site settings** — site name, description, contact email, timezone (saved to `setting` table)  
4. **Mail** — SMTP or log-only (saved to `.env`)  
5. **Administrator** — locks the installer when finished  

After installation, the app verifies **database**, **active site settings**, and **storage** on every request. Failed checks show a prerequisites page instead of a broken layout.  

## Services

| Service | URL / port |
|---------|------------|
| Web (nginx) | http://localhost:8080 |
| MySQL | localhost:3307 (root / password) |
| Redis | localhost:6380 |
| Meilisearch | http://localhost:7700 (API key: `masterKey` by default) |

After install, import searchable models into Meilisearch:

```bash
docker compose exec app php artisan scout:import "App\\Models\\Publication"
```

The **`queue`** service processes Scout index jobs when `SCOUT_QUEUE=true`.

## Database schema

- **258 migrations** plus `database/schema/mysql-schema.dump` for fast fresh installs (Laravel loads the dump before pending migrations).
- Baseline **roles, permissions, and settings** are imported from `database/install/baseline_seed.sql` when empty after migrate.

## Existing installations

If the app is already deployed, mark it installed so `/install` is disabled:

```bash
php artisan khub:mark-installed
```

This sets `APP_INSTALLED=true`, writes `storage/app/installed.lock`, and sets `installer_locked=1` on the active **setting** row. The installer returns **403 Forbidden** if accessed again — even if someone removes the lock file or edits `.env`.

## CLI install (non-Docker)

```bash
cp .env.docker.example .env   # edit DB_* for your server
composer install
php artisan key:generate
php artisan khub:install --email=admin@example.com --password=secret --first-name=Admin --last-name=User
```

## Production Docker notes

- Set `APP_DEBUG=false` and `APP_ENV=production` in `.env`.
- In `docker/php/opcache.ini`, set `opcache.validate_timestamps=0` and reload PHP-FPM after each deploy.
- Scale `docker/php/www.conf` `pm.max_children` using the tier tables in the optimisation repo.
- Run queue workers separately: `php artisan queue:work redis --sleep=3` (required when `SCOUT_QUEUE=true` for Meilisearch indexing).
- Meilisearch master key defaults to `masterKey` in Docker; set `MEILISEARCH_KEY` in `.env` / compose for production.

## PHP performance files

| File | Purpose |
|------|---------|
| `docker/php/php.ini` | Memory, uploads, security, realpath cache |
| `docker/php/opcache.ini` | OPcache sizing (dev: timestamps on) |
| `docker/php/www.conf` | PHP-FPM dynamic pool |
| `docker/mysql/my.cnf` | InnoDB buffer pool for containers |
