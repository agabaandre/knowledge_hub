# SEO slugs

Public URLs for catalogue records use slugs derived from the current name or title (`App\Support\SeoSlugger` and `App\Support\SeoSlugSync`).

---

## Catalogue

| Type (command key) | Source field | Typical public path |
|--------------------|--------------|---------------------|
| `themes` | `description` | `/records/theme/{slug}` |
| `subthemes` | `description` | `/records/sub-theme/{slug}` |
| `publications` | `title` (rows with `is_version = 0`) | `/records/resource/{slug}` |
| `forums` | `forum_title` | `/forums/thread/{slug}` |
| `tags` | `tag_text` | `/records/tag/{slug}`, `/health-topics/{slug}` |
| `communities` | `community_name` | `/communities/detail/{slug}` |
| `authors` | `name` | author publication URLs |
| `countries` | `name` | `/countries/details/{slug}` |
| `data-categories` | `category_name` | category browse URLs |
| `subject-areas` | `name` | subject-area URLs |

---

## When slugs update

`SeoSlugSync::apply()` runs on save in the relevant repositories when:

- the slug is empty, or
- the source name/title **changed**

Unchanged names keep their existing slug. Publication versions (`is_version = 1`) are not given a new public slug.

---

## Artisan

```bash
php artisan slugs:regenerate
php artisan slugs:regenerate themes,subthemes,publications
php artisan slugs:regenerate --dry-run
php artisan slugs:regenerate --only-empty
```

| Argument / option | Effect |
|-------------------|--------|
| `type` (default `all`) | Comma-separated keys from the table above, or `all` |
| `--only-empty` | Fill blank slugs only; do not rewrite existing URLs |
| `--dry-run` | Count would-be updates without saving |

A full regenerate **changes public URLs**. Prefer `--only-empty` on a live site unless you intend to replace old links.

---

## Key files

| Path | Role |
|------|------|
| `app/Support/SeoSlugSync.php` | Catalogue, apply-on-save, bulk regenerate |
| `app/Support/SeoSlugger.php` | Per-type slug strings |
| `app/Console/Commands/RegenerateSeoSlugsCommand.php` | `slugs:regenerate` |
| `tests/Unit/SeoSlugSyncTest.php` | Rename + command coverage |
