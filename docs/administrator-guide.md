# Knowledge Hub administrator guide

For hub administrators, content managers, and operators. End-user tasks are in the [user guide](/user_manual).

---

## 1. Admin home and dashboards

| URL | What it shows |
|-----|----------------|
| `/admin/dashboard` | **Overview** — counts (publications, authors, forums, users, visits, administrative units on country hubs) and recent activity |
| `/admin/dashboard/list` | **View all dashboards** — admin-only dashboard publications and embedded dashboards |

On country hubs, `/admin/dashboard` is the stats home even when no custom dashboards exist. Use **View all dashboards** for the list of dashboard publications.

The Nifty admin sidebar always shows Font Awesome 6 solid icons (`font-weight: 900`). The public-site “menu icons” setting does not hide them. If icons vanish after a theme change, check `resources/views/layouts/theme1/partials/theme1_colors.blade.php` (`#mainnav-container`). The admin layout always includes `menu-icons-enabled`.

![Admin dashboard](/manual/administrator-guide/01-dashboard.png)
*Admin overview — counts and recent activity.*

![Dashboard publications](/manual/administrator-guide/02-dashboard-list.png)
*View all dashboards — admin-only dashboard publications.*

---

## 2. Approvals inbox

**Path:** `/admin/approvals`

One queue for publications, forums, community-of-practice participant requests, and federated partner content staged after sync.

Notification emails (new item and daily summary) link here with a type filter (`?type=publication|forum|cop_participant|federated`).

Approve or reject one item or a selection. Include a reason when rejecting. Specialist list pages under Publish / Forums / CoPs still exist; the inbox is the default landing place for reviewers.

Scheduled: `php artisan approvals:daily-summary` at **08:00**.

Permissions: `moderate_publication`, `moderate_forum`, `moderate_cop_participants`. Federated items are included for publication or forum moderators.

See [features/APPROVALS.md](features/APPROVALS.md).

![Approvals inbox](/manual/administrator-guide/03-approvals.png)
*Approvals inbox — review pending publications, forums, participants, and federated content.*

---

## 3. Publishing and metadata

- Contributors publish from `/account/publish` or Admin → Publish.
- Office→PDF conversion: enable in **Configure** and install LibreOffice on the server.
- Restricted categories require the matching permission; the UI only offers categories the user may use.
- Health emergencies appear in the main menu only when emergency tags exist.

![Admin publications](/manual/administrator-guide/04-publications.png)
*Admin publications list.*

![Create publication](/manual/administrator-guide/05-publication-create.png)
*Admin publish form — metadata, files, and categories.*

![Pending publications](/manual/administrator-guide/06-publications-pending.png)
*Pending publications — specialist queue (the Approvals inbox is the default landing place).*

### SEO slugs

Public URLs for themes, sub-themes, publications, forums, tags, communities, authors, countries, data categories, and subject areas use slugs generated from the current name/title.

- Saving a record **regenerates the slug when that name/title changes**.
- Existing slugs stay if the name did not change.
- Publication versions (`is_version = 1`) do not get a new public slug.

```bash
php artisan slugs:regenerate
php artisan slugs:regenerate themes,subthemes,publications
php artisan slugs:regenerate --dry-run
php artisan slugs:regenerate --only-empty
```

`--only-empty` fills blank slugs only. A full run **changes public URLs** to match current titles.

See [features/SEO_SLUGS.md](features/SEO_SLUGS.md).

---

## 4. Administrative units (country hubs)

When member-state browsing is off, the public map uses **administrative units**.

- Manage units under dropdown lists → Administrative units.
- Units are validated against the country table; icons and logos display on public cards.
- The public detail page (`/adminunits/details?id=…`) lists child units and publications with the **same feed cards as records search** (styles, preview modal, PDF chat). Missing units return 404.

![Administrative units](/manual/administrator-guide/07-adminunits.png)
*Administrative units — manage units, icons, and logos.*

---

## 5. Federation

**Admin → Federated Knowledge Hubs** (`/admin/federated-hubs`)

| Task | Where |
|------|--------|
| Provider API and token | Provider API tab; generate/copy token from Advanced settings |
| Connect to continental hub | Central hub connection (country hubs) |
| Register peers and sync | Remote hubs |
| Review staged partner content | Approvals inbox (federated) or `/admin/federated-content/pending` |
| Provision a country instance | Continental only, when `FEDERATION_PROVISION_ENABLED=true` |

Public browse: `/federated` — partner carousel above listings, compact covers (~320px), longer excerpts (~140 words).

![Federated Knowledge Hubs](/manual/administrator-guide/08-federated-hubs.png)
*Federated Knowledge Hubs — provider API, remote hubs, and sync.*

![Pending federated content](/manual/administrator-guide/09-federated-pending.png)
*Pending federated content — staged partner publications and forums.*

```bash
php artisan federation:sync
php artisan federation:sync --all
php artisan federation:sync --hub=1
php artisan federation:refresh-central-token
```

Full reference: [features/FEDERATION.md](features/FEDERATION.md).

---

## 6. Configure, users, and storage

- **Configure** (`/admin/configure`): branding, mail (Exchange or SMTP; `.env` wins over the database), social login, AI, search, auto-approve, hub deployment type.
- **Users / roles / permissions**: Spatie RBAC. Grant moderation permissions only to reviewers.
- **Storage Management** (`/admin/storage-management`): drivers, host paths, SQL backups, file browser. See [deployment/STORAGE.md](deployment/STORAGE.md).

![Configure](/manual/administrator-guide/10-configure.png)
*Configure — branding, mail, social login, AI, search, and hub type.*

![Storage Management](/manual/administrator-guide/11-storage.png)
*Storage Management — drivers, backups, and file browser.*

![Users](/manual/administrator-guide/12-users.png)
*Users — accounts and role assignment.*

![Roles](/manual/administrator-guide/13-roles.png)
*Roles — Spatie RBAC role list.*

![Permissions](/manual/administrator-guide/14-permissions.png)
*Permissions — grant `moderate_publication`, `moderate_forum`, and `moderate_cop_participants` only to reviewers.*

![Events](/manual/administrator-guide/15-events.png)
*Admin events — public event pages and scheduling.*

```bash
php artisan hub:link-storage
./fix-storage-permissions.sh
```

---

## 7. Search index and sitemap

When Meilisearch is enabled:

```bash
php artisan scout:import "App\Models\Publication"
php artisan scout:sync-index-settings
php artisan sitemap:generate --warm-cache
```

The scheduler reimports publications daily at 03:45, syncs index settings weekly, and regenerates the sitemap at 04:00.

---

## 8. Useful Artisan commands

| Command | Purpose |
|---------|---------|
| `php artisan slugs:regenerate` | Rebuild SEO slugs (`--only-empty`, `--dry-run`, optional type list) |
| `php artisan approvals:daily-summary` | Email pending-approval digest |
| `php artisan federation:sync` | Pull public content from country hubs |
| `php artisan federation:refresh-central-token` | Refresh OAuth token to the continental hub |
| `php artisan hub:backup-database` | SQL backup (also scheduled when enabled in Storage) |
| `php artisan mailing:weekly-digest` | Subscriber digest (Mondays 09:00) |
| `php artisan rss:fetch` | RSS ingest (Tuesdays 03:00) |

Ensure cron runs `php artisan schedule:run` every minute. On the host, `DB_HOST=mysql` is remapped to `127.0.0.1` (use `DB_PORT_FORWARD` if Compose publishes MySQL on another port). Prefer `docker compose exec app php artisan …`. See [deployment/README.md](deployment/README.md).

---

## Related documentation

| Document | Topic |
|----------|--------|
| [user-guide.md](user-guide.md) | Public portal and contributor tasks |
| [README.md](README.md) | Full documentation index |
| [features/APPROVALS.md](features/APPROVALS.md) | Approvals inbox (technical) |
| [features/SEO_SLUGS.md](features/SEO_SLUGS.md) | Slug catalogue and command |
| [features/FEDERATION.md](features/FEDERATION.md) | Federation API and provision |
| [deployment/STORAGE.md](deployment/STORAGE.md) | Files and backups |
