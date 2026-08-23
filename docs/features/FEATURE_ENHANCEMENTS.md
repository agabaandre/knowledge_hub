# Feature enhancements

This document summarizes recent platform enhancements for **administrators**, **contributors**, and **operators**. It complements the [installation guide](../installation/README.md) and the [documentation index](../README.md).

---

## Admin — Health topics (tags)

**Path:** `/admin/tags`

### Tag field length

- Tag names (`tag_text`) support up to **255 characters** (was 20).
- Create/edit modals and validation enforce `max:255`.

**Migration:** `2026_05_20_130000_widen_tag_text_on_tags_table.php`

### AI health topic generation

Button-driven tools (no automatic runs on page load):

| Action | Route | Description |
|--------|-------|-------------|
| Generate with AI | `POST /admin/tags/ai-generate` | Creates new topic names + HTML overviews in batches |
| Import selected | `POST /admin/tags/ai-import` | Saves previewed topics; skips duplicates |
| Deduplicate tags | `POST /admin/tags/deduplicate` | Merges case-insensitive duplicate names and remaps usages |

**Reference sources** (checkboxes in the AI panel):

- [WHO Health Topics](https://www.who.int/health-topics)
- [CDC Health Topics (A–Z)](https://www.cdc.gov/health-topics.html)
- MedlinePlus, UC Berkeley UHS, Dartmouth

Topics are deduplicated across sources and against existing tags before and after AI generation. Default import flags: **Health Topic = Yes**, **Health Emergency = No**.

**Configuration:**

```env
OPEN_API_KEY=sk-...
OPENAI_MODEL=gpt-3.5-turbo
```

Uses the same OpenAI chat-completions pattern as forum summarisation (moderate `max_tokens`, JSON responses with fallback).

### Rich HTML descriptions (WHO factsheets)

Health topic **overviews** are generated as structured HTML:

- Sections: Overview, Key facts, Symptoms, Prevention, Africa relevance
- `<h3>` headings, paragraphs, lists
- **References** block with links to WHO fact sheets and health topic pages

| Action | Route | Description |
|--------|-------|-------------|
| Fill missing descriptions (WHO) | `POST /admin/tags/ai-describe` | Batch (up to 5) for tags with missing/short overviews |
| WHO describe / Enhance (per row) | `POST /admin/tags/ai-describe` with `tag_id` | Single-tag preview from WHO content |
| Apply selected updates | `POST /admin/tags/ai-apply-overviews` | Saves previews; by default only when new text is **longer** |

**WHO sourcing:** `App\Services\WhoFactsheetFetcher` fetches [WHO fact sheets](https://www.who.int/news-room/fact-sheets/detail/) and health topic pages, extracts section text, and passes it to OpenAI for hub-ready HTML (no invented statistics).

**Description status** on the tags table: **Missing** (&lt; 120 plain-text chars), **Short**, or **OK**.

---

## Admin — Configure (settings)

**Path:** `/admin/configure`

### Layout

- **Save** and **Backup & maintenance** (export, import, clear cache) grouped in a clearer action bar.
- **Branding** section redesigned (logo/favicon cards, display options grouped with branding).
- Import form moved outside the main settings form so maintenance actions do not conflict with Save.

### Email tab

New **Email** settings tab:

| Driver | Use case |
|--------|----------|
| **Exchange** (default) | Microsoft 365 / Exchange Online (tenant, client ID/secret, etc.) |
| **SMTP** | Generic SMTP host, port, encryption, credentials |

**Priority:** Values set in **`.env`** take precedence over the database when present. Runtime mail config is applied at boot via `App\Support\EmailConfig`.

**Migration:** `2026_05_19_160000_add_email_settings_to_setting_table.php`

After deploy:

```bash
php artisan migrate
```

---

## Admin — Communities of practice (participants)

**Path:** `/admin/commsofpractice/participants`

- **Communities** column shows a **numbered list** (one community per line).
- First **5** communities visible in the table; additional communities available via **“+ N more”** modal per member.

---

## Admin — Communities of practice (listing & create)

**Path:** `/admin/commsofpractice`

### DataTables

- Community listing and participant tables return JSON when `datatable=1` is passed (no longer requires a strict AJAX-only request), fixing DataTables load errors on admin pages.

### Create / edit modal

- Modal title reads **Create Community** (or edit when updating).
- **Region** and **Member state (country)** fields use chained Select2: choosing a region filters the country dropdown.

**Key files:** `app/Http/Controllers/Admin/CommsOfPracticeController.php`, `resources/views/admin/commsofpractice/index.blade.php`

---

## Frontend — Navigation & account

### Create menu (guests)

- **Create** is visible to guests in the main navigation.
- Choosing **Forum Discussion** or **Resource Publication** redirects to login with a contextual message and return URL.

### My Forums — share from the list

**Path:** `/account/my-forums` (uses the forums list layout with the user’s discussions)

Share controls on **each forum card** without opening the thread:

- LinkedIn, X (Twitter), Facebook, WhatsApp
- **Copy link**

Implemented in `resources/views/forums/partials/share_buttons.blade.php` (also used on forum detail actions).

### My forum posts

**Path:** `/account/my-discussions`

Separate page for threads **authored** by the user (all moderation states), with edit/resubmit for pending or rejected posts.

---

## Public — Health topics

**Paths:** `/health-topics`, `/health-topics/{slug}`

- Topic **overviews** render as HTML on the topic detail page (`overview-text-container`).
- SEO meta description uses plain text derived from the overview when present.

---

## Frontend — Communities of practice (detail)

**Path:** `/communities/detail/{slug}` (e.g. `/communities/detail/africa-cdc-staff`)

Member-facing community hub with tabbed activity, an enhanced sidebar, and publication cards styled like forum posts.

### Activity tabs (no full page reload)

Four tabs, backed by **React 18 (CDN)** and `public/assets/js/community-detail-react.js`:

| Tab | Content |
|-----|---------|
| **Wall posts** | Community wall comments, compose form, likes/replies |
| **Publications** | Linked resources with forum-style cards |
| **Forums** | Discussions linked to the community |
| **Processed requests** | Content requests referred to this CoP and marked complete |

**Behaviour:**

- Switching tabs shows/hides server-rendered panels instantly — **no full page reload**.
- The URL updates with `?tab=wall|publications|forums|processed` via `history.pushState` (browser back/forward supported).
- Default tab is **Wall** when there are approved wall posts in the **last 7 days**; otherwise **Publications**.
- Hero **Post on community wall** switches to the Wall tab and opens the compose form without navigating away.

**Pagination** inside a tab (e.g. publication page 2) still uses normal links.

### Publication cards (Publications tab)

Forum-inspired layout per linked resource:

- Cover image, title, description, theme/sub-theme metadata
- **Associated authors** in the metadata block only (not under the poster name)
- **Posted by** footer shows the contributor’s **account organisation** (`users.organization_name`), matching `/account` — not `author_affiliation` from the publication record
- **Attachments** in a collapsible **Show attachments (N)** panel (preview/download when expanded)
- Inline comments, share buttons, favourite / read more / Khub AI actions
- View counts and engagement stats

### Sidebar

- **Recent forums**, **Upcoming events**, **Community members** (search + infinite scroll), **Contribution badges** — Blade-rendered cards
- **My other communities** — React card list with initials avatar, description snippet, stat pills (pubs / forums / members), hover affordances

### Visual design

- Consistent **4px** corner radius on detail UI via CSS variable `--community-ui-radius: 4px`
- Circular avatars and pill stat badges remain fully rounded

### Contributor organisation helper

`contributor_profile_organization()` in `app/Helpers/UtilsHelper.php` reads **only** the linked user’s `organization_name` (account profile). It no longer falls back to the latest publication’s `author_affiliation`.

Used on:

- Community publication card footers
- Author profile header (`/authors/publications/{slug}`)

### Key files

| Area | Files |
|------|--------|
| Detail page | `resources/views/communities/detail.blade.php` |
| Styles | `resources/views/communities/partials/detail_styles.blade.php` |
| Sidebar | `resources/views/communities/partials/detail_sidebar.blade.php` |
| Publication card | `resources/views/communities/partials/publication_card.blade.php`, `publication_attachments_strip.blade.php`, `publication_card_scripts.blade.php` |
| Wall | `resources/views/communities/partials/community_comments.blade.php`, `community_comment_scripts.blade.php` |
| React tabs & other communities | `public/assets/js/community-detail-react.js` |
| Controller | `app/Http/Controllers/CommunitiesController.php` |
| Org helper | `app/Helpers/UtilsHelper.php` → `contributor_profile_organization()` |

### CDN dependencies (community detail only)

Loaded on member community detail pages:

- `react@18` / `react-dom@18` (unpkg UMD builds)
- `assets/js/community-detail-react.js`

Config is injected as `window.communityDetailReactConfig` from the Blade view.

---

## Approvals, federation browse, admin units, and slugs (August 2026)

- **Approvals inbox** (`/admin/approvals`) — one queue for publications, forums, CoP participants, and federated items. Notification emails link here. Daily digest: `php artisan approvals:daily-summary` at 08:00. See [APPROVALS.md](APPROVALS.md).
- **Admin dashboard** — `/admin/dashboard` is the stats overview; `/admin/dashboard/list` lists dashboard publications (works on empty country hubs).
- **Federation browse** (`/federated`) — partner-hub carousel above listings, compact covers (~320px), longer excerpts (~140 words). See [FEDERATION.md](FEDERATION.md).
- **Admin unit detail** — publication feed cards match records search (preview, download, AI).
- **Nifty sidebar icons** — Font Awesome 6 solid at weight 900; admin layout always enables menu icons.
- **SEO slugs** — regenerated when titles/names change; `php artisan slugs:regenerate` (`--only-empty`, `--dry-run`). See [SEO_SLUGS.md](SEO_SLUGS.md).
- **User guide** — in-app at `/user_manual` (source: `docs/user-guide.md`). Administrator guide at `/administrator-guide`.

---

## Operations checklist (after pull)

1. Run migrations:

   ```bash
   php artisan migrate
   ```

2. Ensure AI tags / WHO descriptions:

   ```env
   OPEN_API_KEY=...
   OPENAI_MODEL=gpt-3.5-turbo
   ```

3. Optional email overrides in `.env` (override DB configure tab).

4. Clear config/cache if settings were changed outside the UI:

   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

---

## Related documentation

| Document | Topic |
|----------|--------|
| [../README.md](../README.md) | Documentation index |
| [../installation/README.md](../installation/README.md) | Installation overview |
| [../deployment/DOCKER.md](../deployment/DOCKER.md) | Container deployment |
| [../deployment/STORAGE.md](../deployment/STORAGE.md) | File storage & backups |
| [CONTENT_REQUEST_REFERRALS.md](CONTENT_REQUEST_REFERRALS.md) | Content request referrals |
| [FORUM_ATTACHMENTS_PDF.md](FORUM_ATTACHMENTS_PDF.md) | Forum Office → PDF |
| [KPI_INDICATORS_OWID.md](KPI_INDICATORS_OWID.md) | KPI / OWID indicators |
| [APPROVALS.md](APPROVALS.md) | Approvals inbox |
| [SEO_SLUGS.md](SEO_SLUGS.md) | SEO slug sync |
| [FEDERATION.md](FEDERATION.md) | Federated hubs |
| [../user-guide.md](../user-guide.md) | End-user guide |
| [../administrator-guide.md](../administrator-guide.md) | Administrator guide |

---

## Key files (developers)

| Area | Files |
|------|--------|
| Tags AI | `app/Http/Controllers/Admin/TagsController.php`, `app/Services/ChatGPTService.php`, `app/Services/WhoFactsheetFetcher.php`, `app/Services/HealthTopicSourceFetcher.php`, `app/Support/HealthTopicSourceCatalog.php`, `app/Repositories/TagsRepository.php`, `resources/views/admin/tags/` |
| Email config | `app/Support/EmailConfig.php`, `app/Providers/AppServiceProvider.php`, `resources/views/admin/settings/index.blade.php` |
| CoP participants | `app/Repositories/CommsOfPracticeRepository.php`, `resources/views/admin/commsofpractice/participants.blade.php` |
| CoP admin listing | `app/Http/Controllers/Admin/CommsOfPracticeController.php`, `resources/views/admin/commsofpractice/index.blade.php` |
| Community detail | `app/Http/Controllers/CommunitiesController.php`, `resources/views/communities/detail.blade.php`, `public/assets/js/community-detail-react.js` |
| Contributor org | `app/Helpers/UtilsHelper.php` → `contributor_profile_organization()` |
| Forum sharing | `resources/views/forums/partials/share_buttons.blade.php`, `resources/views/forums/index.blade.php` |
| Guest create nav | `resources/views/layouts/partials/create_menu.blade.php`, `app/Http/Controllers/Auth/LoginController.php` |
| Approvals inbox | `app/Http/Controllers/Admin/ApprovalsController.php`, `app/Services/ApprovalInboxService.php` |
| SEO slugs | `app/Support/SeoSlugSync.php`, `app/Console/Commands/RegenerateSeoSlugsCommand.php` |
| Admin unit cards | `resources/views/adminunits/details.blade.php` |
| Nifty sidebar icons | `resources/views/layouts/theme1/partials/theme1_colors.blade.php`, `resources/views/admin/layouts/main_nifty.blade.php` |
