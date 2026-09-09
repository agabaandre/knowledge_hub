<?php

namespace App\Support;

/**
 * Google Scholar Highwire citation_* meta tags and outbound citation index links.
 */
class PublicationCitationMeta
{
    /**
     * @return list<array{name: string, content: string}>
     */
    public static function highwireTags(object $publication): array
    {
        $title = trim(strip_tags(clean_unicode((string) ($publication->title ?? ''))));
        $authors = self::authorNames($publication);
        if ($title === '' || $authors === []) {
            return [];
        }

        $tags = [];
        $tags[] = ['name' => 'citation_title', 'content' => $title];

        $orgNames = [];
        if (! empty($publication->author) && ! empty($publication->author->name)
            && strtolower((string) ($publication->author->is_organsiation ?? '')) === 'yes') {
            $orgNames[mb_strtolower(trim(clean_unicode((string) $publication->author->name)))] = true;
        }

        foreach ($authors as $author) {
            $isOrg = isset($orgNames[mb_strtolower($author)]);
            $tags[] = [
                'name' => 'citation_author',
                'content' => self::formatAuthorForCitation($author, $isOrg),
            ];
        }

        $date = self::publicationDate($publication);
        if ($date !== null) {
            $tags[] = ['name' => 'citation_publication_date', 'content' => $date];
        }

        $tags[] = [
            'name' => 'citation_abstract_html_url',
            'content' => publication_url($publication),
        ];

        $doi = self::bareDoi($publication->doi ?? null);
        if ($doi !== null) {
            $tags[] = ['name' => 'citation_doi', 'content' => $doi];
        }

        foreach ([
            'citation_issn' => $publication->issn ?? null,
            'citation_isbn' => $publication->isbn ?? null,
            'citation_journal_title' => $publication->journal_name ?? null,
            'citation_volume' => $publication->journal_volume ?? null,
            'citation_issue' => $publication->journal_issue ?? null,
            'citation_publisher' => $publication->publisher ?? null,
        ] as $name => $value) {
            $content = trim(clean_unicode((string) ($value ?? '')));
            if ($content !== '') {
                $tags[] = ['name' => $name, 'content' => $content];
            }
        }

        [$firstPage, $lastPage] = self::parsePages($publication->journal_pages ?? null);
        if ($firstPage !== null) {
            $tags[] = ['name' => 'citation_firstpage', 'content' => $firstPage];
        }
        if ($lastPage !== null) {
            $tags[] = ['name' => 'citation_lastpage', 'content' => $lastPage];
        }

        $pdfUrl = trim((string) ($publication->publication_pdf_url ?? ''));
        if ($pdfUrl !== '' && filter_var($pdfUrl, FILTER_VALIDATE_URL)) {
            $tags[] = ['name' => 'citation_pdf_url', 'content' => $pdfUrl];
        }

        return $tags;
    }

    /**
     * @return list<array{id: string, label: string, url: string}>
     */
    public static function citationLinks(object $publication): array
    {
        $doi = self::bareDoi($publication->doi ?? null);
        $title = trim(strip_tags(clean_unicode((string) ($publication->title ?? ''))));

        if ($doi !== null) {
            $q = rawurlencode($doi);

            return [
                [
                    'id' => 'google_scholar',
                    'label' => 'Google Scholar',
                    'url' => 'https://scholar.google.com/scholar?q='.$q,
                ],
                [
                    'id' => 'crossref',
                    'label' => 'Crossref',
                    'url' => 'https://search.crossref.org/?q='.$q,
                ],
                [
                    'id' => 'semantic_scholar',
                    'label' => 'Semantic Scholar',
                    'url' => 'https://www.semanticscholar.org/search?q='.$q,
                ],
                [
                    'id' => 'openalex',
                    'label' => 'OpenAlex',
                    'url' => 'https://openalex.org/works/doi:'.$doi,
                ],
                [
                    'id' => 'dimensions',
                    'label' => 'Dimensions',
                    'url' => 'https://app.dimensions.ai/discover/publication?search_text='.$q,
                ],
                [
                    'id' => 'doi',
                    'label' => 'DOI.org',
                    'url' => 'https://doi.org/'.$doi,
                ],
            ];
        }

        if ($title === '') {
            return [];
        }

        return [
            [
                'id' => 'google_scholar',
                'label' => 'Google Scholar',
                'url' => 'https://scholar.google.com/scholar?q='.rawurlencode($title),
            ],
        ];
    }

    public static function bareDoi(?string $doi): ?string
    {
        $doi = trim((string) $doi);
        if ($doi === '') {
            return null;
        }

        $doi = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi) ?? $doi;
        $doi = trim($doi);

        return $doi !== '' ? $doi : null;
    }

    /**
     * @return list<string>
     */
    public static function authorNames(object $publication): array
    {
        $names = [];

        if (! empty($publication->author) && ! empty($publication->author->name)) {
            $names[] = trim(clean_unicode((string) $publication->author->name));
        }

        if (! empty($publication->associated_authors)) {
            foreach (array_map('trim', explode(',', (string) $publication->associated_authors)) as $name) {
                $name = trim(clean_unicode($name));
                if ($name === '') {
                    continue;
                }
                $names[] = $name;
            }
        }

        $unique = [];
        $seen = [];
        foreach ($names as $name) {
            $key = mb_strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $name;
        }

        return $unique;
    }

    public static function formatAuthorForCitation(string $name, bool $isOrganisation = false): string
    {
        $name = trim(clean_unicode($name));
        if ($name === '' || $isOrganisation || str_contains($name, ',')) {
            return $name;
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        if (count($parts) !== 2) {
            return $name;
        }

        // Keep org-like "Africa CDC" / acronym last tokens as full name.
        if (preg_match('/^[A-Z0-9]{2,6}$/', $parts[1])) {
            return $name;
        }

        return $parts[1].', '.$parts[0];
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    public static function parsePages(?string $pages): array
    {
        $pages = trim((string) $pages);
        if ($pages === '') {
            return [null, null];
        }

        if (preg_match('/^(\d+)\s*[-–—]\s*(\d+)$/u', $pages, $m)) {
            return [$m[1], $m[2]];
        }

        if (preg_match('/^(\d+)$/', $pages, $m)) {
            return [$m[1], null];
        }

        return [null, null];
    }

    private static function publicationDate(object $publication): ?string
    {
        $year = $publication->year_published ?? null;
        if ($year !== null && $year !== '' && (int) $year > 0) {
            return (string) (int) $year;
        }

        $created = $publication->created_at ?? null;
        if ($created) {
            try {
                return \Carbon\Carbon::parse($created)->format('Y/m/d');
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
