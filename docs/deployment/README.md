# Deployment & operations

Production operations for the Knowledge Hub: containers, persistent storage, permissions, queues, and search.

---

## Checklist

| Task | Document |
|------|----------|
| Deploy with Docker | [DOCKER.md](DOCKER.md) |
| Configure file storage & backups | [STORAGE.md](STORAGE.md) |
| Fix Laravel / host data permissions | [PERMISSIONS.md](PERMISSIONS.md) |
| Import search index | [DOCKER.md#meilisearch](DOCKER.md) |
| KPI / OWID background jobs | [../features/KPI_INDICATORS_OWID.md](../features/KPI_INDICATORS_OWID.md) |

---

## Environment variables (operations)

| Variable | Purpose |
|----------|---------|
| `APP_URL` | Public site URL (used in file links, mail, and `{site-id}` derivation) |
| `HUB_SITE_ID` | Optional explicit hub storage ID (default: derived from `APP_URL`) |
| `HUB_FILES_ROOT` | Host path for uploads (default `/var/khubdata/{site-id}/files` or `C:\khubdata\{site-id}\files`) |
| `HUB_SQL_BACKUP_ROOT` | Host path for SQL backups (default `.../backups/sql` under the same site folder) |
| `QUEUE_CONNECTION` | Use `redis` in production for Scout and KPI jobs |
| `SCOUT_DRIVER` / `MEILISEARCH_*` | Full-text search |
| `APP_INSTALLED` | Must be `true` after setup |

Cloud storage credentials are stored in the database (`hub_storage_settings.cloud_config`), not in `.env`, except optional env fallbacks for AWS/Azure/GCS.

---

## Scheduled tasks

Ensure the system cron runs Laravel's scheduler:

```bash
* * * * * cd /path/to/knowledge_hub && php artisan schedule:run >> /dev/null 2>&1
```

Includes daily SQL backup (`hub:backup-database`) when enabled in Storage Management, nightly federated hub sync (`federation:sync`) on continental portals, daily approval summaries (`approvals:daily-summary` at 08:00), and sitemap generation.

---

## Useful Artisan commands

| Command | Purpose |
|---------|---------|
| `php artisan hub:link-storage` | Create or repair `public/storage` → hub files root |
| `php artisan hub:recover-storage` | Diagnose storage symlink and active files root |
| `php artisan hub:recover-storage --sync-legacy` | Merge legacy `storage/uploads/` tree into `storage/app/public/uploads/` |
| `php artisan hub:migrate-storage-to-host` | Copy legacy uploads to `/var/khubdata/{site-id}/files` |
| `php artisan hub:purge-legacy-storage` | Remove verified legacy copies after host migration (`--dry-run` to preview) |
| `php artisan hub:backup-database` | Run incremental SQL backup (same as scheduled job) |
| `php artisan hub:backup-database --full` | Run full SQL backup |
| `php artisan federation:sync` | Sync public content from country hubs (`--all` for every active hub) |
| `php artisan federation:sync --hub=1` | Sync a single federated hub by ID |
| `php artisan slugs:regenerate` | Rebuild SEO slugs from current titles (`--only-empty`, `--dry-run`) |
| `php artisan approvals:daily-summary` | Email pending-approval digest (also scheduled at 08:00) |

Host `php artisan` (not inside Docker) remaps `DB_HOST=mysql` to `127.0.0.1`. If Compose publishes MySQL on another port, set `DB_PORT_FORWARD` (for this stack the default published port is `3307`). Prefer `docker compose exec app php artisan migrate` so you use the container network.

Shell helpers: `./link-hub-storage.sh`, `./recover-hub-storage.sh`, `./fix-storage-permissions.sh` (Linux/macOS); `link-hub-storage.bat` / `link-hub-storage.ps1` (Windows).

**Storage Management UI** (`/admin/storage-management`): tabbed overview with live metrics, configuration, host/cloud migration, SQL backup/restore, Laravel File Manager browse with publication linking, and server setup reference. See [STORAGE.md](STORAGE.md).

See also [PERMISSIONS.md](PERMISSIONS.md).
