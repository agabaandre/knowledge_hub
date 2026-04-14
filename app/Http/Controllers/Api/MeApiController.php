<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PdfChatSession;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\PublicationsRepository;
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

    public function __construct(
        PublicationsRepository $publicationsRepo,
        CommsOfPracticeRepository $commsRepo
    ) {
        $this->publicationsRepo = $publicationsRepo;
        $this->commsRepo = $commsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     operationId="getMeLibrary",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="My favourites, publications, communities, and AI chats (summary)",
     *     description="Returns paginated slices for favourites, publications you submitted, and communities you belong to (same data as `GET /api/publications/favourites`, `GET /api/publications/published`, `GET /api/communities/me`). Also returns Khub AI Assistant chat sessions (publication/PDF and forum) in a shape aligned with the web account chats page — there is no separate chats list route.",
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
     *                 @OA\Property(property="assistant_session", type="string", example="/api/ai/assistant/session")
     *             ),
     *             @OA\Property(property="favourites", type="object"),
     *             @OA\Property(property="my_publications", type="object"),
     *             @OA\Property(property="communities", type="object"),
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

        $favouritesPaginator = $this->publicationsRepo->favourites($favRequest);
        $publicationsPaginator = $this->publicationsRepo->my_publications($pubRequest);
        $communitiesPaginator = $this->commsRepo->getByUser($userId, $commRequest);

        return response()->json([
            'status' => 200,
            'endpoints' => [
                'favourites' => url('/api/publications/favourites'),
                'my_publications' => url('/api/publications/published'),
                'my_communities' => url('/api/communities/me'),
                'assistant_session' => url('/api/ai/assistant/session'),
                'assistant_message' => url('/api/ai/assistant/message'),
            ],
            'favourites' => $this->stripPaginatorMeta($favouritesPaginator->toArray()),
            'my_publications' => $this->stripPaginatorMeta($publicationsPaginator->toArray()),
            'communities' => $this->stripPaginatorMeta($communitiesPaginator->toArray()),
            'chats' => $this->buildUserChatsSummary($userId),
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
     * Mirrors web `AccountController::chats` grouping; adds forum-based assistant sessions.
     */
    private function buildUserChatsSummary(int $userId): array
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
                    $byForum[$key] = [
                        'forum_id' => (int) $session->forum_id,
                        'title' => ($forum && $forum->forum_title !== null && $forum->forum_title !== '')
                            ? $forum->forum_title
                            : ('Forum #'.$session->forum_id),
                        'sessions' => [],
                    ];
                }
                $byForum[$key]['sessions'][] = $sessionPayload;
            } else {
                $pub = $session->publication;
                $key = ($session->publication_id ?? '0').'-'.($session->attachment_id ?? 'main');
                if (! isset($byPublication[$key])) {
                    $byPublication[$key] = [
                        'publication_id' => $session->publication_id,
                        'attachment_id' => $session->attachment_id,
                        'title' => $pub
                            ? $pub->title
                            : ('Resource #'.$session->publication_id),
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
