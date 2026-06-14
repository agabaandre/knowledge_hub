
# Africa CDC Knowledge Hub

Web platform for health knowledge management: publications, forums, communities of practice, KPI indicators, and admin tooling.

**Full documentation:** [docs/README.md](docs/README.md)

### Recent features (June 2026)

| Area | Highlights |
|------|------------|
| **Community detail** | Tabbed activity (Wall · Publications · Forums · Processed requests), React-powered tab switching without page reload, collapsible publication attachments, forum-style publication cards |
| **Contributors** | Poster organisation from account profile (not publication metadata) on community cards and author profile pages |
| **Admin CoP** | Communities DataTables fix, region→country linking on create/edit |

Details: [docs/features/FEATURE_ENHANCEMENTS.md](docs/features/FEATURE_ENHANCEMENTS.md#frontend--communities-of-practice-detail)

---

## Quick start

### Docker (recommended)

```bash
cp .env.docker.example .env
docker compose up -d --build
# Mount ./khubdata:/var/khubdata — see docs/deployment/DOCKER.md
open http://localhost:8080/install
```

### Bare metal / LAMP

```bash
git clone https://github.com/Africa-cdc-Khub/knowledge_hub.git
cd knowledge_hub
composer install
sudo mkdir -p /var/khubdata
./fix-storage-permissions.sh
open http://localhost/knowledge_hub/install
```

### CLI (no browser)

```bash
composer install
php artisan khub:install \
  --email=admin@example.com \
  --password='your-secure-password' \
  --files-root=/var/khubdata/files
```

---

## Web installer (6 steps)

| Step | Route | Topic |
|------|-------|--------|
| 1 | `/install` | Prerequisites |
| 2 | `/install/database` | Database connection & migrations |
| 3 | `/install/storage` | Host paths (`/var/khubdata`) or cloud driver |
| 4 | `/install/site` | Site name, SEO, timezone |
| 5 | `/install/mail` | SMTP or log driver |
| 6 | `/install/admin` | Administrator account |

Details: [docs/installation/WEB_INSTALLER.md](docs/installation/WEB_INSTALLER.md)

**Existing production database?** Read [docs/installation/EXISTING_DEPLOYMENTS.md](docs/installation/EXISTING_DEPLOYMENTS.md) before running the installer.

---

## Documentation

| Section | Index |
|---------|--------|
| **All docs** | [docs/README.md](docs/README.md) |
| Installation | [docs/installation/](docs/installation/README.md) |
| Deployment & storage | [docs/deployment/](docs/deployment/README.md) |
| Features | [docs/features/](docs/features/FEATURE_ENHANCEMENTS.md) |

| Topic | Document |
|-------|----------|
| File storage, cloud drivers, SQL backups | [docs/deployment/STORAGE.md](docs/deployment/STORAGE.md) |
| Docker, volumes, queue, Meilisearch | [docs/deployment/DOCKER.md](docs/deployment/DOCKER.md) |
| Permissions script | [docs/deployment/PERMISSIONS.md](docs/deployment/PERMISSIONS.md) |
| KPI / OWID indicators | [docs/features/KPI_INDICATORS_OWID.md](docs/features/KPI_INDICATORS_OWID.md) |
| Forum Office → PDF | [docs/features/FORUM_ATTACHMENTS_PDF.md](docs/features/FORUM_ATTACHMENTS_PDF.md) |
| Community detail (tabs, React, publications) | [docs/features/FEATURE_ENHANCEMENTS.md](docs/features/FEATURE_ENHANCEMENTS.md#frontend--communities-of-practice-detail) |

---

## Prerequisites

| Component | Version / notes |
|-----------|-----------------|
| PHP | 8.0+ (extensions checked at `/install`) |
| Database | MySQL or MariaDB |
| Web server | Apache or Nginx with URL rewriting |
| Composer | PHP dependencies |
| Node.js | 16.x+ (front-end assets) |
| Redis | Optional; recommended for cache, sessions, queues |
| LibreOffice | Recommended for forum attachment PDF conversion |
| Host data path | `/var/khubdata` (Linux), `C:\khubdata` (Windows) — outside app tree |

Manual LAMP setup steps: [docs/installation/README.md](docs/installation/README.md#before-you-start)

---

## Environment variables (common)

```env
APP_URL=https://your-hub.example
APP_INSTALLED=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=knowledge_hub
DB_USERNAME=root
DB_PASSWORD=

# Persistent uploads & SQL backups (outside container; {site-id} from APP_URL)
HUB_SITE_ID=
HUB_FILES_ROOT=/var/khubdata/{site-id}/files
HUB_SQL_BACKUP_ROOT=/var/khubdata/{site-id}/backups/sql

# Optional: LibreOffice for forum PDF conversion
# LIBREOFFICE_BINARY=/usr/bin/soffice
```

After install: `APP_INSTALLED=true`, `INSTALLER_DISABLED=true`

---

## Storage & URLs

- Uploads default to **`/var/khubdata/{site-id}/files`** (not `storage/app/public` inside the container).
- `public/storage` must link to that files root — created automatically on install and app boot.
- Configure drivers and backups in **Settings → Storage Management**.

**Link or repair `public/storage`:**

```bash
php artisan hub:link-storage          # all platforms
./link-hub-storage.sh                 # macOS / Linux
link-hub-storage.bat                  # Windows CMD
```

See [docs/deployment/STORAGE.md](docs/deployment/STORAGE.md).

---

## Forum attachments: Office to PDF

Forum comment uploads (Word, Excel, PowerPoint, etc.) are converted to PDF when possible.

| Requirement | Notes |
|-------------|--------|
| Composer packages | `phpoffice/phpword`, `mpdf/mpdf` (included) |
| LibreOffice (`soffice`) | Strongly recommended for full format support |
| Writable upload path | `hub_storage_path('uploads/forum')` — typically under `/var/khubdata/files` |

Full guide: [docs/features/FORUM_ATTACHMENTS_PDF.md](docs/features/FORUM_ATTACHMENTS_PDF.md)

---

## REST API documentation

- **Swagger UI:** `{APP_URL}/docs`
- **OpenAPI JSON:** `{APP_URL}/docs/spec/api-docs.json`

Regenerate after annotation changes:

```bash
php artisan l5-swagger:generate
```

Notable public endpoints: `GET /api/publications`, `GET /api/home`, `GET /api/health-topics`, paginated section feeds under `/api/publications/sections/*`.

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Installer 403 | Existing DB with data — [EXISTING_DEPLOYMENTS.md](docs/installation/EXISTING_DEPLOYMENTS.md) |
| Prerequisites page (503) | Check DB, active `setting` row, writable `storage/` and `/var/khubdata` |
| `public/storage symlink` failed | Run `php artisan hub:link-storage` — not `storage:link` alone — [STORAGE.md](docs/deployment/STORAGE.md) |
| Permission denied on logs/cache | Run `./fix-storage-permissions.sh` — [PERMISSIONS.md](docs/deployment/PERMISSIONS.md) |
| 404 on routes | Enable Apache `mod_rewrite` / Nginx try_files |
| Numeric job titles on users | `php artisan users:fix-job-title-ids --dry-run` then apply |

---

## Legacy manual install

If you cannot use the web installer or `khub:install`:

```bash
cp .env.example .env
composer install && npm install
php artisan migrate
php artisan db:seed
php artisan khub:mark-installed
php artisan hub:link-storage
```

Configure storage in admin or set `HUB_FILES_ROOT` before uploading content.

---

## Conclusion

For installation, deployment, storage, and feature guides, use the **[documentation index](docs/README.md)**. For API and extension work, explore `app/` and regenerate OpenAPI specs as needed.
