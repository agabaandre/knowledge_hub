# Knowledge Hub — documentation

Central index for operators, administrators, and developers. Start with [installation](installation/README.md) for new deployments, or [deployment](deployment/README.md) for production operations.

---

## Quick links

| I want to… | Start here |
|------------|------------|
| Install on Docker | [deployment/DOCKER.md](deployment/DOCKER.md) |
| Install on LAMP / bare metal | [installation/WEB_INSTALLER.md](installation/WEB_INSTALLER.md) |
| Configure file & backup storage | [deployment/STORAGE.md](deployment/STORAGE.md) |
| Fix storage permissions | [deployment/PERMISSIONS.md](deployment/PERMISSIONS.md) |
| Link `public/storage` to hub files root | `php artisan hub:link-storage` — [deployment/STORAGE.md](deployment/STORAGE.md#url-linkage-publicstorage) |
| Mark an existing site as installed | [installation/EXISTING_DEPLOYMENTS.md](installation/EXISTING_DEPLOYMENTS.md) |
| Run install from the CLI | [installation/CLI.md](installation/CLI.md) |

---

## Documentation map

### Installation

| Document | Audience | Description |
|----------|----------|-------------|
| [installation/README.md](installation/README.md) | Ops | Installation paths overview |
| [installation/WEB_INSTALLER.md](installation/WEB_INSTALLER.md) | Ops | Six-step browser installer (`/install`) |
| [installation/CLI.md](installation/CLI.md) | Ops | `php artisan khub:install` |
| [installation/EXISTING_DEPLOYMENTS.md](installation/EXISTING_DEPLOYMENTS.md) | Ops | Skip migrations, lock installer, reconnect DB |

### Deployment & operations

| Document | Audience | Description |
|----------|----------|-------------|
| [deployment/README.md](deployment/README.md) | Ops | Production checklist |
| [deployment/DOCKER.md](deployment/DOCKER.md) | Dev / ops | Containers, volumes, queue, Meilisearch |
| [deployment/STORAGE.md](deployment/STORAGE.md) | Ops / admin | Host paths, cloud drivers, SQL backups, migration, file manager, live metrics |
| [deployment/PERMISSIONS.md](deployment/PERMISSIONS.md) | Ops | `fix-storage-permissions.sh`, ownership |

### Features & modules

| Document | Audience | Description |
|----------|----------|-------------|
| [features/FEATURE_ENHANCEMENTS.md](features/FEATURE_ENHANCEMENTS.md) | Admin, dev | Tags AI, email config, forum sharing, CoP admin & detail |
| [features/KPI_INDICATORS_OWID.md](features/KPI_INDICATORS_OWID.md) | Admin, dev | Country indicators, OWID import, KPI queues |
| [features/CONTENT_REQUEST_REFERRALS.md](features/CONTENT_REQUEST_REFERRALS.md) | Admin, dev | Content request referrals |
| [features/FORUM_ATTACHMENTS_PDF.md](features/FORUM_ATTACHMENTS_PDF.md) | Dev / ops | Forum Office → PDF conversion |
| [features/FEDERATION.md](features/FEDERATION.md) | Admin, dev | Federated hubs, central metadata/branding sync, federation API |

### Project root

| Document | Description |
|----------|-------------|
| [../README.md](../README.md) | Project overview, prerequisites summary, API index, troubleshooting |

---

## Admin UI reference (storage)

After install, file storage is managed at **Settings → Storage Management** (`/admin/storage-management`):

| Tab | Features |
|-----|----------|
| **Overview** | Live storage & system metrics (disk, uploads, backups, RAM, CPU, queue, DB, deployment); auto-refresh every 30s |
| **Configuration** | Files driver, host paths, cloud credentials, SQL backup root & retention, test connection |
| **Migration** | Legacy → host path copy, purge verified legacy copies, migration to cloud storage |
| **SQL backups** | Incremental/full backup, selective table restore |
| **Browse files** | [Laravel File Manager](https://github.com/alexusmai/laravel-file-manager) on hub disk; linked publication names from database |
| **Server setup** | DevOps commands, paths, `.env` overrides |

Drivers: internal (host path), S3, GCS, Azure Blob, SharePoint, SFTP.

See [deployment/STORAGE.md](deployment/STORAGE.md) for architecture, Artisan commands, API endpoints, and recovery procedures.
