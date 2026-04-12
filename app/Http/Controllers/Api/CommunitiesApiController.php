<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityInvitation;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\Event;
use App\Repositories\CommsOfPracticeRepository;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Communities",
 *     description="Communities of practice: browse public listings, join/leave, members, linked publications & forums, invitations, and community events (mirrors web routes under /communities)."
 * )
 */
class CommunitiesApiController extends Controller
{
    protected $commsRepo;

    public function __construct(CommsOfPracticeRepository $commsRepo)
    {
        $this->commsRepo = $commsRepo;
    }

    private function userIsApprovedMember(int $communityId, int $userId): bool
    {
        return CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->exists();
    }

    private function userIsCommunityAdmin(CommunityOfPractice $community, $user): bool
    {
        if (! $user) {
            return false;
        }
        $uid = (int) $user->id;
        $isSystemAdmin = is_admin() || (method_exists($user, 'can') && $user->can('view_publications'));
        $current = CommunityOfPracticeMembers::where('community_of_practice_id', $community->id)
            ->where('user_id', $uid)
            ->where('is_approved', 1)
            ->first();

        return $isSystemAdmin
            || ($current && ($current->is_admin ?? false))
            || ((int) ($community->created_by ?? 0) === $uid);
    }

    /**
     * @OA\Get(
     *     path="/api/communities",
     *     operationId="getCommunitiesList",
     *     tags={"Communities"},
     *     summary="List public communities",
     *     description="Paginated public communities (same filters as the web directory). Unauthenticated.",
     *     @OA\Parameter(name="term", in="query", required=false, description="Search name, description, or creator", @OA\Schema(type="string", example="malaria")),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=15)),
     *     @OA\Parameter(name="coverage", in="query", required=false, description="whole_of_africa | region | country", @OA\Schema(type="string", example="country")),
     *     @OA\Parameter(name="region_id", in="query", required=false, @OA\Schema(type="integer", example=2)),
     *     @OA\Parameter(name="country_id", in="query", required=false, @OA\Schema(type="integer", example=5)),
     *     @OA\Parameter(name="organisation", in="query", required=false, @OA\Schema(type="string", example="Ministry")),
     *     @OA\Parameter(name="department", in="query", required=false, @OA\Schema(type="string", example="EPI")),
     *     @OA\Response(
     *         response=200,
     *         description="Laravel paginator payload + status/message",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Communities retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=42)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $request->merge(['rows' => $request->page_size ?? 15]);

        $communities = $this->commsRepo->get($request);
        $data = $communities->toArray() ?? [];
        $data['status'] = 200;
        $data['message'] = 'Communities retrieved successfully';
        $data['page_size'] = (int) ($data['per_page'] ?? 15);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/me",
     *     operationId="getMyCommunities",
     *     tags={"Communities"},
     *     summary="Communities I belong to",
     *     description="Approved memberships only (same idea as web /account/my-communities).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="term", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=15)),
     *     @OA\Response(response=200, description="Paginated communities"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function myCommunities(Request $request)
    {
        $userId = (int) $request->user()->id;
        $request->merge(['rows' => $request->page_size ?? 15]);

        $communities = $this->commsRepo->getByUser($userId, $request);
        $data = $communities->toArray() ?? [];
        $data['status'] = 200;
        $data['message'] = 'Your communities retrieved successfully';
        $data['page_size'] = (int) ($data['per_page'] ?? 15);
        unset($data['links'], $data['last_page_url'], $data['next_page_url'], $data['path'], $data['first_page_url'], $data['prev_page_url']);

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/invitations/accept/{token}",
     *     operationId="acceptCommunityInvitation",
     *     tags={"Communities"},
     *     summary="Accept a community invitation",
     *     description="Authenticated user must match the invitation email. Same as web /communities/accept-invitation/{token}.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="token", in="path", required=true, @OA\Schema(type="string", example="abc123token")),
     *     @OA\Response(
     *         response=200,
     *         description="Joined community",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Invitation email does not match signed-in user"),
     *     @OA\Response(response=422, description="Invalid, expired, or already used invitation")
     * )
     */
    public function acceptInvitation(Request $request, string $token)
    {
        $invitation = CommunityInvitation::where('token', $token)->first();
        if (! $invitation) {
            return response()->json(['status' => 'error', 'message' => 'Invalid invitation token'], 422);
        }

        $authEmail = strtolower(trim((string) $request->user()->email));
        $inviteEmail = strtolower(trim((string) $invitation->email));
        if ($authEmail !== $inviteEmail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sign in with the invited email address to accept this invitation.',
            ], 403);
        }

        $result = $this->commsRepo->acceptInvitation($token);
        $code = $result['status'] === 'success' ? 200 : 422;

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'community' => $result['community'] ?? null,
        ], $code);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}",
     *     operationId="getCommunityById",
     *     tags={"Communities"},
     *     summary="Get community detail (member only)",
     *     description="Requires an approved, active membership. Includes related members, forums links, publication links, tags (same shape as web detail data load).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Response(response=200, description="Community with relations"),
     *     @OA\Response(response=403, description="Not a member or restricted community"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Request $request, $id)
    {
        $id = (int) $id;
        $user = $request->user();
        $userId = (int) $user->id;

        if (! $this->userIsApprovedMember($id, $userId)) {
            return response()->json([
                'status' => 403,
                'message' => 'You must be an approved member to view this community.',
                'data' => null,
            ], 403);
        }

        $community = $this->commsRepo->find($id, true);
        if (! $community) {
            return response()->json(['status' => 404, 'message' => 'Community not found', 'data' => null], 404);
        }

        if (community_is_africa_cdc_staff_restricted($community) && ! user_email_allows_africa_cdc_staff_community($user)) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have access to this community.',
                'data' => null,
            ], 403);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Community retrieved successfully',
            'data' => $community,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{id}/join",
     *     operationId="joinCommunity",
     *     tags={"Communities"},
     *     summary="Request to join a community",
     *     description="Same as web POST /communities/join. Creates a pending membership (is_approved=0) unless already present.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Response(response=200, description="Request submitted"),
     *     @OA\Response(response=403, description="e.g. Africa CDC Staff community restriction")
     * )
     */
    public function join(Request $request, $id)
    {
        $id = (int) $id;
        $userId = (int) $request->user()->id;

        $result = $this->commsRepo->addMember($id, $userId);
        if ($result === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'This community is only available to users with an @africacdc.org email address.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Your request has been successfully submitted to the community.',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{id}/leave",
     *     operationId="leaveCommunity",
     *     tags={"Communities"},
     *     summary="Leave a community",
     *     description="Same as web POST /communities/leave.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Response(response=200, description="Left community"),
     *     @OA\Response(response=400, description="Could not leave")
     * )
     */
    public function leave(Request $request, $id)
    {
        $removed = $this->commsRepo->removeMember((int) $id, (int) $request->user()->id);

        if ($removed) {
            return response()->json(['status' => 'success', 'message' => 'You have successfully left the community.'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to leave the community.'], 400);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}/members",
     *     operationId="getCommunityMembers",
     *     tags={"Communities"},
     *     summary="List community members with details",
     *     description="Approved members only. Paginated list with optional search `q` (name, email, job title). Includes publication_count in this community and admin flags. Same payload as web GET /communities/detail/{id}/members-data.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Parameter(name="q", in="query", required=false, description="Search", @OA\Schema(type="string", example="nurse")),
     *     @OA\Response(
     *         response=200,
     *         description="items, pagination, is_community_admin",
     *         @OA\JsonContent(
     *             @OA\Property(property="items", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=20),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="has_more", type="boolean", example=true),
     *             @OA\Property(property="is_community_admin", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not a member")
     * )
     */
    public function members(Request $request, $id)
    {
        $id = (int) $id;
        $userId = (int) $request->user()->id;

        if (! $this->userIsApprovedMember($id, $userId)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $community = $this->commsRepo->find($id);
        if (! $community) {
            return response()->json(['message' => 'Community not found'], 404);
        }

        $currentMembership = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->first();
        $isSystemAdmin = is_admin() || ($request->user() && method_exists($request->user(), 'can') && $request->user()->can('view_publications'));
        $isCommunityAdmin = $isSystemAdmin
            || (bool) ($currentMembership->is_admin ?? false)
            || ((int) ($community->created_by ?? 0) === $userId);

        $page = max((int) $request->input('page', 1), 1);
        $perPage = max((int) $request->input('per_page', 20), 1);
        $search = trim((string) $request->input('q', ''));

        $payload = $this->commsRepo->approvedMembersPaginatedWithStats($id, $page, $perPage, $search, $isCommunityAdmin);

        return response()->json($payload);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}/publications",
     *     operationId="getCommunityPublications",
     *     tags={"Communities"},
     *     summary="Publications linked to this community",
     *     description="Member only. Paginated; use page_size 1–50 (default 10).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Laravel pagination JSON"),
     *     @OA\Response(response=403, description="Not a member")
     * )
     */
    public function publications(Request $request, $id)
    {
        $id = (int) $id;
        if (! $this->userIsApprovedMember($id, (int) $request->user()->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $perPage = (int) ($request->page_size ?? 10);
        $paginator = $this->commsRepo->paginatedPublicationsForCommunity($id, $perPage);
        $data = $paginator->toArray();
        $data['status'] = 200;
        $data['message'] = 'Publications retrieved successfully';

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}/forums",
     *     operationId="getCommunityForums",
     *     tags={"Communities"},
     *     summary="Forum threads linked to this community",
     *     description="Member only. Paginated; use page_size 1–50 (default 10).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Parameter(name="page_size", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Laravel pagination JSON"),
     *     @OA\Response(response=403, description="Not a member")
     * )
     */
    public function forums(Request $request, $id)
    {
        $id = (int) $id;
        if (! $this->userIsApprovedMember($id, (int) $request->user()->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $perPage = (int) ($request->page_size ?? 10);
        $paginator = $this->commsRepo->paginatedForumsForCommunity($id, $perPage);
        $data = $paginator->toArray();
        $data['status'] = 200;
        $data['message'] = 'Forum threads retrieved successfully';

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/communities/{id}/events",
     *     operationId="getCommunityEvents",
     *     tags={"Communities"},
     *     summary="List upcoming community events",
     *     description="Member only. Non-cancelled events ordered by start date. Optional limit (max 100, default 50).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", example=20)),
     *     @OA\Response(response=200, description="Array of events"),
     *     @OA\Response(response=403, description="Not a member")
     * )
     */
    public function events(Request $request, $id)
    {
        $id = (int) $id;
        if (! $this->userIsApprovedMember($id, (int) $request->user()->id)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $limit = (int) ($request->input('limit', 50));
        $events = $this->commsRepo->activeEventsForCommunity($id, $limit);

        return response()->json([
            'status' => 200,
            'message' => 'Events retrieved successfully',
            'data' => $events,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{id}/events",
     *     operationId="createCommunityEvent",
     *     tags={"Communities"},
     *     summary="Create a community event (admin)",
     *     description="Same rules as web POST /communities/detail/{id}/events — community admin, creator, or system admin.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","event_category","startdate"},
     *             @OA\Property(property="title", type="string", example="Community webinar: IPC"),
     *             @OA\Property(property="description", type="string", example="Join us for a one-hour session on infection prevention."),
     *             @OA\Property(property="event_category", type="string", example="Webinar"),
     *             @OA\Property(property="startdate", type="string", format="date-time", example="2026-05-01T14:00:00Z"),
     *             @OA\Property(property="enddate", type="string", format="date-time", nullable=true, example="2026-05-01T15:00:00Z"),
     *             @OA\Property(property="venue", type="string", nullable=true, example="Online"),
     *             @OA\Property(property="event_link", type="string", format="uri", nullable=true, example="https://meet.example.com/room/abc")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Created"),
     *     @OA\Response(response=403, description="Not allowed"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function storeCommunityEvent(Request $request, $id)
    {
        $id = (int) $id;
        $user = $request->user();
        $current = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $user->id)
            ->where('is_approved', 1)
            ->first();
        $community = $this->commsRepo->find($id);
        if (! $community) {
            return response()->json(['status' => 'error', 'message' => 'Community not found.'], 404);
        }

        if (! $this->userIsCommunityAdmin($community, $user)) {
            return response()->json(['status' => 'error', 'message' => 'Only community admins can create events.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_category' => 'required|string|max:100',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'venue' => 'nullable|string|max:255',
            'event_link' => 'nullable|url|max:255',
        ]);

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_category' => $request->event_category,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'venue' => $request->venue,
            'organized_by' => $community->community_name ?? 'Community',
            'status' => 'active',
            'event_link' => $request->event_link,
            'registration_link' => $request->event_link,
            'is_online' => ! empty($request->event_link) ? 1 : 0,
            'contact_person' => $user->name ?? null,
            'country_id' => $community->country_id ?? null,
            'community_of_practice_id' => $id,
            'created_by' => (string) $user->id,
            'updated_by' => (string) $user->id,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Community event created successfully.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{id}/invites",
     *     operationId="inviteColleaguesToCommunity",
     *     tags={"Communities"},
     *     summary="Invite colleagues by email",
     *     description="Active members may invite up to 5 emails per request (comma/space separated). Same as web POST /communities/detail/{id}/invite.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"emails"},
     *             @OA\Property(
     *                 property="emails",
     *                 type="string",
     *                 example="colleague1@who.int, colleague2@unicef.org",
     *                 description="Up to 5 addresses, separated by comma, space, or semicolon"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invitations sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="2 invitation(s) sent."),
     *             @OA\Property(property="result", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Not an active member"),
     *     @OA\Response(response=422, description="Invalid or too many emails")
     * )
     */
    public function inviteColleagues(Request $request, $id)
    {
        $id = (int) $id;
        $user = $request->user();

        $member = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $user->id)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->first();
        if (! $member) {
            return response()->json(['status' => 'error', 'message' => 'Only active community members can invite colleagues.'], 403);
        }

        $emailsInput = (string) $request->input('emails', '');
        $emails = array_values(array_unique(array_filter(array_map('trim', preg_split('/[\s,;]+/', $emailsInput)))));
        if (count($emails) === 0) {
            return response()->json(['status' => 'error', 'message' => 'Please provide at least one email.'], 422);
        }
        if (count($emails) > 5) {
            return response()->json(['status' => 'error', 'message' => 'You can invite up to 5 colleagues at a time.'], 422);
        }
        foreach ($emails as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['status' => 'error', 'message' => "Invalid email: {$email}"], 422);
            }
        }

        $result = $this->commsRepo->sendInvitationsBulk($id, $emails, (int) $user->id);

        return response()->json([
            'status' => 'success',
            'message' => ($result['sent'] ?? 0).' invitation(s) sent.',
            'result' => $result,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{id}/member-status",
     *     operationId="updateCommunityMemberStatus",
     *     tags={"Communities"},
     *     summary="Activate or deactivate a member (admin)",
     *     description="Same as web POST /communities/detail/{id}/member-status. Cannot deactivate yourself.",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"member_id","action"},
     *             @OA\Property(property="member_id", type="integer", example=55, description="community_of_practice_members.id"),
     *             @OA\Property(property="action", type="string", enum={"activate","deactivate"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=403, description="Not admin"),
     *     @OA\Response(response=422, description="Validation / cannot deactivate self")
     * )
     */
    public function updateMemberStatus(Request $request, $id)
    {
        $id = (int) $id;
        $user = $request->user();

        $request->validate([
            'member_id' => 'required|integer|exists:community_of_practice_members,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $current = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $user->id)
            ->where('is_approved', 1)
            ->first();
        $community = $this->commsRepo->find($id);
        if (! $community) {
            return response()->json(['status' => 'error', 'message' => 'Community not found.'], 404);
        }

        $canManage = $this->userIsCommunityAdmin($community, $user);
        if (! $canManage) {
            return response()->json(['status' => 'error', 'message' => 'Only community admins can update member status.'], 403);
        }

        $target = CommunityOfPracticeMembers::where('id', (int) $request->member_id)
            ->where('community_of_practice_id', $id)
            ->where('is_approved', 1)
            ->firstOrFail();

        if ((int) $target->user_id === (int) $user->id && $request->action === 'deactivate') {
            return response()->json(['status' => 'error', 'message' => 'You cannot deactivate yourself.'], 422);
        }

        $this->commsRepo->updateMemberStatus($target->id, $request->action);

        return response()->json(['status' => 'success', 'message' => 'Member status updated.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities",
     *     operationId="createCommunity",
     *     tags={"Communities"},
     *     summary="Create a new community (reserved)",
     *     description="Not exposed on default API routes; documented for future/admin use.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Community Name"),
     *             @OA\Property(property="description", type="string", example="Description of the community")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $community = $this->commsRepo->save($request);

        return response()->json([
            'status' => 201,
            'message' => 'Community created successfully',
            'data' => $community,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/communities/{id}",
     *     operationId="updateCommunity",
     *     tags={"Communities"},
     *     summary="Update community (reserved)",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $community = $this->commsRepo->find($id);

        if (! $community) {
            return response()->json([
                'status' => 404,
                'message' => 'Community not found',
                'data' => null,
            ], 404);
        }

        $community = $this->commsRepo->save($request);

        return response()->json([
            'status' => 200,
            'message' => 'Community updated successfully',
            'data' => $community,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/communities/{communityId}/members",
     *     operationId="addMemberToCommunity",
     *     tags={"Communities"},
     *     summary="Add a user to community (admin)",
     *     description="Creates a pending membership for user_id. **Community admin or system admin only** (not the same as /join).",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="communityId", in="path", required=true, @OA\Schema(type="integer", example=12)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Member row created or returned"),
     *     @OA\Response(response=403, description="Not an admin"),
     *     @OA\Response(response=404, description="Community not found")
     * )
     */
    public function addMember(Request $request, $communityId)
    {
        $community = $this->commsRepo->find((int) $communityId);
        if (! $community) {
            return response()->json(['status' => 404, 'message' => 'Community not found'], 404);
        }
        if (! $this->userIsCommunityAdmin($community, $request->user())) {
            return response()->json(['status' => 403, 'message' => 'Only community admins can add members by user id.'], 403);
        }

        $request->validate(['user_id' => 'required|integer|exists:users,id']);

        $this->commsRepo->addMember((int) $communityId, (int) $request->user_id);

        return response()->json([
            'status' => 200,
            'message' => 'Member added successfully',
            'data' => null,
        ], 200);
    }
}
