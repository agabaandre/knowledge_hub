<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Homepage title, meta description, and JSON-LD structured data.
 */
class HomePageSeo
{
    public static function pageTitle(): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $title = strip_tags((string) (settings()->title ?? $siteName));

        if (stripos($title, 'publication') !== false || stripos($title, 'health emergenc') !== false) {
            return $title;
        }

        return $title.' | Publications, Health Emergencies & Resources';
    }

    /**
     * @param  Collection<int, object>  $healthEmergencies
     */
    public static function pageDescription(Collection $healthEmergencies): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $parts = [
            "Discover publications, flagship initiatives, health topics, forums, and courses on {$siteName}",
            "Africa CDC's continental public health knowledge platform",
        ];

        $emergencyNames = $healthEmergencies
            ->map(fn ($tag) => trim((string) ($tag->tag_text ?? '')))
            ->filter()
            ->take(6)
            ->values();

        if ($emergencyNames->isNotEmpty()) {
            $suffix = $healthEmergencies->count() > 6 ? ' and more' : '';
            array_unshift(
                $parts,
                'Health emergency resources for '.$emergencyNames->implode(', ').$suffix
            );
        }

        return Str::limit(implode('. ', $parts).'.', 160);
    }

    /**
     * @param  Collection<int, object>  $healthEmergencies
     */
    public static function pageKeywords(Collection $healthEmergencies): string
    {
        $base = settings()->seo_keywords ?? 'Africa CDC, public health, health research, publications, knowledge hub, Africa, health emergencies';

        $emergencyKeywords = $healthEmergencies
            ->map(fn ($tag) => trim((string) ($tag->tag_text ?? '')))
            ->filter()
            ->implode(', ');

        $merged = trim($base.($emergencyKeywords !== '' ? ', '.$emergencyKeywords : ''));

        return Str::limit(preg_replace('/\s*,\s*,+/', ', ', $merged) ?? $merged, 300);
    }

    /**
     * @param  array{
     *     healthEmergencies: Collection,
     *     initiatives?: mixed,
     *     featured?: mixed,
     *     recent?: mixed,
     *     categories?: array<int, array<string, mixed>>,
     *     events?: mixed,
     * }  $data
     * @return array<int, array<string, mixed>>
     */
    public static function structuredDataGraph(array $data): array
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');
        $pageTitle = self::pageTitle();
        $pageDescription = self::pageDescription($data['healthEmergencies']);
        $homeUrl = url('/');

        $graph = [
            [
                '@type' => 'WebPage',
                '@id' => $homeUrl.'#webpage',
                'url' => $homeUrl,
                'name' => $pageTitle,
                'description' => $pageDescription,
                'isPartOf' => ['@id' => $siteUrl.'#website'],
                'about' => [
                    '@type' => 'Thing',
                    'name' => 'Public health knowledge and emergency response resources in Africa',
                ],
                'breadcrumb' => [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [[
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => $homeUrl,
                    ]],
                ],
            ],
            [
                '@type' => 'CollectionPage',
                '@id' => $homeUrl.'#collection',
                'name' => 'Public Health Resources',
                'description' => 'Flagship initiatives, publications, health emergencies, forums, and learning on '.$siteName,
                'url' => $homeUrl,
                'isPartOf' => ['@id' => $homeUrl.'#webpage'],
            ],
        ];

        $healthEmergencies = $data['healthEmergencies'];
        if ($healthEmergencies->isNotEmpty()) {
            $graph[] = self::tagItemList(
                'Health Emergencies',
                'Priority disease outbreaks and health emergency topics with curated publications',
                $healthEmergencies
            );
        }

        $initiatives = self::normalizePublications($data['initiatives'] ?? null);
        if ($initiatives->isNotEmpty()) {
            $list = self::publicationItemList(
                settings()->section_title_flagship_initiatives ?? 'Flagship Initiatives',
                'Africa CDC flagship public health initiatives and programmes',
                $initiatives,
                10
            );
            if ($list !== null) {
                $graph[] = $list;
            }
        }

        $featured = self::normalizePublications($data['featured'] ?? null);
        if ($featured->isNotEmpty()) {
            $list = self::publicationItemList(
                settings()->section_title_recommended ?? 'Recommended',
                'Editorially recommended public health publications',
                $featured,
                10
            );
            if ($list !== null) {
                $graph[] = $list;
            }
        }

        $recent = self::normalizePublications($data['recent'] ?? null);
        if ($recent->isNotEmpty()) {
            $list = self::publicationItemList(
                settings()->section_title_top_searches ?? 'Top Searches',
                'Most viewed public health publications on the knowledge hub',
                $recent,
                10
            );
            if ($list !== null) {
                $graph[] = $list;
            }
        }

        $categories = $data['categories'] ?? [];
        if (is_array($categories) && $categories !== []) {
            $categoryList = self::categoryItemList($categories);
            if ($categoryList !== null) {
                $graph[] = $categoryList;
            }
        }

        $events = self::normalizeEvents($data['events'] ?? null);
        if ($events->isNotEmpty()) {
            $graph[] = self::eventItemList($events);
        }

        return $graph;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function publicationItemList(string $name, string $description, Collection $publications, int $limit): ?array
    {
        $items = [];
        foreach ($publications->take($limit)->values() as $index => $publication) {
            $article = self::scholarlyArticleNode($publication);
            if ($article === null) {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $article,
            ];
        }

        if ($items === []) {
            return null;
        }

        return [
            '@type' => 'ItemList',
            'name' => $name,
            'description' => $description,
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  Collection<int, object>  $tags
     * @return array<string, mixed>
     */
    private static function tagItemList(string $name, string $description, Collection $tags): array
    {
        $items = [];
        foreach ($tags->values() as $index => $tag) {
            $label = trim((string) ($tag->tag_text ?? ''));
            if ($label === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'CollectionPage',
                    'name' => $label,
                    'url' => tag_records_url($tag),
                    'description' => 'Publications and resources for '.$label.' on '.(settings()->site_name ?? 'Africa Health Knowledge Hub'),
                ],
            ];
        }

        return [
            '@type' => 'ItemList',
            'name' => $name,
            'description' => $description,
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $categories
     * @return array<string, mixed>|null
     */
    private static function categoryItemList(array $categories): ?array
    {
        $items = [];
        foreach ($categories as $index => $category) {
            $title = trim((string) ($category['title'] ?? ''));
            $link = trim((string) ($category['link'] ?? ''));
            if ($title === '' || $link === '') {
                continue;
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'WebPage',
                    'name' => $title,
                    'url' => url($link),
                    'description' => trim((string) ($category['description'] ?? '')),
                ],
            ];
        }

        if ($items === []) {
            return null;
        }

        return [
            '@type' => 'ItemList',
            'name' => 'Explore Key Sections',
            'description' => 'Main ways to browse public health knowledge on the hub',
            'itemListElement' => $items,
        ];
    }

    /**
     * @param  Collection<int, object>  $events
     * @return array<string, mixed>
     */
    private static function eventItemList(Collection $events): array
    {
        $items = [];
        foreach ($events->take(8)->values() as $index => $event) {
            $title = trim((string) ($event->title ?? ''));
            if ($title === '') {
                continue;
            }
            $eventNode = [
                '@type' => 'Event',
                'name' => $title,
                'url' => url('events/'.$event->id),
                'eventAttendanceMode' => ! empty($event->is_online)
                    ? 'https://schema.org/OnlineEventAttendanceMode'
                    : 'https://schema.org/OfflineEventAttendanceMode',
                'eventStatus' => 'https://schema.org/EventScheduled',
            ];
            if (! empty($event->startdate)) {
                $eventNode['startDate'] = \Carbon\Carbon::parse($event->startdate)->toIso8601String();
            }
            if (! empty($event->enddate)) {
                $eventNode['endDate'] = \Carbon\Carbon::parse($event->enddate)->toIso8601String();
            }
            if (! empty($event->venue)) {
                $eventNode['location'] = [
                    '@type' => 'Place',
                    'name' => strip_tags((string) $event->venue),
                ];
            }
            $desc = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($event->description ?? ''))) ?? '');
            if ($desc !== '') {
                $eventNode['description'] = Str::limit($desc, 240);
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $eventNode,
            ];
        }

        return [
            '@type' => 'ItemList',
            'name' => 'Upcoming Events',
            'description' => 'Public health events listed on '.(settings()->site_name ?? 'Africa Health Knowledge Hub'),
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function scholarlyArticleNode(object $publication): ?array
    {
        $title = trim(strip_tags(clean_unicode((string) ($publication->title ?? ''))));
        if ($title === '') {
            return null;
        }

        $descPlain = trim(preg_replace('/\s+/u', ' ', strip_tags(clean_unicode((string) ($publication->description ?? '')))) ?? '');
        $node = [
            '@type' => 'ScholarlyArticle',
            'headline' => $title,
            'name' => $title,
            'url' => publication_url($publication),
        ];

        if ($descPlain !== '') {
            $node['description'] = Str::limit($descPlain, 240);
        }

        $authorName = trim(clean_unicode((string) (optional($publication->author)->name ?? '')));
        if ($authorName !== '') {
            $node['author'] = ['@type' => 'Organization', 'name' => $authorName];
        }

        if (! empty($publication->created_at)) {
            $node['datePublished'] = $publication->created_at->toIso8601String();
        }

        $rawCover = $publication->cover ?? $publication->image_url ?? null;
        if ($rawCover) {
            $node['image'] = filter_var($rawCover, FILTER_VALIDATE_URL)
                ? $rawCover
                : asset(ltrim((string) $rawCover, '/'));
        }

        if (! empty($publication->tags)) {
            $keywords = collect($publication->tags)
                ->map(fn ($tag) => trim((string) (optional($tag->tag)->tag_text ?? $tag->tag_text ?? '')))
                ->filter()
                ->unique()
                ->implode(', ');
            if ($keywords !== '') {
                $node['keywords'] = $keywords;
            }
        }

        return $node;
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

    private static function normalizeEvents(mixed $events): Collection
    {
        if ($events instanceof Collection) {
            return $events;
        }

        return collect($events ?? []);
    }
}
