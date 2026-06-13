<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Publication;
use App\Models\PublicationTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\Support\Str;

class HealthTopicsController extends Controller
{
    public const HEALTH_TOPICS_INFINITE_ROWS = 6;

    public function index(Request $request)
    {
        $baseQuery = $this->healthTopicsBaseQuery();
        $totalTopics = (clone $baseQuery)->count();
        $letterCounts = $this->healthTopicLetterCounts($baseQuery);
        $availableLetters = $letterCounts->keys()->sort()->values();
        $healthTopicsInfiniteScroll = $this->healthTopicsInfiniteScrollEnabled();

        if ($healthTopicsInfiniteScroll) {
            $this->prepareHealthTopicsListingRequest($request);
            $topicsPage = (clone $baseQuery)->paginate(self::HEALTH_TOPICS_INFINITE_ROWS);
            $groupedTags = null;
            $topicsPaginator = $topicsPage;
            $loadedTopicCount = (($topicsPage->currentPage() - 1) * $topicsPage->perPage()) + $topicsPage->count();
        } else {
            $tags = (clone $baseQuery)->get();
            $groupedTags = $tags->groupBy(function ($tag) {
                return strtoupper(substr($tag->tag_text, 0, 1));
            });
            $topicsPaginator = null;
            $loadedTopicCount = $tags->count();
        }

        $siteName = settings()->site_name ?? 'Africa CDC Knowledge Hub';
        $siteUrl = rtrim((string) config('app.url'), '/') ?: url('/');

        $pageTitle = 'Health Topics — '.$siteName;
        $pageDescription = Str::limit(
            'Browse '.$totalTopics.' health topics—emergencies, diseases, and public health themes. Find publications, resources, and community discussions on '.$siteName.'.',
            160
        );
        $pageKeywords = trim(
            'health topics, health emergencies, public health, diseases, Africa CDC, knowledge hub, '.
            (settings()->seo_keywords ?? 'Africa, public health research')
        );
        $pageKeywords = Str::limit(preg_replace('/\s+/', ' ', $pageKeywords), 300);

        $logoRaw = settings()->logo ?? '';
        $pageImage = $logoRaw && filter_var($logoRaw, FILTER_VALIDATE_URL)
            ? $logoRaw
            : ($logoRaw ? asset(ltrim($logoRaw, '/')) : asset('assets/images/logo.png'));

        $canonicalUrl = url('/health-topics');
        $ogType = 'website';

        $topicItemList = [];
        $pos = 1;
        $schemaTags = $healthTopicsInfiniteScroll
            ? (clone $baseQuery)->limit(36)->get()
            : (clone $baseQuery)->get()->take(36);
        foreach ($schemaTags as $topicTag) {
            $topicItemList[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $topicTag->tag_text,
                'item' => health_topic_url($topicTag),
            ];
        }

        $jsonLdFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

        $healthTopicsJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Health Topics',
            'description' => $pageDescription,
            'url' => $canonicalUrl,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $siteUrl,
            ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $totalTopics,
                'itemListElement' => $topicItemList,
            ],
        ];

        $healthBreadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Health Topics', 'item' => $canonicalUrl],
            ],
        ];

        return view('health-topics.index', compact(
            'groupedTags',
            'totalTopics',
            'availableLetters',
            'letterCounts',
            'topicsPaginator',
            'loadedTopicCount',
            'healthTopicsInfiniteScroll',
            'pageTitle',
            'pageDescription',
            'pageKeywords',
            'canonicalUrl',
            'pageImage',
            'ogType',
            'healthTopicsJsonLd',
            'healthBreadcrumbLd',
            'jsonLdFlags'
        ));
    }

    public function healthTopicsPage(Request $request)
    {
        if (! $this->healthTopicsInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $baseQuery = $this->healthTopicsBaseQuery();
        $letterCounts = $this->healthTopicLetterCounts($baseQuery);
        $this->prepareHealthTopicsListingRequest($request);
        $topicsPage = (clone $baseQuery)->paginate(self::HEALTH_TOPICS_INFINITE_ROWS);
        $page = (int) $topicsPage->currentPage();
        $perPage = (int) $topicsPage->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($topicsPage->total(), $listOffset + $topicsPage->count());

        $mergeLetter = '';
        if ($page > 1 && $topicsPage->count() > 0) {
            $prevTag = (clone $baseQuery)->skip($listOffset - 1)->first();
            $firstTag = $topicsPage->first();
            if ($prevTag && $firstTag) {
                $prevLetter = strtoupper(substr($prevTag->tag_text, 0, 1));
                $firstLetter = strtoupper(substr($firstTag->tag_text, 0, 1));
                if ($prevLetter === $firstLetter) {
                    $mergeLetter = $firstLetter;
                }
            }
        }

        $viewName = $mergeLetter !== ''
            ? 'health-topics.partials.topic_card_items'
            : 'health-topics.partials.topic_list_items';

        return response()->json([
            'ok' => true,
            'html' => view($viewName, [
                'tags' => $topicsPage->getCollection(),
                'letterCounts' => $letterCounts,
            ])->render(),
            'merge_letter' => $mergeLetter,
            'current_page' => $page,
            'last_page' => (int) $topicsPage->lastPage(),
            'has_more' => $topicsPage->hasMorePages(),
            'total' => (int) $topicsPage->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function healthTopicsBaseQuery()
    {
        return Tag::where(function ($q) {
            if (DBSchema::hasColumn('tags', 'is_health_topic')) {
                $q->where('is_health_topic', true);
            } else {
                $q->where('is_health_emergency', true);
            }
        })->orderBy('tag_text');
    }

    protected function healthTopicLetterCounts($baseQuery): \Illuminate\Support\Collection
    {
        return (clone $baseQuery)->get()->groupBy(function ($tag) {
            return strtoupper(substr($tag->tag_text, 0, 1));
        })->map->count();
    }

    protected function prepareHealthTopicsListingRequest(Request $request): void
    {
        if ($this->healthTopicsInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
            ]);
        }
    }

    protected function healthTopicsInfiniteScrollEnabled(): bool
    {
        return (settings()->health_topics_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }

    public function show($key)
    {
        $baseQuery = Tag::where(function ($q) {
            if (DBSchema::hasColumn('tags', 'is_health_topic')) {
                $q->where('is_health_topic', true);
            } else {
                $q->where('is_health_emergency', true);
            }
        });

        if (ctype_digit((string) $key)) {
            $tag = (clone $baseQuery)->findOrFail((int) $key);
            if (seo_friendly_urls_enabled() && ! empty($tag->slug)) {
                return redirect()->to(health_topic_url($tag), 301);
            }
        } else {
            $tag = (clone $baseQuery)->where('slug', $key)->firstOrFail();
        }

        $id = (int) $tag->id;

        // Get publications tagged with this tag
        $publicationIds = PublicationTag::where('tag_id', $id)
                                      ->pluck('publication_id');

        $publications = Publication::whereIn('id', $publicationIds)
                                 ->with(['author', 'file_type', 'tags'])
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(12);

        // Get forums tagged with this tag
        $forumIds = \App\Models\ForumTag::where('tag', $tag->tag_text)
                                       ->pluck('forum_id');

        $relatedForums = \App\Models\Forum::whereIn('id', $forumIds)
                                         ->where('is_approved', 1)
                                         ->where('status', 1)
                                         ->with(['user', 'tags'])
                                         ->withCount(['comments as total_comments' => function($query) {
                                             $query->whereNull('parent_id');
                                         }, 'likes as total_likes'])
                                         ->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get();

        // Get communities tagged with this tag
        $relatedCommunities = \App\Models\CommunityOfPractice::whereHas('tags', function($query) use ($tag) {
                                                $query->where('tags.id', $tag->id);
                                            })
                                            ->where('is_active', 1)
                                            ->with(['creator', 'region', 'country', 'tags'])
                                            ->withCount([
                                                'approvedMembers as members_count',
                                                'communityForums as forums_count',
                                                'communityPublications as publications_count'
                                            ])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(10)
                                            ->get();

        return view('health-topics.show', compact('tag', 'publications', 'relatedForums', 'relatedCommunities'));
    }
} 