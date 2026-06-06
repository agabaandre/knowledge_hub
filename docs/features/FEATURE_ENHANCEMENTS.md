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

---

## Key files (developers)

| Area | Files |
|------|--------|
| Tags AI | `app/Http/Controllers/Admin/TagsController.php`, `app/Services/ChatGPTService.php`, `app/Services/WhoFactsheetFetcher.php`, `app/Services/HealthTopicSourceFetcher.php`, `app/Support/HealthTopicSourceCatalog.php`, `app/Repositories/TagsRepository.php`, `resources/views/admin/tags/` |
| Email config | `app/Support/EmailConfig.php`, `app/Providers/AppServiceProvider.php`, `resources/views/admin/settings/index.blade.php` |
| CoP participants | `app/Repositories/CommsOfPracticeRepository.php`, `resources/views/admin/commsofpractice/participants.blade.php` |
| Forum sharing | `resources/views/forums/partials/share_buttons.blade.php`, `resources/views/forums/index.blade.php` |
| Guest create nav | `resources/views/layouts/partials/create_menu.blade.php`, `app/Http/Controllers/Auth/LoginController.php` |
