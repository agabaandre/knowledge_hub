<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PdfChatSession;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\ForumsRepository;
use App\Repositories\PublicationsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Aggregates the logged-in user’s library in one response. Full paginated lists remain on the dedicated routes.
 */
class MeApiController extends Controller
{
    /** @var PublicationsRepository */
    private $publicationsRepo;

    /** @var CommsOfPracticeRepository */
    private $commsRepo;

    /** @var ForumsRepository */
    private $forumsRepo;

    public function __construct(
        PublicationsRepository $publicationsRepo,
        CommsOfPracticeRepository $commsRepo,
        ForumsRepository $forumsRepo
    ) {
        $this->publicationsRepo = $publicationsRepo;
        $this->commsRepo = $commsRepo;
        $this->forumsRepo = $forumsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     operationId="getMeLibrary",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="My favourites, publications, communities, and AI chats (summary)",
     *     description="Returns paginated slices for favourites, publications you submitted, and communities you belong to (same data as `GET /api/publications/favourites`, `GET /api/publications/published`, `GET /api/communities/me`). The `chats` object matches **`GET /api/me/chats`** (`by_publication` / `by_forum`, with `resource_url` / `thread_url`). Use `GET /api/me/chats` when you only need the chat list.",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Page size for each paginated section (default 10, max 50)",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(name="favourites_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="publications_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="communities_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="publications_term", in="query", required=false, description="Search filter for my publications only", @OA\Schema(type="string")),
     *     @OA\Parameter(name="communities_term", in="query", required=false, description="Search filter for my communities only", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Combined payload",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="endpoints",
     *                 type="object",
     *                 @OA\Property(property="favourites", type="string", example="/api/publications/favourites"),
     *                 @OA\Property(property="my_publications", type="string", example="/api/publications/published"),
     *                 @OA\Property(property="my_communities", type="string", example="/api/communities/me"),
     *                 @OA\Property(property="my_forums", type="string", example="/api/me/forums"),
     *                 @OA\Property(property="assistant_session", type="string", example="/api/ai/assistant/session"),
     *                 @OA\Property(property="assistant_message", type="string", example="/api/ai/assistant/message"),
     *                 @OA\Property(property="ai_chat", type="string", example="/api/ai/chat"),
     *                 @OA\Property(property="my_chats", type="string", example="/api/me/chats")
     *             ),
     *             @OA\Property(property="favourites", type="object"),
     *             @OA\Property(property="my_publications", type="object"),
     *             @OA\Property(property="communities", type="object"),
     *             @OA\Property(property="forums", type="object", description="First page of forums you joined (`GET /api/me/forums`)"),
     *             @OA\Property(property="chats", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function library(Request $request)
    {
        $userId = (int) $request->user()->id;
        $per = min(max((int) $request->input('per_page', 10), 1), 50);
        $pageFav = max((int) $request->input('favourites_page', 1), 1);
        $pagePub = max((int) $request->input('publications_page', 1), 1);
        $pageComm = max((int) $request->input('communities_page', 1), 1);

        $favRequest = $request->duplicate(array_merge($request->query->all(), [
            'page' => $pageFav,
            'rows' => $per,
        ]));

        $pubRequest = $request->duplicate(array_merge($request->query->all(), [
            'page' => $pagePub,
            'rows' => $per,
            'term' => $request->filled('publications_term')
                ? $request->input('publications_term')
                : 'a',
        ]));

        $commQuery = array_merge($request->query->all(), [
            'page' => $pageComm,
            'page_size' => $per,
        ]);
        if ($request->filled('communities_term')) {
            $commQuery['term'] = $request->input('communities_term');
        }
        $commRequest = $request->duplicate($commQuery);
        $commRequest->merge([
            'rows' => (int) ($commRequest->input('page_size') ?? $per),
        ]);

        $forumRequest = $request->duplicate(array_merge($request->query->all(), [
            'page' => 1,
            'page_size' => $per,
        ]));

        $favouritesPaginator = $this->publicationsRepo->favourites($favRequest);
        $publicationsPaginator = $this->publicationsRepo->my_publications($pubRequest);
        $communitiesPaginator = $this->commsRepo->getByUser($userId, $commRequest);
        $forumsPaginator = $this->forumsRepo->getSubscribedForUser($userId, $forumRequest);

        return response()->json([
            'status' => 200,
            'endpoints' => [
                'favourites' => url('/api/publications/favourites'),
                'my_publications' => url('/api/publications/published'),
                'my_communities' => url('/api/communities/me'),
                'my_forums' => url('/api/me/forums'),
                'assistant_session' => url('/api/ai/assistant/session'),
                'assistant_message' => url('/api/ai/assistant/message'),
                'ai_chat' => url('/api/ai/chat'),
                'my_chats' => url('/api/me/chats'),
            ],
            'favourites' => $this->stripPaginatorMeta($favouritesPaginator->toArray()),
            'my_publications' => $this->stripPaginatorMeta($publicationsPaginator->toArray()),
            'communities' => $this->stripPaginatorMeta($communitiesPaginator->toArray()),
            'forums' => $this->stripPaginatorMeta($forumsPaginator->toArray()),
            'chats' => $this->buildUserChatsData($userId),
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/me/forums",
     *     operationId="getMyForums",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Forum threads I joined",
     *     description="Paginated list of live, approved forums you are subscribed to (`forum_subscriptions`). Use `page_size` (1–50) and optional `term` search.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="term", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Paginator JSON + status + page_size"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function forums(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $request->merge([
            'page_size' => min(max((int) $request->input('page_size', 20), 1), 50),
        ]);

        $paginator = $this->forumsRepo->getSubscribedForUser($userId, $request);
        $data = $paginator->toArray() ?? [];
        $data['status'] = 200;
        $data['message'] = 'Your forums retrieved successfully';
        $data['page_size'] = (int) ($data['per_page'] ?? 20);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/me/chats",
     *     operationId="getMyAiChats",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="List my Khub AI chats",
     *     description="Returns PDF/document and forum Khub AI sessions grouped by resource — same information as the web **My Chats** page (`/account/chats`). Each group includes a `resource_url` or `thread_url` for opening the resource in the browser.",
     *     @OA\Response(
     *         response=200,
     *         description="Grouped chat sessions",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="chats",
     *                 type="object",
     *                 @OA\Property(
     *                     property="by_publication",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="publication_id", type="integer", nullable=true),
     *                         @OA\Property(property="attachment_id", type="integer", nullable=true),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="resource_url", type="string", description="Web URL for this resource + attachment"),
     *                         @OA\Property(property="sessions", type="array", @OA\Items(type="object"))
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="by_forum",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="forum_id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="thread_url", type="string"),
     *                         @OA\Property(property="sessions", type="array", @OA\Items(type="object"))
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function chats(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;

        return response()->json([
            'status' => 200,
            'chats' => $this->buildUserChatsData($userId),
        ], 200);
    }

    private function stripPaginatorMeta(array $data): array
    {
        unset(
            $data['links'],
            $data['last_page_url'],
            $data['next_page_url'],
            $data['path'],
            $data['first_page_url'],
            $data['prev_page_url']
        );
        $data['status'] = 200;
        $data['page_size'] = (int) ($data['per_page'] ?? 0);

        return $data;
    }

    /**
     * Mirrors web `AccountController::chats` grouping (publication + attachment); adds forum assistant sessions and web URLs.
     *
     * @return array{by_publication: list<array>, by_forum: list<array>}
     */
    private function buildUserChatsData(int $userId): array
    {
        $sessions = PdfChatSession::query()
            ->where('user_id', $userId)
            ->with([
                'publication:id,title',
                'attachment:id,publication_id,file',
                'forum:id,forum_title',
            ])
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->get();

        $byPublication = [];
        $byForum = [];

        foreach ($sessions as $session) {
            $sessionPayload = [
                'id' => $session->id,
                'message_count' => (int) $session->messages_count,
                'assistant_mode' => $session->assistant_mode,
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at,
            ];

            if ($session->forum_id) {
                $key = 'f-'.$session->forum_id;
                if (! isset($byForum[$key])) {
                    $forum = $session->forum;
                    $forumId = (int) $session->forum_id;
                    $byForum[$key] = [
                        'forum_id' => $forumId,
                        'title' => ($forum && $forum->forum_title !== null && $forum->forum_title !== '')
                            ? $forum->forum_title
                            : ('Forum #'.$forumId),
                        'thread_url' => url('forums/thread?id='.$forumId),
                        'sessions' => [],
                    ];
                }
                $byForum[$key]['sessions'][] = $sessionPayload;
            } else {
                $pub = $session->publication;
                $key = ($session->publication_id ?? '0').'-'.($session->attachment_id ?? 'main');
                if (! isset($byPublication[$key])) {
                    $publicationId = $session->publication_id !== null ? (int) $session->publication_id : null;
                    $attachmentId = $session->attachment_id !== null ? (int) $session->attachment_id : null;
                    $resourceUrl = $publicationId !== null
                        ? url('records/resource?id='.$publicationId.($attachmentId ? '&attachment_id='.$attachmentId : ''))
                        : null;
                    $byPublication[$key] = [
                        'publication_id' => $publicationId,
                        'attachment_id' => $attachmentId,
                        'title' => $pub
                            ? $pub->title
                            : ('Resource #'.$session->publication_id),
                        'resource_url' => $resourceUrl,
                        'sessions' => [],
                    ];
                }
                $byPublication[$key]['sessions'][] = $sessionPayload;
            }
        }

        return [
            'by_publication' => array_values($byPublication),
            'by_forum' => array_values($byForum),
        ];
    }
}
