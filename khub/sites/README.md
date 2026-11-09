# Sites

One folder per hub instance. A site folder holds **configuration and customisation only** — never uploads, backups or any other data. Data lives under `HUB_DATA_ROOT/{storageId}` (default `/var/khubdata/{storageId}`), exactly as in the Laravel hub.

```
sites/
  default/
    site.json          ← required
    overrides/         ← optional: css/custom.css, locales/<lang>.json
  nigeria/
    site.json
```

## site.json

| Key | Meaning |
|-----|---------|
| `enabled` | `false` ignores the folder entirely: no routes, no DB connection, no storage folders. The `nigeria` example ships disabled; set `true` once its database exists. |
| `name` | Fallback site name until the `setting` table is read |
| `pathPrefix` | URL prefix for path-based resolution (`nigeria` → `khub.org/nigeria`) |
| `hosts` | Hostnames for host-based resolution (`SITE_RESOLUTION=host`) |
| `storageId` | Folder under `HUB_DATA_ROOT`; derived from `APP_URL` + `pathPrefix` when empty |
| `database` | Dedicated knex connection (`client`: `mysql2` or `pg`). `null` = global `DB_*` env. Values like `${VAR}` are read from the environment. |
| `modules` | Default on/off per module. Admins can override from the backend (`PUT /api/admin/modules/:name`); DB overrides win. |
| `branding` | Fallback colours/logo before the database settings load |
| `locales` | Languages offered in the switcher; first is default |
| `features` | Non-module switches: `statesEnabled`, `adminUnitsEnabled`, `seoFriendlyUrls` |

Adding a site = add a folder with a `site.json`, create its database, run `npm run migrate -- --site=<id>`, restart.
