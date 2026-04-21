<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Models\Forum;
use App\Models\ForumSubscription;
use App\Repositories\ForumsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumsApiController extends ApiController
{
    /** @var ForumsRepository */
    private $forumsRepo;

    public function __construct(ForumsRepository $forumsRepo)
    {
        $this->forumsRepo = $forumsRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/forums",
     *     operationId="ListForums",
     *     tags={"Forums"},
     *     summary="List forums",
     *     description="Paginated approved forums (same rules as web `/forums`: search, tag, community filter, visibility). Send `Authorization: Bearer` to include `joined_forum_ids` for the current user.",
     *     @OA\Parameter(name="term", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="tag", in="query", description="Filter by tag text (ForumTag)", @OA\Schema(type="string")),
     *     @OA\Parameter(name="community_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Paginator payload + status + optional joined_forum_ids")
     * )
     */
    public function index(Request $request)
    {
        $request->merge([
            'rows' => $request->input('page_size', $request->input('rows', 20)),
        ]);

        $paginator = $this->forumsRepo->get($request, 1, null, false);
        $data = $paginator->toArray() ?? [];
        $data['status'] = 200;
        $data['page_size'] = (int) ($data['per_page'] ?? $request->input('rows', 20));
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        $user = $request->user('api') ?? $request->user();
        if ($user) {
            $data['joined_forum_ids'] = ForumSubscription::query()
                ->where('user_id', $user->id)
                ->pluck('forum_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        } else {
            $data['joined_forum_ids'] = [];
        }

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/forums/me",
     *     operationId="getMyForumsByPath",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Forum threads I joined",
     *     description="Same response as `GET /api/me/forums`: paginated live, approved forums you are subscribed to (`forum_subscriptions`). Query: `page`, `page_size` (1–50), optional `term`.",
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="term", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Paginator JSON + status + page_size"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function myForums(Request $request): JsonResponse
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
     *     path="/api/forums/{id}",
     *     operationId="ShowForum",
     *     tags={"Forums"},
     *     summary="Forum thread",
     *     description="Full thread (same eager loads as web). Non–live threads are only visible to the author or an admin. View count increments like the web thread page.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Forum + meta"),
     *     @OA\Response(response=404, description="Not found or not visible")
     * )
     */
    public function show(Request $request, $id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return response()->json(['status' => 404, 'message' => 'Forum not found'], 404);
        }

        $forum = $this->forumsRepo->find($id, true);
        if (! $forum) {
            return response()->json(['status' => 404, 'message' => 'Forum not found'], 404);
        }

        if (! $this->requestUserMayViewForum($request, $forum)) {
            return response()->json(['status' => 404, 'message' => 'Forum not found'], 404);
        }

        $forum->loadCount([
            'comments as total_comments' => function ($q) {
                $q->whereNull('parent_id');
            },
            'likes as total_likes',
        ]);

        $user = $request->user('api') ?? $request->user();
        $meta = [
            'joined' => $user
                ? ForumSubscription::where('user_id', $user->id)->where('forum_id', $forum->id)->exists()
                : false,
            'is_liked' => $user ? $forum->isLikedBy($user->id) : false,
        ];

        return response()->json([
            'status' => 200,
            'data' => $forum,
            'meta' => $meta,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/forums",
     *     operationId="CreateForum",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Create forum thread",
     *     description="Same as web `POST /forums/publish`: pending moderation, optional cover `image`, `attachments[]`, `communities` ids, `tags` ids, `tag_all_my_communities`, `also_public_with_communities`. Use multipart for files.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","description"},
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string", description="HTML allowed; sanitized like web"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="attachments", type="array", @OA\Items(type="string", format="binary")),
     *                 @OA\Property(property="communities", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="tag_all_my_communities", type="boolean"),
     *                 @OA\Property(property="also_public_with_communities", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created (pending approval)"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        Auth::shouldUse('api');

        $this->normalizeForumArrayInputs($request);

        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'required|string|max:200000',
            'image' => 'sometimes|file|image|max:10240',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:10240',
            'communities' => 'sometimes|array',
            'communities.*' => 'integer',
            'tags' => 'sometimes|array',
            'tags.*' => 'integer|exists:tags,id',
            'tag_all_my_communities' => 'sometimes|boolean',
            'also_public_with_communities' => 'sometimes|boolean',
        ]);

        $forum = $this->forumsRepo->save($request);

        return response()->json([
            'status' => 201,
            'message' => 'Forum submitted for approval.',
            'data' => [
                'id' => $forum->id,
                'forum_title' => $forum->forum_title,
                'status' => (int) ($forum->status ?? 0),
                'is_approved' => (int) ($forum->is_approved ?? 0),
            ],
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/forums/comment",
     *     operationId="CreateForumComment",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Post comment or reply",
     *     description="`forum_id` (or legacy `id`) + `comment`; optional `parent_id` for replies. Max 300 words, 20000 chars. Multipart: optional `attachments[]` — images, PDF, audio/video, or office formats (converted to PDF on save), max 2MB each.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"comment"},
     *                 @OA\Property(property="forum_id", type="integer"),
     *                 @OA\Property(property="id", type="integer", description="Alias for forum_id"),
     *                 @OA\Property(property="parent_id", type="integer", nullable=true),
     *                 @OA\Property(property="comment", type="string"),
     *                 @OA\Property(property="attachments", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Comment created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function comment(Request $request)
    {
        Auth::shouldUse('api');

        $forumId = (int) $request->input('forum_id', $request->input('id'));
        $request->merge(['id' => $forumId]);

        $request->validate([
            'id' => 'required|integer|min:1|exists:forums,id',
            'comment' => 'required|string|max:20000',
            'parent_id' => 'nullable|integer',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:2048|mimes:jpeg,jpg,png,gif,webp,pdf,mp4,m4v,mov,avi,webm,mkv,wmv,flv,3gp,3gpp,mpeg,mpg,mp3,m4a,wav,aac,ogg,oga,opus,flac,wma,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,rtf',
        ]);

        $commentText = trim((string) $request->input('comment'));
        $wordCount = count(preg_split('/\s+/u', $commentText, -1, PREG_SPLIT_NO_EMPTY));
        if ($wordCount > 300) {
            return response()->json([
                'status' => 422,
                'message' => 'Comments are limited to 300 words.',
                'errors' => ['comment' => ['Comments are limited to 300 words.']],
            ], 422);
        }
        $request->merge(['comment' => $commentText]);

        $comment = $this->forumsRepo->save_comment($request);
        if (! $comment) {
            return response()->json(['status' => 400, 'message' => 'Could not save comment'], 400);
        }

        $comment->load(['user', 'likes']);
        $comment->refresh();
        $comment->attachments;

        return response()->json([
            'status' => 201,
            'message' => 'Comment saved successfully',
            'data' => $comment,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/forums/{id}/join",
     *     operationId="JoinForum",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Join discussion",
     *     description="Same as web `GET /forums/join?id=` — subscribes the user (idempotent).",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Joined"),
     *     @OA\Response(response=404, description="Forum not found")
     * )
     */
    public function join(Request $request, $id)
    {
        Auth::shouldUse('api');

        $id = (int) $id;
        if ($id < 1 || ! Forum::query()->whereKey($id)->exists()) {
            return response()->json(['status' => 404, 'message' => 'Forum not found'], 404);
        }

        $request->merge(['id' => $id]);
        $this->forumsRepo->join_forum($request);

        return response()->json([
            'status' => 200,
            'success' => true,
            'joined' => true,
            'forum_id' => $id,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/forums/{id}/like",
     *     operationId="ToggleForumLike",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Like or unlike forum thread",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="{ liked, count }")
     * )
     */
    public function like(Request $request, $id)
    {
        Auth::shouldUse('api');

        $id = (int) $id;
        if ($id < 1) {
            return response()->json(['error' => 'Invalid forum'], 422);
        }

        $uid = (int) $request->user()->id;
        $result = $this->forumsRepo->toggleLike($id, $uid);

        return response()->json($result, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/forums/comment/like",
     *     operationId="ToggleForumCommentLike",
     *     tags={"Forums"},
     *     security={{"bearer_token":{}}},
     *     summary="Like or unlike a comment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment_id"},
     *             @OA\Property(property="comment_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="{ liked, count }")
     * )
     */
    public function likeComment(Request $request)
    {
        Auth::shouldUse('api');

        $request->validate([
            'comment_id' => 'required|integer|min:1',
        ]);

        $uid = (int) $request->user()->id;
        $result = $this->forumsRepo->toggleCommentLike((int) $request->comment_id, $uid);

        return response()->json($result, 200);
    }

    private function forumIsLive(Forum $forum): bool
    {
        $isRejected = (int) ($forum->is_rejected ?? 0) === 1;

        return (int) ($forum->status ?? 0) === 1
            && (int) ($forum->is_approved ?? 0) === 1
            && ! $isRejected;
    }

    private function requestUserMayViewForum(Request $request, Forum $forum): bool
    {
        if ($this->forumIsLive($forum)) {
            return true;
        }

        $user = $request->user('api') ?? $request->user();
        if (! $user) {
            return false;
        }

        if ((int) $forum->created_by === (int) $user->id) {
            return true;
        }

        return $this->userIsAdmin($user);
    }

    private function userIsAdmin($user): bool
    {
        if (! $user) {
            return false;
        }
        if (function_exists('get_role')) {
            $role = get_role($user->id);
            if ($role && stripos($role->name, 'admin') !== false) {
                return true;
            }
        }
        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole('admin') || $user->hasRole('Super Admin')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return false;
    }

    private function normalizeForumArrayInputs(Request $request): void
    {
        foreach (['tags', 'communities'] as $key) {
            if (! $request->has($key)) {
                continue;
            }
            $v = $request->input($key);
            if (is_string($v)) {
                $t = trim($v);
                if ($t !== '' && isset($t[0]) && $t[0] === '[') {
                    $decoded = json_decode($t, true);
                    if (is_array($decoded)) {
                        $request->merge([$key => $decoded]);
                    }
                }
            }
        }
    }
}
