# Installation overview

The Knowledge Hub supports three installation paths. Choose based on your environment.

| Path | Best for | Guide |
|------|----------|-------|
| **Docker** | New environments, consistent stack | [../deployment/DOCKER.md](../deployment/DOCKER.md) |
| **Web installer** | LAMP, Apache/Nginx, manual PHP | [WEB_INSTALLER.md](WEB_INSTALLER.md) |
| **CLI** | Servers without browser, automation | [CLI.md](CLI.md) |

All paths run the same **six-step** configuration (prerequisites through administrator account), including **persistent storage** on host paths outside the application tree.

---

## Before you start

1. **PHP 8.0+** with extensions listed in the installer prerequisites step.
2. **MySQL or MariaDB** — create an empty database (default name `knowledge_hub`) unless restoring an existing dump.
3. **Composer** — run `composer install` in the project root.
4. **Host data directory** (Linux example):
   ```bash
   sudo mkdir -p /var/khubdata/files /var/khubdata/backups/sql
   sudo chown -R www-data:www-data /var/khubdata
   ```
   See [../deployment/STORAGE.md](../deployment/STORAGE.md) and [../deployment/PERMISSIONS.md](../deployment/PERMISSIONS.md).

---

## After installation

- `APP_INSTALLED=true` and installer lock are set automatically.
- Configure cloud storage, backups, and migration in **Settings → Storage Management**.
- Import search index: `php artisan scout:import "App\Models\Publication"` (when Meilisearch is enabled).

**Existing production database?** Do not run the web installer blindly — read [EXISTING_DEPLOYMENTS.md](EXISTING_DEPLOYMENTS.md) first.
