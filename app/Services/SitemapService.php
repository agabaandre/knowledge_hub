<?php

namespace App\Services;

use App\Models\Author;
use App\Models\CommunityOfPractice;
use App\Models\Country;
use App\Models\Course;
use App\Models\Event;
use App\Models\Forum;
use App\Models\Publication;
use App\Models\Tag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SitemapService
{
    public const PUBLICATIONS_PER_SITEMAP = 5000;

    /** @var list<string> */
    private const EXCLUDED_PATH_PREFIXES = [
        'admin',
        'account',
        'api',
        'tools',
        'permissions',
        'docs',
        'telescope',
        'install',
        'storage',
        'content-request',
        'mailing_list',
        'tests',
        'auth',
        'locale',
        'hub-media',
        'verify',
        'logout',
        'login',
        'register',
        'password',
    ];

    public function indexEntries(): array
    {
        $entries = [
            $this->indexEntry('pages'),
            $this->indexEntry('forums'),
            $this->indexEntry('communities'),
            $this->indexEntry('health-topics'),
            $this->indexEntry('courses'),
            $this->indexEntry('events'),
            $this->indexEntry('authors'),
        ];

        if (function_exists('states_enabled') && states_enabled()) {
            $entries[] = $this->indexEntry('countries');
        }

        $publicationPages = max(1, (int) ceil($this->publicPublicationCount() / self::PUBLICATIONS_PER_SITEMAP));
        for ($page = 1; $page <= $publicationPages; $page++) {
            $entries[] = $this->indexEntry('publications-'.$page);
        }

        return $entries;
    }

    /**
     * @return array{
     *     sections: list<array{loc: string, lastmod: ?string}>,
     *     section_count: int,
     *     publication_count: int,
     *     sitemap_index_url: string,
     *     robots_url: string
     * }
     */
    public function adminSummary(): array
    {
        $sections = $this->indexEntries();

        return [
            'sections' => $sections,
            'section_count' => count($sections),
            'publication_count' => $this->publicPublicationCount(),
            'sitemap_index_url' => url('sitemap.xml'),
            'robots_url' => url('robots.txt'),
        ];
    }

    public function renderIndex(): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($this->indexEntries() as $entry) {
            $lines[] = '  <sitemap>';
            $lines[] = '    <loc>'.htmlspecialchars($entry['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc>';
            if (! empty($entry['lastmod'])) {
                $lines[] = '    <lastmod>'.$entry['lastmod'].'</lastmod>';
            }
            $lines[] = '  </sitemap>';
        }

        $lines[] = '</sitemapindex>';

        return implode("\n", $lines)."\n";
    }

    public function renderSection(string $name): string
    {
        if (preg_match('/^publications-(\d+)$/', $name, $matches) === 1) {
            return $this->renderUrlSet($this->publicationEntries((int) $matches[1]));
        }

        $entries = match ($name) {
            'pages' => $this->staticPageEntries(),
            'forums' => $this->forumEntries(),
            'communities' => $this->communityEntries(),
            'health-topics' => $this->healthTopicEntries(),
            'countries' => $this->countryEntries(),
            'courses' => $this->courseEntries(),
            'events' => $this->eventEntries(),
            'authors' => $this->authorEntries(),
            default => [],
        };

        return $this->renderUrlSet($entries);
    }

    /**
     * @param  list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>  $entries
     */
    public function renderUrlSet(array $entries): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($entries as $entry) {
            if (! $this->isPublicUrl($entry['loc'])) {
                continue;
            }

            $lines[] = '  <url>';
            $lines[] = '    <loc>'.htmlspecialchars($entry['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc>';
            if (! empty($entry['lastmod'])) {
                $lines[] = '    <lastmod>'.$entry['lastmod'].'</lastmod>';
            }
            if (! empty($entry['changefreq'])) {
                $lines[] = '    <changefreq>'.$entry['changefreq'].'</changefreq>';
            }
            if (! empty($entry['priority'])) {
                $lines[] = '    <priority>'.$entry['priority'].'</priority>';
            }
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }

    public function isPublicUrl(string $url): bool
    {
        $path = strtolower(trim((string) parse_url($url, PHP_URL_PATH), '/'));
        if ($path === '') {
            return true;
        }

        foreach (self::EXCLUDED_PATH_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return false;
            }
        }

        return ! str_contains($path, '/admin/') && ! str_starts_with($path, 'admin/');
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function staticPageEntries(): array
    {
        $pages = [
            ['path' => '/', 'changefreq' => 'daily', 'priority' => '1.0'],
            ['path' => 'records', 'changefreq' => 'daily', 'priority' => '0.9'],
            ['path' => 'publications', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => 'forums', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['path' => 'communities', 'changefreq' => 'daily', 'priority' => '0.8'],
            ['path' => 'courses', 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['path' => 'health-topics', 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['path' => 'authors', 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['path' => 'faqs', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['path' => 'privacy', 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['path' => 'user_manual', 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['path' => 'federated', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'browse/themes', 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['path' => 'browse/subthemes', 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['path' => 'browse/authors', 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['path' => 'healthassets', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/workforce', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/phassets', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/inititaives', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/research_dev', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/data_stats', 'changefreq' => 'weekly', 'priority' => '0.5'],
            ['path' => 'categories/healthindicators', 'changefreq' => 'weekly', 'priority' => '0.5'],
        ];

        if (function_exists('states_enabled') && states_enabled()) {
            $pages[] = ['path' => 'countries', 'changefreq' => 'weekly', 'priority' => '0.8'];
        } elseif (function_exists('hub_admin_units_enabled') && hub_admin_units_enabled()) {
            $pages[] = ['path' => 'adminunits', 'changefreq' => 'weekly', 'priority' => '0.7'];
        }

        $entries = [];
        foreach ($pages as $page) {
            $entries[] = [
                'loc' => url($page['path']),
                'lastmod' => $this->formatDate(now()),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function publicationEntries(int $page): array
    {
        $offset = max(0, ($page - 1) * self::PUBLICATIONS_PER_SITEMAP);
        $entries = [];

        $this->publicPublicationQuery()
            ->select(['id', 'slug', 'content_updated_at', 'updated_at'])
            ->orderBy('id')
            ->offset($offset)
            ->limit(self::PUBLICATIONS_PER_SITEMAP)
            ->get()
            ->each(function ($publication) use (&$entries) {
                $entries[] = [
                    'loc' => publication_url($publication),
                    'lastmod' => $this->formatDate($publication->content_updated_at ?? $publication->updated_at),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function forumEntries(): array
    {
        $entries = [];

        Forum::query()
            ->where('is_approved', 1)
            ->where('status', 1)
            ->where(function ($query) {
                $query->where('is_rejected', 0)->orWhereNull('is_rejected');
            })
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $forum) {
                    $entries[] = [
                        'loc' => forum_thread_url($forum),
                        'lastmod' => $this->formatDate($forum->created_at ?? null),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function communityEntries(): array
    {
        $entries = [];

        CommunityOfPractice::query()
            ->where('is_public', 1)
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $community) {
                    $entries[] = [
                        'loc' => community_detail_url($community),
                        'lastmod' => $this->formatDate($community->updated_at ?? $community->created_at ?? null),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function healthTopicEntries(): array
    {
        if (! Schema::hasTable('tags')) {
            return [];
        }

        $entries = [];
        $query = Tag::query()->orderBy('id');

        if (Schema::hasColumn('tags', 'is_health_topic')) {
            $query->where('is_health_topic', true);
        } elseif (Schema::hasColumn('tags', 'is_health_emergency')) {
            $query->where('is_health_emergency', true);
        }

        $query->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $tag) {
                    $entries[] = [
                        'loc' => health_topic_url($tag),
                        'lastmod' => null,
                        'changefreq' => 'weekly',
                        'priority' => '0.7',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function countryEntries(): array
    {
        if (! function_exists('states_enabled') || ! states_enabled()) {
            return [];
        }

        $entries = [];

        Country::query()
            ->where('region_id', '>', 0)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $country) {
                    $entries[] = [
                        'loc' => country_detail_url($country),
                        'lastmod' => null,
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function courseEntries(): array
    {
        $entries = [];

        Course::query()
            ->where('is_active', true)
            ->select(['id', 'updated_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $course) {
                    $entries[] = [
                        'loc' => route('courses.details', ['id' => (int) $course->id]),
                        'lastmod' => $this->formatDate($course->updated_at ?? $course->created_at),
                        'changefreq' => 'monthly',
                        'priority' => '0.5',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function eventEntries(): array
    {
        $entries = [];

        Event::query()
            ->select(['id', 'updated_at', 'created_at', 'startdate'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $event) {
                    $entries[] = [
                        'loc' => route('public.events.show', ['id' => (int) $event->id]),
                        'lastmod' => $this->formatDate($event->updated_at ?? $event->startdate ?? $event->created_at),
                        'changefreq' => 'monthly',
                        'priority' => '0.5',
                    ];
                }
            });

        return $entries;
    }

    /**
     * @return list<array{loc: string, lastmod: ?string, changefreq: ?string, priority: ?string}>
     */
    private function authorEntries(): array
    {
        $entries = [];

        Author::query()
            ->whereHas('publications', function ($query) {
                $query->where('is_active', 'Active')
                    ->where('is_approved', 1)
                    ->where('is_rejected', 0)
                    ->where('is_admin_only_access', 0);
            })
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$entries) {
                foreach ($rows as $author) {
                    $entries[] = [
                        'loc' => author_publications_url($author),
                        'lastmod' => null,
                        'changefreq' => 'monthly',
                        'priority' => '0.5',
                    ];
                }
            });

        return $entries;
    }

    private function publicPublicationCount(): int
    {
        return $this->publicPublicationQuery()->count();
    }

    private function publicPublicationQuery()
    {
        return Publication::query()
            ->where('is_active', 'Active')
            ->where('is_approved', 1)
            ->where('is_rejected', 0)
            ->where('is_admin_only_access', 0)
            ->where(function ($query) {
                $query->whereDoesntHave('communities')
                    ->orWhere('also_public_on_hub', 1);
            });
    }

    /**
     * @return array{loc: string, lastmod: string}
     */
    private function indexEntry(string $name): array
    {
        return [
            'loc' => url('sitemaps/'.$name.'.xml'),
            'lastmod' => $this->formatDate(now()),
        ];
    }

    private function formatDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->copy()->utc()->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->utc()->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
