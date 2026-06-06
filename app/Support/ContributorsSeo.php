<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * SEO meta and JSON-LD for contributor directory and profile pages.
 */
class ContributorsSeo
{
    public static function authorsIndexTitle(int $total, int $currentPage = 1, ?string $searchTerm = null): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';

        if ($searchTerm !== null && $searchTerm !== '') {
            $base = 'Contributors matching "'.Str::limit($searchTerm, 40).'"';
        } else {
            $base = 'Contributors & Authors Directory';
            if ($total > 0) {
                $base .= ' | '.number_format($total).' Experts & Institutions';
            }
        }

        if ($currentPage > 1) {
            $base .= ' — Page '.$currentPage;
        }

        return $base.' — '.$siteName;
    }

    public static function authorsIndexDescription(int $total, int $currentPage = 1, ?string $searchTerm = null): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';

        if ($searchTerm !== null && $searchTerm !== '') {
            return Str::limit(
                'Search results for contributors named or affiliated with "'.$searchTerm.'" on '.$siteName.'. '
                .'View publications, forum activity, and profiles.',
                160
            );
        }

        $parts = [
            'Discover researchers, clinicians, ministries, and institutions contributing public health knowledge on '.$siteName,
        ];
        if ($total > 0) {
            array_unshift($parts, number_format($total).' verified contributors sharing publications and forum expertise');
        }
        if ($currentPage > 1) {
            $parts[] = 'Page '.$currentPage;
        }

        return Str::limit(implode('. ', $parts).'.', 160);
    }

    /**
     * @param  iterable<int, object>  $authors
     * @return array<string, mixed>
     */
    public static function authorsIndexGraph(
        iterable $authors,
        int $total,
        string $canonicalUrl,
        int $offset = 0,
        int $currentPage = 1
    ): array {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');
        $description = self::authorsIndexDescription($total);

        $itemList = [];
        $pos = $offset + 1;
        foreach ($authors as $author) {
            $item = self::contributorSchemaNode($author);
            if ($item === null) {
                continue;
            }
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'item' => $item,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    '@id' => $canonicalUrl.'#webpage',
                    'url' => $canonicalUrl,
                    'name' => 'Contributors & Authors',
                    'description' => $description,
                    'isPartOf' => ['@id' => $siteUrl.'#website'],
                    'breadcrumb' => ['@id' => $canonicalUrl.'#breadcrumb'],
                    'mainEntity' => ['@id' => $canonicalUrl.'#contributor-list'],
                ],
                [
                    '@type' => 'BreadcrumbList',
                    '@id' => $canonicalUrl.'#breadcrumb',
                    'itemListElement' => self::authorsBreadcrumbItems($canonicalUrl, $currentPage),
                ],
                [
                    '@type' => 'ItemList',
                    '@id' => $canonicalUrl.'#contributor-list',
                    'name' => 'Knowledge Hub Contributors',
                    'description' => 'Researchers, clinicians, and organisations publishing on '.$siteName,
                    'numberOfItems' => $total,
                    'itemListElement' => $itemList,
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function authorsBreadcrumbItems(string $canonicalUrl, int $currentPage = 1): array
    {
        $items = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Contributors', 'item' => route('browse.authors')],
        ];
        if ($currentPage > 1) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => 'Page '.$currentPage,
                'item' => $canonicalUrl,
            ];
        }

        return $items;
    }

    /**
     * @param  array<string, int>  $stats
     */
    public static function authorProfileTitle(object $author, array $stats): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $name = clean_unicode($author->name ?? 'Contributor');
        $resources = (int) ($stats['resource_contributions'] ?? 0);
        $suffix = $resources > 0
            ? ' | '.number_format($resources).' '.Str::plural('Publication', $resources)
            : ' | Contributor Profile';

        return Str::limit($name.$suffix.' — '.$siteName, 70);
    }

    /**
     * @param  array<string, int>  $stats
     */
    public static function authorProfileDescription(object $author, array $stats): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $name = clean_unicode($author->name ?? 'Contributor');
        $isOrganisation = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
        $user = $author->user ?? null;

        $roleBits = array_filter([
            $user && ! empty($user->job_title) ? clean_unicode($user->job_title) : null,
            $user && ! empty($user->organization_name) ? clean_unicode($user->organization_name) : null,
            $user && ! empty($user->country->name ?? null) ? clean_unicode($user->country->name) : null,
        ]);

        $resources = (int) ($stats['resource_contributions'] ?? 0);
        $total = (int) ($stats['total_contributions'] ?? $resources);
        $forum = (int) ($stats['forum_contributions'] ?? 0);

        $intro = $isOrganisation
            ? 'Publications and resources from '.$name
            : 'Publications and knowledge contributions by '.$name;

        if ($roleBits !== []) {
            $intro .= ' ('.implode(', ', $roleBits).')';
        }

        $impact = $total > 0
            ? '. '.$total.' total contributions'
            : '';
        if ($resources > 0) {
            $impact .= ($impact !== '' ? ', ' : '. ').$resources.' published resources';
        }
        if ($forum > 0) {
            $impact .= ', '.$forum.' forum contributions';
        }

        return Str::limit($intro.$impact.' on '.$siteName.'.', 160);
    }

    /**
     * @param  array<string, int>  $stats
     */
    public static function authorProfileKeywords(object $author, array $stats): string
    {
        $user = $author->user ?? null;
        $bits = array_filter([
            clean_unicode($author->name ?? ''),
            $user && ! empty($user->job_title) ? clean_unicode($user->job_title) : null,
            $user && ! empty($user->organization_name) ? clean_unicode($user->organization_name) : null,
            $user && ! empty($user->country->name ?? null) ? clean_unicode($user->country->name) : null,
            'contributor, author, publications, public health, Africa CDC',
            settings()->seo_keywords ?? 'knowledge hub, Africa',
        ]);

        return Str::limit(implode(', ', $bits), 300);
    }

    /**
     * @param  array<string, int>  $stats
     * @param  mixed  $publications
     */
    public static function authorProfileGraph(object $author, array $stats, $publications, string $canonicalUrl, string $pageDescription): array
    {
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');
        $contributor = self::contributorSchemaNode($author, true, $stats, $canonicalUrl, $pageDescription);
        $publicationItems = self::publicationListFromPaginator($publications, 10);

        $graph = [
            [
                '@type' => 'ProfilePage',
                '@id' => $canonicalUrl.'#webpage',
                'url' => $canonicalUrl,
                'name' => self::authorProfileTitle($author, $stats),
                'description' => $pageDescription,
                'isPartOf' => ['@id' => $siteUrl.'#website'],
                'breadcrumb' => ['@id' => $canonicalUrl.'#breadcrumb'],
                'mainEntity' => ['@id' => $canonicalUrl.'#contributor'],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $canonicalUrl.'#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Contributors', 'item' => route('browse.authors')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => clean_unicode($author->name ?? 'Contributor'), 'item' => $canonicalUrl],
                ],
            ],
            $contributor,
        ];

        if ($publicationItems !== []) {
            $graph[] = [
                '@type' => 'ItemList',
                '@id' => $canonicalUrl.'#publications',
                'name' => 'Publications by '.clean_unicode($author->name ?? 'Contributor'),
                'description' => 'Public health resources published by this contributor',
                'numberOfItems' => (int) ($stats['resource_contributions'] ?? count($publicationItems)),
                'itemListElement' => $publicationItems,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    /**
     * @param  array<string, int>|null  $stats
     * @return array<string, mixed>|null
     */
    public static function contributorSchemaNode(
        object $author,
        bool $asMainEntity = false,
        ?array $stats = null,
        ?string $profileUrl = null,
        ?string $description = null
    ): ?array {
        $name = trim(clean_unicode($author->name ?? ''));
        if ($name === '') {
            return null;
        }

        $isOrganisation = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
        $user = $author->user ?? null;
        $url = $profileUrl ?? author_publications_url($author);

        $node = [
            '@type' => $isOrganisation ? 'Organization' : 'Person',
            'name' => $name,
            'url' => $url,
        ];

        if ($asMainEntity) {
            $node['@id'] = $url.'#contributor';
        }

        if ($description) {
            $node['description'] = $description;
        }

        $image = self::contributorImageUrl($author);
        if ($image) {
            $node['image'] = $image;
        }

        if (! empty($author->orcid)) {
            $node['sameAs'] = ['https://orcid.org/'.$author->orcid];
        }

        if ($user && ! empty($user->job_title)) {
            $node['jobTitle'] = clean_unicode($user->job_title);
        }

        if ($user && ! empty($user->organization_name)) {
            if ($isOrganisation) {
                $node['alternateName'] = clean_unicode($user->organization_name);
            } else {
                $node['worksFor'] = [
                    '@type' => 'Organization',
                    'name' => clean_unicode($user->organization_name),
                ];
            }
        }

        if ($user && ! empty($user->country->name ?? null)) {
            $node['homeLocation'] = [
                '@type' => 'Place',
                'name' => clean_unicode($user->country->name),
            ];
        }

        if ($stats !== null) {
            $node['agentInteractionStatistic'] = [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/WriteAction',
                'userInteractionCount' => (int) ($stats['total_contributions'] ?? 0),
            ];
        }

        return $node;
    }

    public static function contributorImageUrl(object $author): ?string
    {
        if (! empty($author->logo) && $author->logo !== 'author.png') {
            $logo = $author->logo;

            return filter_var($logo, FILTER_VALIDATE_URL) ? $logo : asset(ltrim((string) $logo, '/'));
        }

        $user = $author->user ?? null;
        if ($user && ! empty($user->photo)) {
            return $user->photo;
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function publicationListFromPaginator(mixed $publications, int $limit): array
    {
        $items = [];
        $collection = self::normalizePublications($publications)->take($limit)->values();

        foreach ($collection as $index => $publication) {
            $title = trim(strip_tags(clean_unicode((string) ($publication->title ?? ''))));
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
                    'url' => publication_url($publication),
                ],
            ];
        }

        return $items;
    }

    private static function normalizePublications(mixed $publications): Collection
    {
        if ($publications instanceof \Illuminate\Pagination\AbstractPaginator) {
            return collect($publications->items());
        }
        if ($publications instanceof Collection) {
            return $publications;
        }

        return collect($publications ?? []);
    }
}
