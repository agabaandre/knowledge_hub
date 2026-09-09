# Publication citation indexing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Emit Google Scholar Highwire `citation_*` meta tags on publication detail pages and a lawful outbound “Track citations” link block (no scraped counts).

**Architecture:** A pure support class builds tag maps and link lists from a publication-like object. The show view assigns those into view data; the shared SEO meta partial prints Highwire tags in `<head>`; the Resource Details card renders the Track citations UI near DOI.

**Tech Stack:** Laravel Blade, PHPUnit unit tests, existing helpers (`publication_url`, `clean_unicode`).

## Global Constraints

- No Google Scholar scraping; no fabricated “Cited by N” counts.
- Prefer DOI-based outbound URLs; without DOI only Google Scholar title search.
- Do not emit empty Highwire tags; require title + ≥1 author before emitting any tags.
- Leave Docker leftovers (`config/database.php`, `DockerServiceHost*`, `.DS_Store`) uncommitted.
- Spec: `docs/superpowers/specs/2026-09-09-publication-citation-indexing-design.md`

---

### Task 1: `PublicationCitationMeta` support class

**Files:**
- Create: `app/Support/PublicationCitationMeta.php`
- Test: `tests/Unit/PublicationCitationMetaTest.php`

**Interfaces:**
- Produces: `PublicationCitationMeta::highwireTags(object $publication): array` (ordered list of `['name' => string, 'content' => string]`); `PublicationCitationMeta::citationLinks(object $publication): array` (list of `['id','label','url']`); helpers for DOI bare form, authors, date, pages.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Unit;

use App\Support\PublicationCitationMeta;
use Tests\TestCase;

class PublicationCitationMetaTest extends TestCase
{
    public function test_highwire_tags_include_required_fields_and_doi(): void
    {
        $pub = (object) [
            'title' => 'Malaria surveillance in East Africa',
            'associated_authors' => 'Ada Lovelace, Alan Turing',
            'author' => (object) ['name' => 'Africa CDC'],
            'year_published' => 2024,
            'doi' => 'https://doi.org/10.1234/abcd',
            'issn' => '1234-5678',
            'isbn' => '',
            'journal_name' => 'Lancet Global Health',
            'journal_volume' => '12',
            'journal_issue' => '3',
            'journal_pages' => '45-52',
            'publisher' => 'Elsevier',
            'publication_pdf_url' => 'https://example.test/file.pdf',
            'id' => 1,
            'seo_url' => 'malaria-surveillance',
            'created_at' => null,
        ];

        $tags = PublicationCitationMeta::highwireTags($pub);
        $byName = collect($tags)->groupBy('name')->map(fn ($g) => $g->pluck('content')->all());

        $this->assertSame(['Malaria surveillance in East Africa'], $byName['citation_title']);
        $this->assertContains('Lovelace, Ada', $byName['citation_author']);
        $this->assertContains('Turing, Alan', $byName['citation_author']);
        $this->assertContains('Africa CDC', $byName['citation_author']);
        $this->assertSame(['2024'], $byName['citation_publication_date']);
        $this->assertSame(['10.1234/abcd'], $byName['citation_doi']);
        $this->assertSame(['1234-5678'], $byName['citation_issn']);
        $this->assertSame(['Lancet Global Health'], $byName['citation_journal_title']);
        $this->assertSame(['12'], $byName['citation_volume']);
        $this->assertSame(['3'], $byName['citation_issue']);
        $this->assertSame(['45'], $byName['citation_firstpage']);
        $this->assertSame(['52'], $byName['citation_lastpage']);
        $this->assertSame(['Elsevier'], $byName['citation_publisher']);
        $this->assertSame(['https://example.test/file.pdf'], $byName['citation_pdf_url']);
        $this->assertArrayHasKey('citation_abstract_html_url', $byName->all());
        $this->assertArrayNotHasKey('citation_isbn', $byName->all());
    }

    public function test_highwire_tags_empty_without_title_or_authors(): void
    {
        $this->assertSame([], PublicationCitationMeta::highwireTags((object) [
            'title' => '',
            'associated_authors' => '',
            'author' => null,
        ]));
        $this->assertSame([], PublicationCitationMeta::highwireTags((object) [
            'title' => 'Only title',
            'associated_authors' => '',
            'author' => null,
        ]));
    }

    public function test_citation_links_prefer_doi_and_fallback_to_scholar_title(): void
    {
        $withDoi = PublicationCitationMeta::citationLinks((object) [
            'title' => 'Test paper',
            'doi' => '10.5555/xyz',
        ]);
        $ids = array_column($withDoi, 'id');
        $this->assertSame(['google_scholar', 'crossref', 'semantic_scholar', 'openalex', 'dimensions', 'doi'], $ids);
        $this->assertStringContainsString('10.5555/xyz', collect($withDoi)->firstWhere('id', 'google_scholar')['url']);

        $noDoi = PublicationCitationMeta::citationLinks((object) [
            'title' => 'Untitled study on cholera',
            'doi' => null,
        ]);
        $this->assertCount(1, $noDoi);
        $this->assertSame('google_scholar', $noDoi[0]['id']);
        $this->assertStringContainsString(rawurlencode('Untitled study on cholera'), $noDoi[0]['url']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PublicationCitationMetaTest`

Expected: FAIL (class not found)

- [ ] **Step 3: Write minimal implementation**

Create `app/Support/PublicationCitationMeta.php` with:

- `highwireTags()` — return `[]` unless non-empty title and ≥1 author name; push meta rows for title, each author (Last, First when two+ whitespace tokens), date (`year_published` as `YYYY` else `created_at` as `Y/m/d`), `citation_abstract_html_url` via `publication_url()`, bare DOI, optional issn/isbn/journal/volume/issue/pages/publisher/pdf.
- `citationLinks()` — DOI path builds all six sources; no DOI builds only Scholar `q={title}`.
- `bareDoi()`, `authorNames()`, `formatAuthorForCitation()`, `parsePages()`.

Author order: primary `author->name` first, then comma-split `associated_authors`, unique by case-insensitive name.

- [ ] **Step 4: Run tests and make sure they pass**

Run: `php artisan test --filter=PublicationCitationMetaTest`

Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add app/Support/PublicationCitationMeta.php tests/Unit/PublicationCitationMetaTest.php
git commit -m "$(cat <<'EOF'
Add PublicationCitationMeta for Scholar Highwire tags and citation links.

EOF
)"
```

---

### Task 2: Emit Highwire tags in publication `<head>`

**Files:**
- Modify: `resources/views/publications/show.blade.php` (top `@php` block)
- Modify: `resources/views/partials/seo/meta.blade.php` (after canonical / before structured data)
- Test: extend `tests/Unit/PublicationCitationMetaTest.php` with a string assertion that show + meta reference the class / tags

**Interfaces:**
- Consumes: `PublicationCitationMeta::highwireTags`, `::citationLinks`
- Produces: `$publicationCitationTags`, `$publicationCitationLinks` view vars; meta partial loops tags

- [ ] **Step 1: Write failing view-wiring test**

```php
public function test_show_and_meta_partials_wire_citation_tags(): void
{
    $show = file_get_contents(resource_path('views/publications/show.blade.php'));
    $meta = file_get_contents(resource_path('views/partials/seo/meta.blade.php'));
    $this->assertStringContainsString('PublicationCitationMeta::highwireTags', $show);
    $this->assertStringContainsString('PublicationCitationMeta::citationLinks', $show);
    $this->assertStringContainsString('publicationCitationTags', $meta);
    $this->assertStringContainsString('citation_', $meta);
    $this->assertStringContainsString('Track citations', $show);
}
```

- [ ] **Step 2: Run to verify fail**

Run: `php artisan test --filter=test_show_and_meta_partials_wire_citation_tags`

Expected: FAIL

- [ ] **Step 3: Wire Blade**

In `show.blade.php` `@php` (after SEO vars):

```php
$publicationCitationTags = \App\Support\PublicationCitationMeta::highwireTags($publication);
$publicationCitationLinks = \App\Support\PublicationCitationMeta::citationLinks($publication);
```

In `meta.blade.php` after canonical:

```blade
@if(!empty($publicationCitationTags))
{{-- Google Scholar / Highwire Press citation meta --}}
@foreach($publicationCitationTags as $citationTag)
<meta name="{{ $citationTag['name'] }}" content="{{ $citationTag['content'] }}">
@endforeach
@endif
```

Near DOI block in show (after DOI / before ISSN), render Track citations when links non-empty:

```blade
@if(!empty($publicationCitationLinks))
<label class="meta-label">Track citations</label>
<span class="meta-value">
    <span class="text-muted d-block mb-1" style="font-size: 0.85rem;">Look up this work in external citation indexes (opens in a new tab).</span>
    <div class="d-flex flex-wrap gap-2 mt-1">
        @foreach($publicationCitationLinks as $citeLink)
            <a href="{{ $citeLink['url'] }}" target="_blank" rel="noopener noreferrer" class="badge badge-secondary" style="text-decoration: none;">
                {{ $citeLink['label'] }} <i class="fa fa-external-link-alt" style="font-size: 0.7rem;"></i>
            </a>
        @endforeach
    </div>
</span>
@endif
```

- [ ] **Step 4: Run tests**

Run: `php artisan test --filter=PublicationCitationMetaTest`

Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/publications/show.blade.php resources/views/partials/seo/meta.blade.php tests/Unit/PublicationCitationMetaTest.php
git commit -m "$(cat <<'EOF'
Wire Highwire citation meta and Track citations on publication pages.

EOF
)"
```

---

## Spec coverage

| Spec item | Task |
| --- | --- |
| Highwire required + optional tags | Task 1 |
| No empty tags; title+author gate | Task 1 |
| Track citations outbound links | Task 1–2 |
| No counts | Task 2 copy |
| Keep Schema.org | untouched |
| Tests | Task 1–2 |
