<?php

namespace App\Support;

use App\Models\Author;
use App\Models\Country;
use App\Models\PublicationCategory;
use App\Models\SubThemeticArea;
use App\Models\Tag;
use App\Models\ThemeticArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * SEO meta and JSON-LD for /records/search and filtered browse views.
 */
class RecordsSearchSeo
{
    /**
     * @return array{
     *     pageTitle: string,
     *     pageDescription: string,
     *     pageKeywords: string,
     *     canonicalUrl: string,
     *     searchJsonLd: array<string, mixed>,
     *     searchHeading: string,
     *     robotsNoindex: bool,
     * }
     */
    public static function build(Request $request, mixed $publications, int $resultsCount, mixed $searchForums, mixed $searchCommunities): array
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');
        $term = $request->filled('term') ? trim((string) $request->term) : '';
        $currentPage = max(1, (int) $request->input('page', 1));
        $context = self::filterContext($request);

        $tagModel = null;
        if ($request->filled('tag')) {
            $tagModel = Tag::find((int) $request->tag);
        }

        $pubTotal = method_exists($publications, 'total') ? (int) $publications->total() : 0;

        if ($tagModel) {
            $tagTitle = Str::limit($tagModel->tag_text, 50);
            $pageTitle = $tagTitle.' — Resources — '.$siteName;
            $pageDescription = Str::limit(
                $pubTotal > 0
                    ? $pubTotal.' resources tagged "'.$tagModel->tag_text.'" on '.$siteName.'. Browse publications, reports, and guidance.'
                    : 'Resources tagged "'.$tagModel->tag_text.'" on '.$siteName.'.',
                160
            );
            $pageKeywords = $tagModel->tag_text.', health topic, publications, '.(settings()->seo_keywords ?? 'Africa CDC, public health');
        } elseif ($term !== '') {
            $pageTitle = 'Search: "'.Str::limit($term, 50).'" — '.$siteName;
            if ($currentPage > 1) {
                $pageTitle = 'Search: "'.Str::limit($term, 40).'" — Page '.$currentPage.' — '.$siteName;
            }
            $pageDescription = Str::limit(
                $resultsCount > 0
                    ? $resultsCount.' results for "'.$term.'" — publications, forums, and communities on '.$siteName.'.'
                    : 'Search results for "'.$term.'" on '.$siteName.'.',
                160
            );
            $pageKeywords = $term.', search, publications, forums, communities, '.(settings()->seo_keywords ?? 'Africa CDC, public health');
        } elseif ($context['label'] !== '') {
            $pageTitle = $context['label'].' — Resources — '.$siteName;
            if ($currentPage > 1) {
                $pageTitle = $context['label'].' — Page '.$currentPage.' — '.$siteName;
            }
            $pageDescription = Str::limit(
                ($pubTotal > 0 ? number_format($pubTotal).' ' : '')
                .'public health resources'.($context['label'] !== '' ? ' for '.$context['label'] : '')
                .' on '.$siteName.'.',
                160
            );
            $pageKeywords = $context['label'].', publications, browse, '.(settings()->seo_keywords ?? 'Africa CDC, public health');
        } else {
            $pageTitle = $pubTotal > 0
                ? 'Browse Public Health Resources | '.number_format($pubTotal).' Results — '.$siteName
                : 'Browse Public Health Resources & Discussions — '.$siteName;
            if ($currentPage > 1) {
                $pageTitle = 'Browse Resources — Page '.$currentPage.' — '.$siteName;
            }
            $pageDescription = Str::limit(
                ($pubTotal > 0 ? 'Explore '.number_format($pubTotal).' publications, reports, and resources ' : 'Search and browse publications, forums, and communities ')
                .'on '.$siteName.' — Africa CDC\'s continental public health knowledge platform.',
                160
            );
            $pageKeywords = 'browse, publications, resources, public health, Africa CDC, forums, '.(settings()->seo_keywords ?? 'knowledge hub');
        }

        $canonicalQuery = self::canonicalQuery($request, $tagModel);
        if ($tagModel && seo_friendly_urls_enabled() && ! empty($tagModel->slug)) {
            $tagQuery = $canonicalQuery;
            unset($tagQuery['tag']);
            $canonicalUrl = tag_records_url($tagModel, true, $tagQuery);
        } elseif ($canonicalQuery === []) {
            $canonicalUrl = url('records/search');
        } else {
            $canonicalUrl = url('records/search?'.http_build_query($canonicalQuery, '', '&', PHP_QUERY_RFC3986));
        }

        $searchJsonLd = self::graph(
            $pageTitle,
            $pageDescription,
            $canonicalUrl,
            $siteUrl,
            $resultsCount,
            $publications,
            $searchForums,
            $searchCommunities
        );

        $searchHeading = match (true) {
            $term !== '' => 'Search results for “'.$term.'”',
            $context['label'] !== '' => 'Resources: '.$context['label'],
            $tagModel !== null => 'Resources tagged “'.$tagModel->tag_text.'”',
            default => 'Browse public health resources',
        };

        return [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageKeywords' => Str::limit(trim($pageKeywords), 300),
            'canonicalUrl' => $canonicalUrl,
            'searchJsonLd' => $searchJsonLd,
            'searchHeading' => $searchHeading,
            'robotsNoindex' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function canonicalQuery(Request $request, ?Tag $tagModel = null): array
    {
        $raw = $request->only([
            'term', 'rcc', 'country_id', 'author_id', 'author', 'thematic_area_id',
            'sub_thematic_area_id', 'subtheme', 'data_category_id', 'category',
            'file_category_id', 'file_type_id', 'file_type', 'tag', 'page',
        ]);

        $filtered = [];
        foreach ($raw as $key => $value) {
            if ($value === null || $value === '' || $value === 'all') {
                continue;
            }
            if (is_array($value)) {
                $value = array_values(array_filter($value, fn ($v) => $v !== null && $v !== '' && $v !== 'all'));
                if ($value === []) {
                    continue;
                }
            }
            if ($key === 'page' && (int) $value <= 1) {
                continue;
            }
            $filtered[$key] = $value;
        }

        if ($tagModel && seo_friendly_urls_enabled() && ! empty($tagModel->slug)) {
            unset($filtered['tag']);
        }

        return $filtered;
    }

    /**
     * @return array{label: string, parts: array<int, string>}
     */
    private static function filterContext(Request $request): array
    {
        $parts = [];

        if ($request->filled('thematic_area_id')) {
            $name = ThemeticArea::query()->whereKey((int) $request->thematic_area_id)->value('description');
            if ($name) {
                $parts[] = clean_unicode($name);
            }
        }

        $subId = $request->input('sub_thematic_area_id') ?: $request->input('subtheme');
        if ($subId) {
            $name = SubThemeticArea::query()->whereKey((int) $subId)->value('description');
            if ($name) {
                $parts[] = clean_unicode($name);
            }
        }

        if ($request->filled('country_id')) {
            $name = Country::query()->whereKey((int) $request->country_id)->value('name');
            if ($name) {
                $parts[] = clean_unicode($name);
            }
        }

        $authorId = $request->input('author_id') ?: $request->input('author');
        if ($authorId) {
            $name = Author::query()->whereKey((int) $authorId)->value('name');
            if ($name) {
                $parts[] = clean_unicode($name);
            }
        }

        if ($request->filled('data_category_id') && $request->data_category_id !== 'all') {
            $catId = is_array($request->data_category_id)
                ? (int) ($request->data_category_id[0] ?? 0)
                : (int) $request->data_category_id;
            if ($catId > 0) {
                $name = PublicationCategory::query()->whereKey($catId)->value('category_name');
                if ($name) {
                    $parts[] = clean_unicode($name);
                }
            }
        }

        $label = $parts !== [] ? implode(' · ', array_slice($parts, 0, 3)) : '';

        return ['label' => $label, 'parts' => $parts];
    }

    /**
     * @return array<string, mixed>
     */
    private static function graph(
        string $pageTitle,
        string $pageDescription,
        string $canonicalUrl,
        string $siteUrl,
        int $resultsCount,
        mixed $publications,
        mixed $searchForums,
        mixed $searchCommunities
    ): array {
        $items = [];
        $position = 1;

        $pubs = $publications instanceof \Illuminate\Pagination\AbstractPaginator
            ? collect($publications->items())
            : collect($publications ?? []);

        foreach ($pubs->take(10) as $pub) {
            $title = trim(strip_tags(clean_unicode((string) ($pub->title ?? ''))));
            if ($title === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'ScholarlyArticle',
                    'headline' => $title,
                    'name' => $title,
                    'url' => publication_url($pub),
                ],
            ];
        }

        $forums = collect($searchForums ?? [])->take(5);
        foreach ($forums as $forum) {
            $title = trim(strip_tags((string) ($forum->forum_title ?? '')));
            if ($title === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'DiscussionForumPosting',
                    'headline' => $title,
                    'name' => $title,
                    'url' => forum_thread_url($forum),
                ],
            ];
        }

        $communities = collect($searchCommunities ?? [])->take(5);
        foreach ($communities as $community) {
            $name = trim((string) ($community->community_name ?? ''));
            if ($name === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'item' => [
                    '@type' => 'Organization',
                    'name' => $name,
                    'url' => community_detail_url($community),
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'SearchResultsPage',
                    '@id' => $canonicalUrl.'#webpage',
                    'url' => $canonicalUrl,
                    'name' => $pageTitle,
                    'description' => $pageDescription,
                    'isPartOf' => ['@id' => $siteUrl.'#website'],
                    'breadcrumb' => ['@id' => $canonicalUrl.'#breadcrumb'],
                    'mainEntity' => ['@id' => $canonicalUrl.'#results'],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl.'#breadcrumb',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Browse Resources', 'item' => url('records/search')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Results', 'item' => $canonicalUrl],
                    ],
                ],
                [
                    '@type' => 'ItemList',
                    '@id' => $canonicalUrl.'#results',
                    'name' => 'Search results',
                    'numberOfItems' => $resultsCount,
                    'itemListElement' => $items,
                ],
            ],
        ];
    }
}
