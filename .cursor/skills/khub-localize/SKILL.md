---
name: khub-localize
description: >-
  Localize Knowledge Hub UI for new forms, modules, admin pages, and frontend
  listings. Use when adding or editing Blade views, forms, modals, breadcrumbs,
  page titles, filters, empty states, or user-facing PHP strings; when the user
  mentions i18n, translation, Language Management, ui_t, locale packs, or
  English leftover copy; or before calling a feature done on admin/frontend UI.
---

# Knowledge Hub localization

Every new form, module, or page must ship with dynamic strings — never hardcoded English in blades or controllers (except English pack source files).

## Non‑negotiables

1. **No hardcoded UI chrome** in Blade/JS/controllers: titles, breadcrumbs, labels, placeholders, buttons, empty states, confirm/alerts, infinite-scroll status, SEO page titles shown to users.
2. **English is the source of truth** in `resources/lang/en/{group}.php`.
3. **All active locales** get packs: `es`, `fr`, `pt`, `ar`, `sw` (see `config/supported_locales.php`).
4. **Register new groups** in both `ui_groups` and `ui_native_groups` in `config/supported_locales.php`.
5. Prefer **`ui_t('group.key', 'English fallback')`** in blades; use **`__('group.key')`** in PHP/`@php` (never nest `{{ ui_t(...) }}` inside PHP strings).
6. Mark client-switched chrome with `class="khub-i18n-text notranslate" data-khub-i18n="group.key"` when the language switcher updates without full reload.
7. After writing packs: `UiTranslationCache::invalidateAfterSave($locale, $group)` or `flushAll()`, then `php artisan view:clear`.

## Which group to use

| Surface | Group |
|---------|--------|
| Shared Save/Cancel/Clear/Close | `general` |
| Admin sidebar / top nav / crumbs | `admin_nav` |
| Admin Configure tabs & labels | `admin_configure` |
| Admin publications / pending | `admin_publications` |
| Admin dashboard | `admin_dashboard` |
| Language Management UI | `admin_language_management` |
| Login / register chrome | `auth` |
| Frontend nav / browse categories | `frontend_nav` |
| Homepage sections & key cards | `home_sections` |
| Account / footer body chrome | `ui_body` |
| Publish wizard | `account_publish` |
| Records search | `publications` (often nested `search.*`) |
| Communities / forums / courses / FAQs / authors / health-topics / content request | `frontend_pages` |
| New large module | New group `admin_*` or `frontend_*` — do not dump into `general` |

DB-driven names (data categories, static links, themes) are edited via **Language Management** / storage overrides under `storage/app/ui_translations/{locale}/`, not only packaged PHP.

## Workflow for a new form or module

1. **Inventory** every user-visible string (blade + controller validation messages + JS confirms).
2. **Choose or create** a lang group; add flat string keys to `resources/lang/en/{group}.php`.
3. **Wire** blades with `ui_t` / `__`; in `@php` / controllers call `ui_t()` / `__()` as PHP — never Blade tags inside PHP strings.
4. **Register** the group in `config/supported_locales.php` (`ui_groups` + `ui_native_groups`).
5. **Fill locales** `es/fr/pt/ar/sw` (AI batch via `ChatGPTService::translateUiStringBatch` is OK; keep `:placeholders`).
6. **Verify** with tinker: `__('group.key', [], 'es')` and `__('group.key', [], 'fr')` are not English (unless intentional cognates).
7. **Smoke** the page under Configure default language ≠ `en`.

## Anti-patterns (reject in review)

- `'title' => 'Health Topics'` (or any UI English) in controllers without `__()` / `ui_t()`.
- `$pageTitle = '{{ ui_t(...) }}'` inside `@php`.
- Replacing only the H1 and leaving filters, empty states, modals, and JS status strings in English.
- New group missing from `ui_native_groups` (AJAX locale switch will not refresh it).
- Translating only French and leaving `es/pt/ar/sw` as English stubs.
- Overwriting Language Management storage files with English.

## Loader priority (read-only reminder)

`MergingTranslationLoader`: English → DB dynamic seeds → packaged `resources/lang/{locale}` → non-empty `storage/app/ui_translations/{locale}` (wins).

## Done checklist

- [ ] No new hardcoded UI English in changed blades/JS/controllers
- [ ] EN pack keys added
- [ ] `es` `fr` `pt` `ar` `sw` packs updated
- [ ] `ui_groups` + `ui_native_groups` updated if new group
- [ ] Cache invalidated; views cleared
- [ ] Spot-checked under non-English default locale

## Related files

- `config/supported_locales.php`
- `app/Helpers/UtilsHelper.php` (`ui_t`, `active_ui_locale`)
- `app/Translation/MergingTranslationLoader.php`
- `app/Support/UiTranslationCache.php`
- `app/Services/UiTranslationService.php` / `ChatGPTService::translateUiStringBatch`
- `resources/lang/{en,es,fr,pt,ar,sw}/`
