# Publication citation indexing & tracking (Google Scholar–ready)

**Date:** 2026-09-09  
**Status:** Approved in chat; awaiting spec file review  
**Scope:** Public publication detail pages (`resources/views/publications/show.blade.php` and related support code)

## Problem

Knowledge Hub publication pages already expose Schema.org `ScholarlyArticle` JSON-LD and DOI links, but they lack:

1. Highwire Press `citation_*` HTML meta tags that [Google Scholar’s inclusion guidelines](https://scholar.google.com/intl/en/scholar/inclusion.html) require for reliable indexing.
2. A clear, lawful “Track citations” surface that sends readers to established citation indexes without scraping or fabricating citation counts.

## Goals

- Optimise publication landing pages for Google Scholar and similar scholarly crawlers.
- Give readers outbound links to citation indexes (Lancet-style “find citations elsewhere” pattern).
- Stay industry-standard and lawful: no Google Scholar scraping; no undocumented APIs; no false “Cited by N” claims.

## Non-goals

- Live citation counts from Google Scholar (ToS / fragile).
- Paid Dimensions/Web of Science API integrations.
- Changing the publish wizard or requiring DOI for all records.
- Sitemap or robots.txt redesign (only ensure publication URLs remain crawlable as today).

## Approach (approved)

**Indexing meta tags + outbound lookup links.**

| Piece | Behaviour |
| --- | --- |
| Google Scholar meta | Emit Highwire `citation_*` tags in `<head>` when title + ≥1 author exist |
| Schema.org | Keep / align existing `ScholarlyArticle` JSON-LD |
| Track citations UI | Outbound links only; prefer DOI; else title+author query where the service supports it |
| Counts | Do **not** display scraped or estimated counts |

### Citation meta tags (when data present)

Required for Scholar inclusion when possible:

- `citation_title`
- `citation_author` (repeat once per author; “Last, First” when parseable, else full name)
- `citation_publication_date` (prefer `year_published` as `YYYY`, or content/created date as `YYYY/MM/DD`)
- `citation_abstract_html_url` (canonical publication URL)

Optional when available:

- `citation_doi` (bare DOI or `10.…` form; must match stored DOI)
- `citation_issn`, `citation_isbn`
- `citation_journal_title`, `citation_volume`, `citation_issue`, `citation_firstpage` / `citation_lastpage` (parse from `journal_pages` when possible)
- `citation_publisher`
- `citation_pdf_url` (absolute URL of the main public PDF only when openly accessible)

Do not emit empty tags.

### Outbound citation sources (no complex integration)

Show a **Track citations** block on the publication page when at least one reliable link can be built:

| Source | Link strategy | Notes |
| --- | --- | --- |
| Google Scholar | DOI → `scholar.google.com/scholar?q=10.…` or title query | Official public search URL |
| Crossref | DOI → `https://search.crossref.org/?q={doi}` | Open registry |
| Semantic Scholar | DOI → `https://www.semanticscholar.org/search?q={doi}` | Public search |
| OpenAlex | DOI → `https://openalex.org/works/doi:{doi}` | Open scholarly graph |
| Dimensions | DOI → `https://app.dimensions.ai/discover/publication?search_text={doi}` | Public discover URL |
| DOI.org | `https://doi.org/{doi}` | Canonical resolver (already partly shown) |

Rules:

- Prefer DOI-based URLs.
- If no DOI: still offer Google Scholar title search when title exists; omit DOI-only services.
- Open in new tab with `rel="noopener noreferrer"`.
- Label as external indexes; copy must not claim “Cited by N on this hub”.

### Lawful / compliance boundary

- No automated harvesting of Google Scholar HTML or CAPTCHA bypass.
- No storing third-party citation counts without a licensed/open API (future optional work via OpenAlex only if explicitly requested).
- Meta tags and deep links use publicly documented URL patterns and publisher-owned page metadata only.

## Implementation sketch

1. **`App\Support\PublicationCitationMeta`** (or extend `PublicationSeo`)  
   - `highwireTags(Publication): array<string, list<string>>`  
   - `citationLinks(Publication): list<{id,label,url,available}>`  
   - Shared author/DOI/date/PDF helpers reused by JSON-LD and meta.

2. **Blade**  
   - Include Highwire tags from publication show head (via existing SEO partial or a dedicated `@push('meta')` / section used by the layout).  
   - Render “Track citations” near existing DOI metadata.

3. **Tests**  
   - Unit: tag set for DOI + authors; omit empty tags; PDF URL absolute; links prefer DOI.  
   - View/string tests: show template references citation meta + Track citations.

4. **Ops note**  
   - Indexing is not instantaneous; Scholar must crawl the public URL. Ensure production pages are publicly reachable (no login wall for the abstract HTML).

## Success criteria

- View-source on a public publication with DOI shows `citation_title`, `citation_author`, `citation_publication_date`, `citation_doi`, `citation_abstract_html_url`.
- Page shows a Track citations section with working external links.
- No citation counts displayed unless a future open API feature is separately approved.
- Existing Schema.org graph remains valid.

## Out of scope follow-ups (optional later)

- OpenAlex open API for approximate citation counts (with attribution).
- ORCID enrichment for `citation_author_orcid`.
- Admin “Scholar readiness” checklist on the publication edit form.
