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
    public function index()
    {
        // Get all tags that are marked as health topics
        $tags = Tag::where(function ($q) {
            if (DBSchema::hasColumn('tags', 'is_health_topic')) {
                $q->where('is_health_topic', true);
            } else {
                $q->where('is_health_emergency', true);
            }
        })
            ->orderBy('tag_text')
            ->get();

        // Group tags by first letter
        $groupedTags = $tags->groupBy(function ($tag) {
            return strtoupper(substr($tag->tag_text, 0, 1));
        });

        $totalTopics = $tags->count();
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
        foreach ($tags->take(36) as $topicTag) {
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