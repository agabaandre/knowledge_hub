<?php

namespace App\Http\Controllers\Api;

use App\Models\CommunityOfPractice;
use App\Models\Forum;
use App\Models\ForumTag;
use App\Models\Publication;
use App\Models\PublicationTag;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * JSON API aligned with web `/health-topics` and header "Health Emergencies" (tags with is_health_emergency).
 */
class HealthTopicsApiController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/health-topics",
     *     operationId="HealthTopicsIndex",
     *     tags={"Health topics"},
     *     summary="List health topics (A–Z)",
     *     description="Tags marked as health topics (`is_health_topic`, or legacy `is_health_emergency` if that column is absent)—same scope as the web `/health-topics` index. Returns flat `topics`, `grouped_by_letter`, and `total`.",
     *     @OA\Response(
     *         response=200,
     *         description="status, data.topics[], data.grouped_by_letter, data.total"
     *     )
     * )
     *
     * List health topics (same scope as {@see \App\Http\Controllers\HealthTopicsController::index}).
     */
    public function index(): JsonResponse
    {
        $tags = $this->healthTopicsBaseQuery()
            ->orderBy('tag_text')
            ->get();

        $grouped = [];
        foreach ($tags->groupBy(function (Tag $tag) {
            return strtoupper(Str::substr($tag->tag_text, 0, 1));
        }) as $letter => $letterTags) {
            $grouped[$letter] = $letterTags->map(fn (Tag $t) => $this->tagSummary($t))->values()->all();
        }

        ksort($grouped);

        return response()->json([
            'status' => 200,
            'data' => [
                'total' => $tags->count(),
                'topics' => $tags->map(fn (Tag $t) => $this->tagSummary($t))->values()->all(),
                'grouped_by_letter' => $grouped,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/health-topics/{id}",
     *     operationId="HealthTopicsShow",
     *     tags={"Health topics"},
     *     summary="Health topic detail",
     *     description="Overview HTML, paginated tagged publications, related forums and communities—aligned with web `health-topics.show`. Query `page`, `per_page` / `page_size` (default page size 12, max 50). Includes `web.health_topic_page` and `web.records_search_by_tag` URLs.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="data.tag, data.publications, data.related_forums, data.related_communities, data.web"),
     *     @OA\Response(response=404, description="Health topic not found")
     * )
     *
     * Health topic detail: overview, paginated publications, related forums & communities (web health-topics.show).
     */
    public function showTopic(Request $request, int $id): JsonResponse
    {
        $tag = $this->healthTopicsBaseQuery()->find($id);
        if (! $tag) {
            return response()->json(['status' => 404, 'message' => 'Health topic not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $this->buildTagDetail($tag, $request),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/health-emergencies",
     *     operationId="HealthEmergenciesIndex",
     *     tags={"Health topics"},
     *     summary="List health emergency tags",
     *     description="Tags with `is_health_emergency` (same set as the web header **Health Emergencies** menu). Empty when the column is missing. Returns `emergencies`, `grouped_by_letter`, `total`.",
     *     @OA\Response(
     *         response=200,
     *         description="status, data.emergencies[], data.grouped_by_letter, data.total"
     *     )
     * )
     *
     * Tags shown under "Health Emergencies" in the web header (is_health_emergency).
     */
    public function emergenciesIndex(): JsonResponse
    {
        if (! Schema::hasColumn('tags', 'is_health_emergency')) {
            return response()->json([
                'status' => 200,
                'data' => [
                    'total' => 0,
                    'emergencies' => [],
                    'grouped_by_letter' => [],
                ],
            ]);
        }

        $tags = Tag::query()
            ->where('is_health_emergency', true)
            ->orderBy('tag_text')
            ->get();

        $grouped = [];
        foreach ($tags->groupBy(function (Tag $tag) {
            return strtoupper(Str::substr($tag->tag_text, 0, 1));
        }) as $letter => $letterTags) {
            $grouped[$letter] = $letterTags->map(fn (Tag $t) => $this->tagSummary($t))->values()->all();
        }
        ksort($grouped);

        return response()->json([
            'status' => 200,
            'data' => [
                'total' => $tags->count(),
                'emergencies' => $tags->map(fn (Tag $t) => $this->tagSummary($t))->values()->all(),
                'grouped_by_letter' => $grouped,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/health-emergencies/{id}",
     *     operationId="HealthEmergenciesShow",
     *     tags={"Health topics"},
     *     summary="Health emergency tag detail",
     *     description="Same payload shape as `GET /api/health-topics/{id}` but the tag must have `is_health_emergency`. Paginate publications with `page`, `per_page` / `page_size`.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Same as health topic detail"),
     *     @OA\Response(response=404, description="Not found or emergencies not configured")
     * )
     *
     * Detail for one health emergency tag (same related content as topic show).
     */
    public function showEmergency(Request $request, int $id): JsonResponse
    {
        if (! Schema::hasColumn('tags', 'is_health_emergency')) {
            return response()->json(['status' => 404, 'message' => 'Health emergencies not configured'], 404);
        }

        $tag = Tag::query()->where('is_health_emergency', true)->find($id);
        if (! $tag) {
            return response()->json(['status' => 404, 'message' => 'Health emergency not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $this->buildTagDetail($tag, $request),
        ]);
    }

    private function healthTopicsBaseQuery()
    {
        return Tag::query()->where(function ($q) {
            if (Schema::hasColumn('tags', 'is_health_topic')) {
                $q->where('is_health_topic', true);
            } else {
                $q->where('is_health_emergency', true);
            }
        });
    }

    private function tagSummary(Tag $tag): array
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($tag->overview ?? ''))));

        return [
            'id' => (int) $tag->id,
            'tag_text' => (string) $tag->tag_text,
            'overview_plain_preview' => $plain !== '' ? Str::limit($plain, 220) : null,
            'is_health_topic' => (bool) ($tag->is_health_topic ?? false),
            'is_health_emergency' => (bool) ($tag->is_health_emergency ?? false),
        ];
    }

    private function tagDetail(Tag $tag): array
    {
        return [
            'id' => (int) $tag->id,
            'tag_text' => (string) $tag->tag_text,
            'overview' => $tag->overview,
            'is_health_topic' => (bool) ($tag->is_health_topic ?? false),
            'is_health_emergency' => (bool) ($tag->is_health_emergency ?? false),
        ];
    }

    private function buildTagDetail(Tag $tag, Request $request): array
    {
        $perPage = max(1, min(50, (int) $request->input('page_size', $request->input('per_page', 12))));

        $publicationIds = PublicationTag::query()->where('tag_id', $tag->id)->pluck('publication_id');

        $publications = Publication::query()
            ->whereIn('id', $publicationIds)
            ->with(['author', 'file_type', 'tags'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($request->query());

        $pubArray = $publications->toArray();
        unset(
            $pubArray['links'],
            $pubArray['last_page_url'],
            $pubArray['next_page_url'],
            $pubArray['path'],
            $pubArray['first_page_url'],
            $pubArray['prev_page_url']
        );

        $forumIds = ForumTag::query()->where('tag', $tag->tag_text)->pluck('forum_id');

        $relatedForums = Forum::query()
            ->whereIn('id', $forumIds)
            ->where('is_approved', 1)
            ->where('status', 1)
            ->with(['user', 'tags'])
            ->withCount([
                'comments as total_comments' => function ($query) {
                    $query->whereNull('parent_id');
                },
                'likes as total_likes',
            ])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $relatedCommunities = CommunityOfPractice::query()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->where('is_active', 1)
            ->with(['creator', 'region', 'country', 'tags'])
            ->withCount([
                'approvedMembers as members_count',
                'communityForums as forums_count',
                'communityPublications as publications_count',
            ])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'tag' => $this->tagDetail($tag),
            'publications' => $pubArray,
            'related_forums' => $relatedForums->map(fn ($f) => $f->toArray())->values()->all(),
            'related_communities' => $relatedCommunities->map(fn ($c) => $c->toArray())->values()->all(),
            'web' => [
                'health_topic_page' => url('/health-topics/'.$tag->id),
                'records_search_by_tag' => url('/records?'.http_build_query(['tag' => $tag->id])),
            ],
        ];
    }
}
