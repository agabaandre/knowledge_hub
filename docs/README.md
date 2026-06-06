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
| [deployment/STORAGE.md](deployment/STORAGE.md) | Ops / admin | Host paths, cloud drivers, SQL backups, migration |
| [deployment/PERMISSIONS.md](deployment/PERMISSIONS.md) | Ops | `fix-storage-permissions.sh`, ownership |

### Features & modules

| Document | Audience | Description |
|----------|----------|-------------|
| [features/FEATURE_ENHANCEMENTS.md](features/FEATURE_ENHANCEMENTS.md) | Admin, dev | Tags AI, email config, forum sharing, CoP |
| [features/KPI_INDICATORS_OWID.md](features/KPI_INDICATORS_OWID.md) | Admin, dev | Country indicators, OWID import, KPI queues |
| [features/CONTENT_REQUEST_REFERRALS.md](features/CONTENT_REQUEST_REFERRALS.md) | Admin, dev | Content request referrals |
| [features/FORUM_ATTACHMENTS_PDF.md](features/FORUM_ATTACHMENTS_PDF.md) | Dev / ops | Forum Office → PDF conversion |

### Project root

| Document | Description |
|----------|-------------|
| [../README.md](../README.md) | Project overview, prerequisites summary, API index, troubleshooting |

---

## Admin UI reference (storage)

After install, file storage is managed at **Settings → Storage Management** (`/admin/storage-management`):

- Files driver (internal, S3, GCS, Azure, SharePoint, SFTP)
- Host paths for internal storage and SQL backups
- Connection test, file browser, migration to external storage
- SQL backup and restore

See [deployment/STORAGE.md](deployment/STORAGE.md) for full configuration.
