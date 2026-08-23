# Knowledge Hub — documentation

Central index for operators, administrators, contributors, and developers. **End users:** start with the [user guide](user-guide.md) (also at `/user_manual` on the portal). **Hub staff:** see the [administrator guide](administrator-guide.md). For new servers start with [installation](installation/README.md); for production operations see [deployment](deployment/README.md).

---

## User and administrator guides

| Document | Audience | Description |
|----------|----------|-------------|
| [user-guide.md](user-guide.md) | Visitors, contributors, reviewers | Search, publish, forums, communities, federation browse, approvals inbox (in-app: `/user_manual`) |
| [administrator-guide.md](administrator-guide.md) | Admins, operators | Dashboards, approvals, slugs, admin units, federation, Artisan (in-app: `/administrator-guide`) |

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

### Architecture & design

| Document | Audience | Description |
|----------|----------|-------------|
| [architecture/KH_Portal_Architecture.md](architecture/KH_Portal_Architecture.md) | All | Three-tier architecture: portal, admin, API, AI, storage, federation |
| [architecture/KH_Knowledge_Flow.md](architecture/KH_Knowledge_Flow.md) | All | Knowledge lifecycle from onboarding through impact |
| [../KH_Portal_Architecture_Structure.png](../KH_Portal_Architecture_Structure.png) | All | Architecture diagram (PNG) |
| [../Knowledge Flow structure.png](../Knowledge%20Flow%20structure.png) | All | Knowledge flow diagram (PNG) |

### Features & modules

| Document | Audience | Description |
|----------|----------|-------------|
| [features/FEATURE_ENHANCEMENTS.md](features/FEATURE_ENHANCEMENTS.md) | Admin, dev | Tags AI, email config, forum sharing, CoP admin & detail |
| [features/APPROVALS.md](features/APPROVALS.md) | Admin, reviewers | Central approvals inbox, notification links, daily digest |
| [features/SEO_SLUGS.md](features/SEO_SLUGS.md) | Admin, ops | Slug catalogue, rename sync, `php artisan slugs:regenerate` |
| [features/KPI_INDICATORS_OWID.md](features/KPI_INDICATORS_OWID.md) | Admin, dev | Country indicators, OWID import, KPI queues |
| [features/CONTENT_REQUEST_REFERRALS.md](features/CONTENT_REQUEST_REFERRALS.md) | Admin, dev | Content request referrals |
| [features/FORUM_ATTACHMENTS_PDF.md](features/FORUM_ATTACHMENTS_PDF.md) | Dev / ops | Forum Office → PDF conversion |
| [features/FEDERATION.md](features/FEDERATION.md) | Admin, dev | Federated hubs, central metadata/branding sync, federation API |

### Security

| Document | Audience | Description |
|----------|----------|-------------|
| [security/RECORDS_SEARCH_SECURITY_TESTING.md](security/RECORDS_SEARCH_SECURITY_TESTING.md) | Dev, ops | Records search SQL-injection probes and production smoke results |

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
