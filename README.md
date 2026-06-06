
# Installation Guide for Africa CDC Knowledge Hub

This guide covers the setup process for the Africa CDC Knowledge Hub on both Windows and Linux environments.

---

## Docker deployment (recommended for new environments)

See **[docs/DOCKER.md](docs/DOCKER.md)** for container setup, the web installer at `/install`, and PHP performance tuning based on the [enterprise PHP/Laravel optimisation guide](https://github.com/agabaandre/PHP_laravel_Codeigniter_wordpress_server_optimisation_enterprise).

Quick start:

```bash
cp .env.docker.example .env
docker compose up -d --build
# Open http://localhost:8080/install — 5 steps (prerequisites → database → site → mail → admin)
```

---

## Web installer (recommended for bare-metal / LAMP)

After cloning the repository and running `composer install`, open **`{APP_URL}/install`** in a browser. The installer walks through five steps:

| Step | Route | What it does |
|------|-------|--------------|
| 1. Prerequisites | `/install` | PHP version, extensions, writable paths, `vendor/` presence |
| 2. Database | `/install/database` | Saves `DB_*` to `.env`, runs migrations (optional — see below) |
| 3. Site settings | `/install/site` | Active row in the `setting` table (name, description, timezone, contact) |
| 4. Mail | `/install/mail` | SMTP or log driver saved to `.env` |
| 5. Administrator | `/install/admin` | Creates the first admin user and locks the installer |

Docker and local hosts are detected automatically (Docker defaults to database host `mysql`; local defaults to `127.0.0.1` and database name `knowledge_hub`).

### Optional migrations (existing database)

On the **database** step, if the target database already contains tables, a checkbox appears:

**Skip migrations (use existing database schema and data)**

- **Checked (default when the database already has data):** saves the connection to `.env`, generates `APP_KEY` and the storage symlink if needed, and **does not** run `migrate`, baseline seed, or Passport install.
- **Unchecked:** runs a full fresh setup (migrations, roles/permissions baseline when empty, Passport when needed).

Use this when reconnecting an imported or restored database, or when the schema is already present and you only need to finish site/mail/admin setup.

### Existing deployments (installer auto-block)

The web installer is **blocked** when **both** are true:

1. Composer dependencies are present (`vendor/autoload.php` exists)
2. The configured database has **tables with data**

In that case, visiting `/install` returns **403 Forbidden** with instructions to mark the application as installed. This protects live sites (for example an already-running production hub) from being run through the installer by mistake.

Mark an existing deployment as installed:

```bash
php artisan khub:mark-installed
```

This sets `APP_INSTALLED=true` and `INSTALLER_DISABLED=true` in `.env`, writes `storage/app/installed.lock`, and sets `installer_locked=1` on the active **setting** row. The installer stays disabled even if someone removes the lock file or edits `.env` without updating the database flag.

You can also set `APP_INSTALLED=true` manually in `.env`, but the Artisan command is preferred because it updates all lock mechanisms.

### CLI installation (non-interactive)

For servers without browser access, use:

```bash
cp .env.example .env          # or .env.docker.example for Docker-style DB defaults
composer install
php artisan khub:install \
  --email=admin@example.com \
  --password='your-secure-password' \
  --first-name=Admin \
  --last-name=User \
  --site-name='Knowledge Hub'
```

Use **`--skip-migrate`** when the database schema and data already exist (same behaviour as checking “Skip migrations” in the web installer):

```bash
php artisan khub:install --skip-migrate --email=admin@example.com --password='your-secure-password'
```

Other options: `--mail-driver=log|smtp`, `--first-name`, `--last-name`, `--site-name`.

### After installation

When setup completes (web or CLI), the application:

- Sets `APP_INSTALLED=true` and `INSTALLER_DISABLED=true`
- Verifies **database connection**, **active site settings**, and **writable storage** on each request (shows a prerequisites page instead of a broken layout if something fails)
- Redirects `/install` to the home page, or returns **403** if the installer is locked

Relevant `.env` keys:

```env
APP_INSTALLED=true
INSTALLER_DISABLED=true
APP_URL=https://your-hub.example
```

For Docker-specific notes (Meilisearch import, queue workers, production tuning), see **[docs/DOCKER.md](docs/DOCKER.md)**.

### Documentation index

| Document | Audience | Topic |
|----------|----------|--------|
| [docs/FEATURE_ENHANCEMENTS.md](docs/FEATURE_ENHANCEMENTS.md) | Admin, users, dev | Recent features: tags AI, WHO descriptions, email configure, forum sharing, CoP participants |
| [docs/DOCKER.md](docs/DOCKER.md) | Dev / ops | Container deploy, queue, Meilisearch |
| [docs/KPI_INDICATORS_OWID.md](docs/KPI_INDICATORS_OWID.md) | Users, admin, dev | Country indicators, OWID import, KPI settings, queue tasks |
| [docs/CONTENT_REQUEST_REFERRALS.md](docs/CONTENT_REQUEST_REFERRALS.md) | Admin, dev | Content request referrals |
| [docs/FORUM_ATTACHMENTS_PDF.md](docs/FORUM_ATTACHMENTS_PDF.md) | Dev / ops | Forum Office → PDF conversion |

---

## 1. Prerequisites

Ensure you have the following installed:

- Operating System: Windows, Linux, or macOS
- Web Server: Apache or Nginx
- Database: MySQL or MariaDB
- PHP Version: 8.x or later
- Node.js Version: 16.x or later
- Composer: PHP dependency manager
- Git: Version control
- Redis (Optional but recommended for caching)
- **(Recommended for production forums)** [LibreOffice](https://www.libreoffice.org/) (headless) — converts forum comment attachments (Word, Excel, PowerPoint, etc.) to PDF for storage and preview. PHP libraries alone cover only part of this; see [Forum attachments: Office to PDF](#forum-attachments-office-to-pdf).

---

## 2. Windows Installation

### 2.1 Install WAMP/XAMPP

1. To set up PHP, MySQL, and Apache, download and install WAMP or XAMPP:
   - [Download XAMPP](https://www.apachefriends.org/download.html)
   - [Download WAMP](http://www.wampserver.com/en/)

2. Follow the installation instructions for either WAMP or XAMPP.

### 2.2 Install Composer

1. Download and install Composer from [here](https://getcomposer.org/download/) to manage PHP dependencies.

### 2.3 Install Node.js

1. Download and install Node.js from [here](https://nodejs.org/) for JavaScript dependency management.

---

## 3. Linux Installation

### 3.1 Install LAMP Stack (Linux)

To install Apache, MySQL, and PHP on a Linux (Ubuntu) system, follow these steps:

```bash
sudo apt update
sudo apt install apache2
sudo apt install mysql-server
sudo apt install php libapache2-mod-php php-mysql
```

### 3.2 Install Composer

Install Composer on Linux:

```bash
sudo apt install composer
```

### 3.3 Install Node.js

Install Node.js and npm:

```bash
sudo apt install nodejs
sudo apt install npm
```

---

## 4. Clone the Knowledge Hub Repository

Clone the Knowledge Hub repository from GitHub into your web server directory:

```bash
git clone https://github.com/Africa-cdc-Khub/knowledge_hub.git
```

For Windows:
- Extract the project into your XAMPP or WAMP htdocs folder:
  ```bash
  C:/xampp/htdocs/knowledge_hub
  ```

For Linux:
- Place the folder in the Apache web root directory:
  ```bash
  /var/www/html/knowledge_hub
  ```

---

## 5. Create Environment Configuration (.env)

> **Recommended:** skip manual migration steps below and use the **[web installer](#web-installer-recommended-for-bare-metal--lamp)** at `/install` after `composer install`.

1. Inside the project directory, create an .env file by copying the example file:
   ```bash
   cp .env.example .env
   ```

2. Open the .env file and update the following variables with your database details:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=knowledge_hub
DB_USERNAME=root
DB_PASSWORD=
APP_INSTALLED=false
INSTALLER_DISABLED=false
```

3. Configure other necessary environment settings:

```env
STATES_ENABLED=TRUE
ADMIN_UNITS_ENABLED=FALSE
APP_URL=http://localhost/knowledge_hub

# Optional: full path to LibreOffice "soffice" if not found on PATH (forum PDF conversion)
# LIBREOFFICE_BINARY=/usr/bin/soffice
```

---

## 6. Create and Import Database

Create an empty MySQL/MariaDB database named **`knowledge_hub`** (or your chosen name — match `DB_DATABASE` in `.env`).

### Fresh install via web installer

Leave the database empty and complete **step 2 (Database)** at `/install`. Migrations, roles, permissions, and baseline settings are applied automatically unless you choose **Skip migrations**.

### Restore from backup or SQL dump

1. Import your dump into the database (phpMyAdmin, or `mysql -u root -p knowledge_hub < backup.sql`).
2. Run `composer install` if not already done.
3. Open `/install` and on the database step check **Skip migrations** so existing schema and data are preserved.
4. If `vendor/` is present and the database already contains data, the installer may be blocked — run `php artisan khub:mark-installed` instead and use the admin UI for any remaining configuration.

### Legacy starter SQL (manual path only)

If you are **not** using the web installer and rely on the older manual flow:

**Windows (phpMyAdmin):** create database `knowledge_hub`, import the starter SQL file.

**Linux (MySQL CLI):**

```bash
mysql -u root -p knowledge_hub < path_to_starter_db.sql
```

---

## 7. Install Dependencies

### 7.1 PHP Dependencies

Install PHP dependencies using Composer:

```bash
composer install --ignore-platform-reqs
```

### 7.2 JavaScript Dependencies

Install JavaScript dependencies using npm:

```bash
npm install
```

---

## 8. Run Database Migrations and Seeders

> **Not needed** if you used the web installer or `php artisan khub:install`.

Run the following commands to set up the database schema and populate it with initial data:

### 8.1 Migrate the database:

```bash
php artisan migrate
```

### 8.2 Seed the database:

```bash
php artisan db:seed
```

### 8.3 Create symbolic links for file storage:

```bash
php artisan storage:link
```

### 8.4 Mark installation complete (manual path):

```bash
php artisan khub:mark-installed
```

---

## 9. Set Folder Permissions (Linux)

For Linux, ensure the correct folder permissions:

1. Set ownership for the project directory:
   ```bash
   sudo chown -R $USER:$USER /var/www/html/knowledge_hub
   ```

2. Set the correct permissions for the project:
   ```bash
   sudo chmod -R 755 /var/www/html/knowledge_hub
   sudo chmod -R 777 storage public
   ```

---

## 10. Start the Application

### For Windows (XAMPP/WAMP):

1. Open the XAMPP Control Panel and start Apache and MySQL.

2. Access the application in your browser:

   ```bash
   http://localhost/knowledge_hub
   ```

   For a **new** installation, run `composer install` first, then open **`http://localhost/knowledge_hub/install`** to complete setup.

### For Linux:

1. Start Apache:

   ```bash
   sudo service apache2 start
   ```

2. Access the application in your browser:

   ```bash
   http://{server_ip}/knowledge_hub
   ```

   For a **new** installation, run `composer install` first, then open **`http://{server_ip}/knowledge_hub/install`** to complete setup.

---

## Forum attachments: Office to PDF

Discussion **forum comments** can include file attachments. The application **normalises convertible office documents to PDF** when possible (upload path and on first access for older rows), so previews and downloads behave like PDFs.

### Required (already part of Composer install)

| Area | Role |
|------|------|
| **PHP 8.x** | Application runtime |
| **phpoffice/phpword** | Reads `.docx`; can export PDF via MPDF when LibreOffice is absent |
| **mpdf/mpdf** | PDF engine used by PhpWord’s PDF writer |
| **symfony/process** | Runs LibreOffice as a subprocess with timeouts (pulled in via Laravel) |

Run `composer install` as documented below; no extra Composer packages are required for this feature.

### Recommended server software

| Software | Purpose | Without it |
|----------|---------|------------|
| **LibreOffice** (`soffice`, headless) | Reliable conversion for **.doc**, **.docx**, **.xls**, **.xlsx**, **.ppt**, **.pptx**, **OpenDocument**, **.rtf** | `.docx` may still convert via PhpWord+MPDF (layout can differ). Other formats usually stay as originals until LibreOffice is installed. |

**Install examples**

- **Ubuntu / Debian:** `sudo apt update && sudo apt install -y libreoffice-writer libreoffice-calc libreoffice-impress` (or package `libreoffice`)
- **RHEL / Alma / Rocky:** `sudo dnf install -y libreoffice-headless`
- **macOS (development):** `brew install --cask libreoffice` — binary is often `/Applications/LibreOffice.app/Contents/MacOS/soffice`
- **Windows:** Install LibreOffice from [libreoffice.org](https://www.libreoffice.org/download/download/) and set `LIBREOFFICE_BINARY` to the full path of `soffice.exe`

**Configuration**

- Set `LIBREOFFICE_BINARY` in `.env` to the absolute path of `soffice` if auto-detection fails (`config/services.php` → `services.libreoffice.binary`).
- The **PHP / web-server user** must be allowed to **execute** that binary and **write** under `storage/app/public/uploads/forum/`.
- Conversion is capped at about **120 seconds** per file; increase PHP `max_execution_time` and proxy timeouts if needed for very large documents.

**Behaviour summary**

- **On upload:** Convertible types are written as `.pdf` in storage when conversion succeeds; the `custom_attachments` row points at the PDF.
- **Legacy rows:** Opening a preview/link hits `GET /forums/comment-attachment/{id}/pdf`, which converts once, updates the row, then redirects to the public PDF URL. If conversion fails, users still get the original file.

For a longer deployment checklist, see [docs/FORUM_ATTACHMENTS_PDF.md](docs/FORUM_ATTACHMENTS_PDF.md).

---

## REST API documentation

Interactive **OpenAPI 3** docs (try requests, schemas) are served by **L5 Swagger**:

- **Swagger UI:** `{APP_URL}/docs` (e.g. `https://your-hub.example/docs`)
- **OpenAPI JSON:** `{APP_URL}/docs/spec/api-docs.json`

Regenerate the spec after changing `@OA\` annotations in `app/`:

```bash
php artisan l5-swagger:generate
```

In production, set `L5_SWAGGER_GENERATE_ALWAYS=false` in `.env` and run the command on deploy (see `config/l5-swagger.php`).

### Notable public endpoints

| Endpoint | Summary |
|----------|---------|
| `GET /api/publications` | Records search / listing; optional `meta.filter_groups` for mobile refine UI (`include_filters`, default on). |
| `GET /api/publications/sections/recommended` | Home-style recommended feed; **paginated** (`page`, `per_page` / `page_size` / `limit`; default size **20**, max **100**). Optional Bearer (`auth.passport`) for personalization. |
| `GET /api/publications/sections/top-searches` | Top publications by visits; **paginated** (same query params; `meta.has_more`, `total`, `last_page`). |
| `GET /api/publications/sections/flagship-initiatives` | Category **10** initiatives; **paginated**, stable sort for infinite scroll. |
| `GET /api/home` | Single JSON with all three home sections; `limit` default **20**, max **48** (recommended + top searches); flagship fixed at **20** items. |
| `GET /api/health-topics` | Health topics list (matches web `/health-topics`). |
| `GET /api/health-topics/{id}` | Topic detail, publications, forums, communities. |
| `GET /api/health-emergencies` | Health emergency tags (header menu). |
| `GET /api/health-emergencies/{id}` | Emergency tag detail (same shape as topic detail). |

Authenticated routes (e.g. `POST /api/login`, `auth:api` groups) are documented in Swagger under **bearer_token** where applicable.

---

## Troubleshooting

### 1. Installer returns 403 Forbidden

The installer is locked when the application is already installed (`APP_INSTALLED=true`, lock file, or `installer_locked` in the database), or when **`vendor/` exists and the database already has data**. For an existing production deployment, run:

```bash
php artisan khub:mark-installed
```

To run the installer again on a development machine, set `APP_INSTALLED=false` in `.env`, remove `storage/app/installed.lock`, and clear `installer_locked` on the **setting** row — only do this on non-production environments.

### 2. 404 Not Found Errors:
Ensure that .htaccess files are enabled for Apache and that your virtual host configuration allows for URL rewrites.

### 3. Database Connection Issues:
Verify that the database credentials in the .env file are correct and that the MySQL/MariaDB server is running. On the installer database step, use **Skip migrations** if you are pointing at an existing populated database and only need to save credentials.

### 4. Permission Issues (Linux):
If you encounter permission errors, ensure the storage and public directories are writable by the web server:

```bash
sudo chmod -R 777 storage public
```

### 5. Prerequisites page after install (503)

If the site shows a prerequisites check page instead of the home page, verify database connectivity, that an active **setting** row exists with a site name, and that `storage/` is writable. Details are listed on the error page.

### 6. Repair Numeric Job Titles on Users
If some users have numeric IDs saved in `users.job_title` (instead of the job title text), use the maintenance command below:

Dry run first:
```bash
php artisan users:fix-job-title-ids --dry-run
```

Apply updates:
```bash
php artisan users:fix-job-title-ids
```

---

## Conclusion

You have successfully installed the Africa CDC Knowledge Hub. Refer to the Developer Guide for instructions on maintaining and extending the platform.
