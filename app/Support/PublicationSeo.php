<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * SEO JSON-LD graph for publication detail pages.
 */
class PublicationSeo
{
    /**
     * @param  iterable<int, object>|null  $relatedPublications
     * @return array<string, mixed>
     */
    public static function structuredDataGraph(object $publication, ?iterable $relatedPublications = null): array
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');
        $pubTitle = clean_unicode($publication->title ?? '');
        $canonicalUrl = publication_url($publication);
        $descPlain = trim(preg_replace('/\s+/u', ' ', strip_tags(clean_unicode($publication->description ?? ''))) ?? '');
        $pageDescription = Str::limit($descPlain !== '' ? $descPlain : $pubTitle, 320);

        $rawCover = $publication->cover ?? $publication->image_url ?? null;
        $pageImage = $rawCover
            ? (filter_var($rawCover, FILTER_VALIDATE_URL) ? $rawCover : asset(ltrim((string) $rawCover, '/')))
            : asset('assets/images/cover.png');

        $publishDate = $publication->created_at ? $publication->created_at->toIso8601String() : now()->toIso8601String();
        $contentUpdated = function_exists('publication_content_updated_at')
            ? publication_content_updated_at($publication)
            : null;
        $modifiedDate = $contentUpdated
            ? \Carbon\Carbon::parse($contentUpdated)->toIso8601String()
            : $publishDate;

        $logoRaw = settings()->logo ?? '';
        $logoAbsolute = $logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
            ? $logoRaw
            : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png'));

        $authorLd = self::authorNodes($publication);
        $categoryName = clean_unicode(optional($publication->data_category)->category_name ?? optional($publication->category)->category_name ?? '');
        $themeDesc = clean_unicode(optional($publication->theme)->description ?? '');
        $subThemeDesc = clean_unicode(optional($publication->sub_theme)->description ?? '');

        $keywords = collect([
            optional($publication->tags)->map(fn ($pt) => clean_unicode(optional($pt->tag)->tag_text ?? ''))->filter()->implode(', '),
            $themeDesc,
            $subThemeDesc,
            $categoryName,
            'public health',
            'Africa CDC',
        ])->filter()->implode(', ');

        $article = [
            '@type' => 'ScholarlyArticle',
            '@id' => $canonicalUrl.'#article',
            'headline' => $pubTitle,
            'name' => $pubTitle,
            'description' => $pageDescription,
            'image' => $pageImage,
            'datePublished' => $publishDate,
            'dateModified' => $modifiedDate,
            'author' => count($authorLd) === 1 ? $authorLd[0] : $authorLd,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => $siteUrl ?: url('/'),
                'logo' => ['@type' => 'ImageObject', 'url' => $logoAbsolute],
            ],
            'mainEntityOfPage' => ['@id' => $canonicalUrl.'#webpage'],
            'inLanguage' => 'en',
            'url' => $canonicalUrl,
            'isAccessibleForFree' => true,
        ];

        if ($keywords !== '') {
            $article['keywords'] = Str::limit($keywords, 300);
        }
        if ($categoryName !== '') {
            $article['articleSection'] = $categoryName;
        }
        if ($themeDesc !== '') {
            $article['about'] = ['@type' => 'Thing', 'name' => $themeDesc];
        }
        if (! empty($publication->doi)) {
            $article['identifier'] = [
                '@type' => 'PropertyValue',
                'propertyID' => 'DOI',
                'value' => $publication->doi,
            ];
        }
        if (! empty($publication->year_published)) {
            $article['copyrightYear'] = (int) $publication->year_published;
        }

        $graph = [
            [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl.'#webpage',
                'url' => $canonicalUrl,
                'name' => $pubTitle.' — '.$siteName,
                'description' => Str::limit($descPlain !== '' ? $descPlain : $pubTitle.' on '.$siteName, 160),
                'isPartOf' => ['@id' => $siteUrl.'#website'],
                'breadcrumb' => ['@id' => $canonicalUrl.'#breadcrumb'],
                'primaryImageOfPage' => $pageImage,
                'mainEntity' => ['@id' => $canonicalUrl.'#article'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $canonicalUrl.'#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Browse Resources', 'item' => url('records/search')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => Str::limit($pubTitle, 70), 'item' => $canonicalUrl],
                ],
            ],
            $article,
        ];

        $relatedItems = self::relatedPublicationList($relatedPublications);
        if ($relatedItems !== []) {
            $graph[] = [
                '@type' => 'ItemList',
                '@id' => $canonicalUrl.'#related',
                'name' => 'Related publications',
                'itemListElement' => $relatedItems,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function authorNodes(object $publication): array
    {
        $nodes = [];
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';

        if ($publication->author) {
            $author = $publication->author;
            $isOrg = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
            $node = [
                '@type' => $isOrg ? 'Organization' : 'Person',
                'name' => clean_unicode($author->name ?? ''),
                'url' => author_publications_url($author),
            ];
            if (! empty($author->orcid)) {
                $node['sameAs'] = ['https://orcid.org/'.$author->orcid];
            }
            $nodes[] = $node;
        }

        if (! empty($publication->associated_authors)) {
            foreach (array_map('trim', explode(',', (string) $publication->associated_authors)) as $name) {
                if ($name === '') {
                    continue;
                }
                $exists = collect($nodes)->contains(fn ($n) => ($n['name'] ?? '') === $name);
                if (! $exists) {
                    $nodes[] = ['@type' => 'Person', 'name' => clean_unicode($name)];
                }
            }
        }

        if ($nodes === []) {
            $nodes[] = ['@type' => 'Organization', 'name' => $siteName];
        }

        $affiliation = trim(clean_unicode($publication->author_affiliation ?? ''));
        if ($affiliation !== '') {
            foreach ($nodes as &$node) {
                if (($node['@type'] ?? '') === 'Person') {
                    $node['affiliation'] = [
                        '@type' => 'Organization',
                        'name' => $affiliation,
                    ];
                }
            }
            unset($node);
        }

        return $nodes;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function relatedPublicationList(?iterable $relatedPublications): array
    {
        if ($relatedPublications === null) {
            return [];
        }

        $items = [];
        $collection = $relatedPublications instanceof Collection
            ? $relatedPublications
            : collect($relatedPublications);

        foreach ($collection->take(8)->values() as $index => $pub) {
            $title = trim(strip_tags(clean_unicode((string) ($pub->title ?? ''))));
            if ($title === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'ScholarlyArticle',
                    'headline' => $title,
                    'name' => $title,
                    'url' => publication_url($pub),
                ],
            ];
        }

        return $items;
    }
}
