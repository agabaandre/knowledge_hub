# Theme guidelines

A theme restyles the public portal without code changes. Admins upload a theme as a **zip file** on **Admin → Themes**, where theme switching is managed. The same page serves these guidelines.

## Package format

The zip contains one folder (or files at the zip root):

```
my-theme.zip
└── my-theme/
    ├── theme.json        required — the manifest
    ├── theme.css         optional — stylesheet loaded after the built-in CSS
    ├── preview.png       optional — shown on the Themes admin page
    └── assets/           optional — images/fonts used by theme.css
        └── banner.jpg
```

Allowed file types: `.json .css .png .jpg .jpeg .svg .gif .webp .woff .woff2 .ttf .md .txt`. Maximum unpacked size: **10 MB**. Unsafe paths (`..`), executables and scripts are rejected — a theme is styling only and can never run code.

## theme.json

```json
{
  "slug": "nigeria-green",
  "name": "Nigeria Green",
  "version": "1.0.0",
  "description": "Nigeria country-hub colours and typography.",
  "author": "NCDC web team",
  "stylesheet": "theme.css",
  "tokens": {
    "primaryColor": "#008751",
    "secondaryColor": "#1f2937",
    "textColor": "#212529",
    "linkColor": "#008751",
    "bodyFont": "'Segoe UI', system-ui, sans-serif",
    "headingFont": "'Segoe UI', system-ui, sans-serif"
  }
}
```

| Field | Notes |
|---|---|
| `slug` | Lowercase letters, numbers, dashes. Unique per site; re-uploading the same slug replaces the theme. `classic` is reserved for the built-in look. |
| `name`, `version`, `description`, `author` | Shown on the admin Themes page. |
| `stylesheet` | CSS file inside the package (default `theme.css`). Served at `/hub-theme/{slug}/theme.css`. |
| `tokens` | Design tokens applied as CSS variables before your stylesheet loads (see below). |

## Design tokens

Tokens map onto the CSS variables the portal already uses, so most themes need **no CSS at all**:

| Token | CSS variable it sets |
|---|---|
| `primaryColor` | `--theme-color-primary` (buttons, nav accents, links) |
| `secondaryColor` | `--theme-color-secondary` |
| `textColor` | `--default-font-color` |
| `linkColor` | `--text-color-secondary` |
| `bodyFont` | `--font-family-primary` |
| `headingFont` | `--heading-font-family` |

## Writing theme.css

- The stylesheet loads **after** the built-in CSS, so plain selectors win. Avoid `!important` unless the built-in rule uses it.
- Reference packaged assets relatively: `background: url('assets/banner.jpg')` — they are served next to your stylesheet.
- Style against the stable public hooks: `.modern-header`, `.navigation`, `.nav-menu`, `.home-spotlight`, `.theme-card`, `.kh-key-section-card`, `.jbr-wrap`, `.community-pub-card`, `.dark-footer`.
- Keep pages legible at phone width; the built-in layout is responsive and your overrides must not break `@media (max-width: 991px)` behaviour.
- Dark colours on `--theme-color-primary` must keep ≥ 4.5:1 contrast with white text (WCAG AA) — the nav and buttons render white text on it.

## Lifecycle

| Action | Where |
|---|---|
| Upload / replace | Admin → Themes → *Upload theme* (zip) |
| Activate / switch | Admin → Themes → *Activate* (stored per site in `setting.site_theme`) |
| Delete | Admin → Themes (only when not active) |
| Storage | `{HUB_DATA_ROOT}/{site}/themes/{slug}/` — data, not code; survives deploys and is excluded from git |

Switching is instant for new visitors; cached manifests refresh within a minute.
