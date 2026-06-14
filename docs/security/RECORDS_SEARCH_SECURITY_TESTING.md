# Records search security testing

Smoke tests and automated checks for SQL injection, filter validation, and error disclosure on the **records search** endpoints (`/records/search`, `/records/search/fragment`).

## Scope

| Input | Handling |
|-------|----------|
| `term` | Bound parameters in `PublicationSearchQuery` (LIKE); not concatenated into raw SQL |
| Integer filters (`country_id`, `author_id`, `tag`, `thematic_area_id`, …) | Laravel `integer` validation or custom numeric checks in `validateRecordsSearchRequest()` |
| `rcc` (region) | Custom rule: `all`, empty, or positive integer only |
| Category / file-type filters | Custom numeric validation (arrays or scalar) |

Invalid filter values on the **fragment** endpoint (AJAX) return **422 JSON**. Invalid values on the **full page** search redirect back (**302**) with validation errors (web form flow).

## PHPUnit (automated)

```bash
php artisan test tests/Feature/RecordsSearchSecurityTest.php
```

Tests cover:

- Fragment endpoint rejects SQL-style probes on integer filters (422 + validation errors)
- Fragment endpoint accepts `term` SQL probes without leaking `SQLSTATE` / PDO / QueryException
- Full-page search redirects (302) on invalid `country_id` and `rcc` probes

**Note:** When `APP_URL` includes a subdirectory (e.g. `http://localhost/knowledge_hub/`), PHPUnit sets `URL::forceRootUrl('http://localhost')` in the test `setUp()` so routes resolve correctly. `phpunit.xml` also sets `APP_URL=http://localhost`.

## Shell smoke script (production / staging / local)

Uses a browser **User-Agent** so BotProtection does not block `curl`.

```bash
./scripts/security-test-records-search.sh
./scripts/security-test-records-search.sh https://khub.africacdc.org
./scripts/security-test-records-search.sh http://localhost/knowledge_hub
```

The script checks HTTP status, response size, and scans the body for database error strings (`SQLSTATE`, `PDOException`, `QueryException`). The **fragment** probe sends `Accept: application/json`. **Admin** probe does not follow redirects (expects 302 to login).

### Production results — 2026-06-14 18:33 UTC

Target: `https://khub.africacdc.org`

| Probe | Result | Time | Body size |
|-------|--------|------|-----------|
| `baseline_home` | OK (200) | 2.28s | 595964 bytes |
| `search_empty` | OK (200) | 9.66s | 627636 bytes |
| `term_sql_or` | OK (200) | 2.63s | 481610 bytes |
| `term_union` | OK (200) | 8.24s | 482008 bytes |
| `country_sqli` | OK (200) | 8.40s | 595964 bytes |
| `author_sqli` | OK (200) | 2.53s | 595964 bytes |
| `file_type_sqli` | OK (200) | 2.46s | 595964 bytes |
| `thematic_sqli` | OK (200) | 2.92s | 595964 bytes |
| `tag_sqli` | OK (200) | 7.02s | 628783 bytes |
| `rcc_sqli` | OK (200) | 3.62s | 595964 bytes |
| `rcc_all` | OK (200) | 2.24s | 623106 bytes |
| `country_invalid` | OK (200) | 8.02s | 595964 bytes |
| `data_cat_invalid` | OK (200) | 4.16s | 595964 bytes |
| `term_xss` | OK (200) | 4.78s | 481336 bytes |
| `fragment_sqli` | REJECTED (422) | 1.82s | 104 bytes |
| `admin_unauth` | REDIRECT (302) | 0.79s | 104 bytes |
| `path_traversal` | OK (200) | 1.93s | 481598 bytes |

**Interpretation**

- No probe response contained exploitable SQL error text in the HTML/JSON body.
- `fragment_sqli` returned **422** with JSON validation errors (expected).
- Filter SQL probes on the full-page search return **200** after redirect follow (`-L`): validation rejects invalid integers with **302** first, then the search page loads without executing injection.
- `admin_unauth` returns **302** to login when redirects are not followed.
- Automated scans may flag `sqlstate` inside embedded JSON/map data; the script filters for actual exception patterns only.

### Localhost results — 2026-06-14 18:27 UTC

Target: `http://localhost/knowledge_hub`

After a burst of requests, BotProtection returned **429** for several probes. Re-run with delays or from PHPUnit for local validation. PHPUnit tests passed against the local app database.

## PHPUnit results — 2026-06-14

```
PASS  Tests\Feature\RecordsSearchSecurityTest
✓ rejects invalid country id on fragment
✓ rejects sql injection style country id on fragment
✓ rejects sql injection style rcc on fragment
✓ rejects invalid author id on fragment
✓ rejects invalid file type id on fragment
✓ rejects invalid tag on fragment
✓ term sql probe does not leak database errors on fragment
✓ rejects invalid country id on full search
✓ rejects sql injection style rcc on full search

Tests:  9 passed
Time:   0.90s
```

## Related code

- `app/Http/Controllers/PublicationsController.php` — `validateRecordsSearchRequest()`
- `scripts/security-test-records-search.sh` — external smoke tests
- `tests/Feature/RecordsSearchSecurityTest.php` — automated regression tests
