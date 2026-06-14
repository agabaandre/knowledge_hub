<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\AreasRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityInvitation;
use App\Models\CommunityComment;
use App\Models\ContentRequest;
use App\Models\Event;
use Illuminate\Support\Str;

class CommunitiesController extends Controller
{
    public const COMMUNITIES_INFINITE_ROWS = 6;

    private $commsOfPracticeRepository;
    private $areasRepo;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository, AreasRepository $areasRepo)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
        $this->areasRepo = $areasRepo;
    }

    public function index()
    {
        $request = request();
        $this->prepareCommunitiesListingRequest($request);
        $communities = $this->commsOfPracticeRepository->get($request);

        // SEO variables
        $pageTitle = 'Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $pageDescription = 'Join professional communities of practice focused on public health topics across Africa. Connect with experts, share knowledge, and collaborate on health initiatives.';
        $pageKeywords = 'communities of practice, public health communities, Africa CDC communities, health professionals, networking, collaboration, ' . (settings()->seo_keywords ?? '');
        $pageImage = settings()->logo ?? asset('assets/images/logo.png');
        $canonicalUrl = url('communities');
        $ogType = 'website';
        
        $this->commsOfPracticeRepository->attachListingMeta($communities);

        if (communities_listing_show_participants()) {
            $participantKeywords = collect($communities->items())
                ->take(8)
                ->flatMap(function ($c) {
                    return collect($c->listing_contributor_faces ?? [])->map(fn ($f) => $f['user']->name ?? '');
                })
                ->filter()
                ->unique()
                ->take(24)
                ->implode(', ');
            if ($participantKeywords !== '') {
                $pageKeywords .= ', '.$participantKeywords;
            }
        }

        $recommendedCommunities = collect();
        $userMembershipStats = null;
        if (Auth::check()) {
            $userId = (int) Auth::id();
            $userMembershipStats = $this->commsOfPracticeRepository->membershipStatsForUser($userId);
            $rawRecommended = $this->commsOfPracticeRepository->recommendedForUser(
                $userId,
                communities_listing_recommended_fetch_limit()
            );
            $this->commsOfPracticeRepository->attachListingMeta($rawRecommended);
            $recommendedCommunities = communities_listing_prepare_recommended($rawRecommended);
        }
        
        $communitiesCollectionPageSchema = $this->buildCommunitiesCollectionPageSchema(
            $pageTitle,
            $pageDescription,
            $canonicalUrl,
            $communities
        );

        return view('communities.index', compact(
            'communities',
            'recommendedCommunities',
            'userMembershipStats',
            'pageTitle',
            'pageDescription',
            'pageKeywords',
            'pageImage',
            'canonicalUrl',
            'ogType',
            'communitiesCollectionPageSchema'
        ) + [
            'communitiesInfiniteScroll' => $this->communitiesInfiniteScrollEnabled(),
        ]);
    }

    /**
     * Append the next page of community cards for infinite-scroll listing.
     */
    public function communitiesPage(Request $request)
    {
        if (! $this->communitiesInfiniteScrollEnabled()) {
            return response()->json(['ok' => false, 'error' => 'infinite_scroll_disabled'], 403);
        }

        $this->prepareCommunitiesListingRequest($request);
        $request->merge(['rows' => self::COMMUNITIES_INFINITE_ROWS]);
        $communities = $this->commsOfPracticeRepository->get($request);
        $this->commsOfPracticeRepository->attachListingMeta($communities);

        $page = (int) $communities->currentPage();
        $perPage = (int) $communities->perPage();
        $listOffset = max(0, ($page - 1) * $perPage);
        $loadedCount = min($communities->total(), $listOffset + $communities->count());

        return response()->json([
            'ok' => true,
            'html' => view('communities.partials.community_list_items', [
                'communities' => $communities,
            ])->render(),
            'current_page' => $page,
            'last_page' => (int) $communities->lastPage(),
            'has_more' => $communities->hasMorePages(),
            'total' => (int) $communities->total(),
            'loaded_count' => $loadedCount,
        ]);
    }

    protected function prepareCommunitiesListingRequest(Request $request): void
    {
        $request->merge(['admin' => false]);

        if ($this->communitiesInfiniteScrollEnabled()) {
            $request->merge([
                'page' => max(1, (int) $request->input('page', 1)),
                'rows' => self::COMMUNITIES_INFINITE_ROWS,
            ]);
        }
    }

    protected function communitiesInfiniteScrollEnabled(): bool
    {
        return (settings()->communities_pagination_mode ?? 'infinite_scroll') === 'infinite_scroll';
    }

    public function myCommunities()
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }
        
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->guest(route('login'));
        }
        
        $communities = $this->commsOfPracticeRepository->getByUser($userId, request());
        $this->commsOfPracticeRepository->attachListingMeta($communities);
        $recommendedCommunities = collect();
        $userMembershipStats = $this->commsOfPracticeRepository->membershipStatsForUser((int) $userId);

        $pageKeywords = 'communities of practice, my communities, ' . (settings()->seo_keywords ?? '');
        if (communities_listing_show_participants()) {
            $participantKeywords = collect($communities->items())
                ->take(8)
                ->flatMap(function ($c) {
                    return collect($c->listing_contributor_faces ?? [])->map(fn ($f) => $f['user']->name ?? '');
                })
                ->filter()
                ->unique()
                ->take(24)
                ->implode(', ');
            if ($participantKeywords !== '') {
                $pageKeywords .= ', '.$participantKeywords;
            }
        }

        $pageTitle = 'Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $pageDescription = 'Your communities of practice on ' . (settings()->site_name ?? 'the Knowledge Hub') . '.';
        $canonicalUrl = url('account/my-communities');
        $communitiesCollectionPageSchema = $this->buildCommunitiesCollectionPageSchema(
            $pageTitle,
            $pageDescription,
            $canonicalUrl,
            $communities
        );

        return view('communities.index', compact(
            'communities',
            'recommendedCommunities',
            'userMembershipStats',
            'communitiesCollectionPageSchema',
            'pageTitle',
            'pageDescription',
            'canonicalUrl',
            'pageKeywords'
        ));
    }

    public function join(Request $request)
    {
        if (!Auth::check()) {
            $request->session()->put('url.intended', url()->previous() ?: route('community.index'));
            return response()->json(['redirect' => url('/login')]);
        }

        $result = $this->commsOfPracticeRepository->addMember($request->community_id, Auth::id());

        if ($result === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'This community is only available to users with an @africacdc.org email address.',
            ], 403);
        }

        $community = \App\Models\CommunityOfPractice::find((int) $request->community_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Your request has been submitted to the community.',
            'redirect' => $community ? community_detail_url($community) : null,
        ]);
    }

    public function leave(Request $request)
    {
        if (!Auth::check()) {
            $request->session()->put('url.intended', url()->previous() ?: route('community.index'));
            return response()->json(['redirect' => url('/login')]);
        }

        $result = $this->commsOfPracticeRepository->removeMember($request->community_id, Auth::id());

        if ($result) {
            return response()->json(['status' => 'success', 'message' => 'You have successfully left the community.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to leave the community.']);
        }
    }

    public function acceptInvitation($token)
    {
        if (!Auth::check()) {
            session()->put('invitation_token', $token);
            return redirect()->guest(route('login'))->with('info', 'Please login to accept the invitation.');
        }

        $invitation = CommunityInvitation::where('token', $token)->first();
        if (!$invitation) {
            return redirect()->route('community.index')
                ->with('alert-danger', 'Invalid invitation token');
        }

        $authEmail = strtolower(trim((string) (Auth::user()->email ?? '')));
        $inviteEmail = strtolower(trim((string) $invitation->email));
        if ($authEmail !== $inviteEmail) {
            return redirect()->route('community.index')
                ->with('alert-danger', 'Sign in with the invited email address to accept this invitation.');
        }

        $communityId = (int) ($invitation->community_of_practice_id ?? 0);

        $result = $this->commsOfPracticeRepository->acceptInvitation($token);
        if (!$communityId && !empty($result['community']) && !empty($result['community']->id)) {
            $communityId = (int) $result['community']->id;
        }

        if ($result['status'] === 'success') {
            if ($communityId > 0) {
                return redirect()->to(community_detail_url($communityId))
                    ->with('alert-success', $result['message']);
            }

            return redirect()->route('community.index')
                ->with('alert-success', $result['message']);
        }

        if ($communityId > 0) {
            return redirect()->to(community_detail_url($communityId))
                ->with('alert-danger', $result['message']);
        }

        return redirect()->route('community.index')
            ->with('alert-danger', $result['message']);
    }

    public function detail($key)
    {
        $community = ctype_digit((string) $key)
            ? $this->commsOfPracticeRepository->find((int) $key)
            : $this->commsOfPracticeRepository->findBySlug((string) $key);

        if (!$community) {
            return redirect()->route('community.index')->with('error', 'Community not found.');
        }

        if (ctype_digit((string) $key) && seo_friendly_urls_enabled() && ! empty($community->slug)) {
            return redirect()->to(community_detail_url($community), 301);
        }

        $id = (int) $community->id;

        if (community_is_africa_cdc_staff_restricted($community)) {
            if (! Auth::check() || ! user_email_allows_africa_cdc_staff_community(Auth::user())) {
                return redirect()->route('community.index')->with('error', 'You do not have access to this community.');
            }
        }

        // Load counts for the community
        $community->loadCount(['communityPublications', 'communityForums', 'approvedMembers']);
        $community->loadMissing('region', 'country');
        $this->commsOfPracticeRepository->attachListingMeta(collect([$community]));

        $userId = Auth::id();
        $isCommunityMember = false;
        $isPendingMember = false;

        if ($userId) {
            $membership = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
                ->where('user_id', $userId)
                ->first();

            if ($membership && (int) $membership->is_approved === 1 && (int) $membership->is_active === 1) {
                $isCommunityMember = true;
            } elseif ($membership && (int) $membership->is_approved === 0) {
                $isPendingMember = true;
            }
        }

        $memberCountForSeo = (int) ($community->approved_members_count ?? $community->members_count ?? 0);
        $canonicalUrl = community_detail_url($community);
        $communityOrganizationLd = $this->buildCommunityDetailOrganizationSchema($community, $canonicalUrl, $memberCountForSeo);

        if (! $isCommunityMember) {
            return view('communities.detail', [
                'community' => $community,
                'isCommunityMember' => false,
                'isPendingMember' => $isPendingMember,
                'publications' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'pendingCommunityContentRequests' => collect(),
                'processedCommunityContentRequests' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'forums' => collect(),
                'otherCommunities' => collect(),
                'badgeTypes' => collect(),
                'isCommunityAdmin' => false,
                'communityEvents' => collect(),
                'communityWallPosts' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'communityComments' => collect(),
                'communityForums' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'hasRecentWallPosts' => false,
                'activeTab' => 'publications',
                'openWallPostForm' => false,
                'communityOrganizationLd' => $communityOrganizationLd,
            ]);
        }

        // Get community publications
        $publicationIds = \App\Models\PublicationCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('publication_id');
        $publications = \App\Models\Publication::whereIn('id', $publicationIds)
            ->with(['author.user', 'sub_theme.theme', 'data_category', 'sub_category', 'favourited', 'comments.user', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page');
        $publications->appends(['tab' => 'publications']);

        $wallBaseQuery = CommunityComment::query()
            ->where('community_of_practice_id', $id)
            ->whereNull('parent_id')
            ->where('status', 'approved');

        $hasRecentWallPosts = (clone $wallBaseQuery)
            ->where('created_at', '>=', now()->subWeek())
            ->exists();

        $communityWallPosts = (clone $wallBaseQuery)
            ->with(['user', 'likes', 'replies.user', 'replies.likes'])
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'wall_page');
        $communityWallPosts->appends(['tab' => 'wall']);

        $allowedTabs = ['wall', 'publications', 'forums', 'processed'];
        $activeTab = request()->query('tab');
        if (! in_array($activeTab, $allowedTabs, true)) {
            if (request()->filled('pcr_page')) {
                $activeTab = 'processed';
            } elseif ($hasRecentWallPosts) {
                $activeTab = 'wall';
            } else {
                $activeTab = 'publications';
            }
        }

        $openWallPostForm = request()->boolean('post');

        // Content requests referred to this community (hub workflow), including multi-community referrals
        $pendingCommunityContentRequests = ContentRequest::query()
            ->whereNull('processed_at')
            ->whereNotNull('referred_at')
            ->where(function ($q) use ($id) {
                $q->whereHas('referralTargets', fn ($t) => $t->where('community_of_practice_id', $id))
                    ->orWhere(function ($q2) use ($id) {
                        $q2->whereIn('referral_type', ['community', 'mixed'])
                            ->where('referred_to_community_id', $id);
                    });
            })
            ->with(['country', 'referredByUser', 'referralTargets'])
            ->orderByDesc('referred_at')
            ->orderByDesc('created_at')
            ->get();

        $processedCommunityContentRequests = ContentRequest::query()
            ->whereNotNull('processed_at')
            ->where(function ($q) use ($id) {
                $q->whereHas('referralTargets', fn ($t) => $t->where('community_of_practice_id', $id))
                    ->orWhere(function ($q2) use ($id) {
                        $q2->whereIn('referral_type', ['community', 'mixed'])
                            ->where('referred_to_community_id', $id);
                    });
            })
            ->with(['country', 'processedBy', 'referralTargets'])
            ->orderByDesc('processed_at')
            ->paginate(10, ['*'], 'pcr_page');
        $processedCommunityContentRequests->appends(['tab' => 'processed']);
        
        // SEO variables
        $pageTitle = ($community->community_name ?? 'Community') . ' - Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $pageDescription = \Illuminate\Support\Str::limit(strip_tags($community->description ?? ''), 160) ?: ($community->community_name . ' - A professional community of practice focused on public health topics.');
        $pageKeywords = 'community of practice, ' . ($community->community_name ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
        $pageImage = settings()->logo ?? asset('assets/images/logo.png');
        $canonicalUrl = community_detail_url($community);
        $ogType = 'profile';

        // Get forum engagements (forums in this community)
        $forumIds = \App\Models\ForumCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('forum_id');
        $forums = \App\Models\Forum::whereIn('id', $forumIds)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $communityForums = \App\Models\Forum::whereIn('id', $forumIds)
            ->with(['user', 'tags'])
            ->withCount(['comments', 'likes'])
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'forum_page');
        $communityForums->appends(['tab' => 'forums']);

        // Get other communities user belongs to
        $otherCommunityIds = \App\Models\CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('community_of_practice_id', '!=', $id)
            ->where('is_approved', 1)
            ->pluck('community_of_practice_id');
        $otherCommunities = \App\Models\CommunityOfPractice::whereIn('id', $otherCommunityIds)
            ->withCount(['communityPublications', 'communityForums', 'approvedMembers'])
            ->limit(5)
            ->get();
        if (! user_email_allows_africa_cdc_staff_community(Auth::user())) {
            $otherCommunities = $otherCommunities->filter(function ($c) {
                return ! community_is_africa_cdc_staff_restricted($c);
            })->values();
        }

        $myMembership = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->first();
        $isSystemAdmin = is_admin() || (auth()->user() && method_exists(auth()->user(), 'can') && auth()->user()->can('view_publications'));
        $isCommunityAdmin = $isSystemAdmin || (bool) ($myMembership->is_admin ?? false) || ((int) ($community->created_by ?? 0) === (int) $userId);

        $communityEvents = Event::where('community_of_practice_id', $id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('startdate', 'asc')
            ->limit(10)
            ->get();

        // Get all badge types for displaying requirements
        $badgeTypes = \App\Models\BadgeType::getAllBadgesInOrder();

        return view('communities.detail', compact(
            'community',
            'publications',
            'pendingCommunityContentRequests',
            'processedCommunityContentRequests',
            'forums',
            'communityForums',
            'otherCommunities',
            'badgeTypes',
            'isCommunityAdmin',
            'communityEvents',
            'communityWallPosts',
            'hasRecentWallPosts',
            'activeTab',
            'openWallPostForm',
            'communityOrganizationLd'
        ) + [
            'isCommunityMember' => true,
            'isPendingMember' => false,
            'communityComments' => $communityWallPosts,
        ]);
    }

    /**
     * JSON-LD CollectionPage for communities index / my-communities (includes highlighted participants per community).
     */
    private function buildCommunitiesCollectionPageSchema(string $pageTitle, string $pageDescription, string $canonicalUrl, $communities): array
    {
        if ($communities instanceof \Illuminate\Pagination\LengthAwarePaginator || $communities instanceof \Illuminate\Pagination\Paginator) {
            $items = $communities->items();
        } elseif ($communities instanceof \Illuminate\Support\Collection) {
            $items = $communities->all();
        } elseif (is_array($communities)) {
            $items = $communities;
        } else {
            $items = [];
        }

        $faceLimit = communities_listing_show_participants() ? communities_listing_max_faces() : 0;

        $itemListElement = [];
        foreach (array_slice($items, 0, 10) as $index => $c) {
            $members = [];
            foreach (collect($c->listing_contributor_faces ?? [])->take($faceLimit) as $face) {
                $u = $face['user'];
                $row = ['@type' => 'Person', 'name' => $u->name];
                $jt = trim((string) ($u->job_title ?? ''));
                if ($jt !== '') {
                    $row['jobTitle'] = $jt;
                }
                $members[] = $row;
            }
            $org = [
                '@type' => 'Organization',
                'name' => $c->community_name,
                'url' => community_detail_url($c),
                'description' => Str::limit(strip_tags($c->description ?? ''), 200),
            ];
            if ($members !== []) {
                $org['member'] = $members;
            }
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => $org,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageTitle,
            'description' => strip_tags($pageDescription),
            'url' => $canonicalUrl,
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListElement' => $itemListElement,
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Communities', 'item' => $canonicalUrl],
                ],
            ],
        ];
    }

    /**
     * JSON-LD Organization for community detail (highlighted members from settings).
     */
    private function buildCommunityDetailOrganizationSchema(\App\Models\CommunityOfPractice $community, string $canonicalUrl, int $memberCount): array
    {
        $faceLimit = communities_listing_show_participants() ? communities_listing_max_faces() : 0;
        $members = [];
        foreach (collect($community->listing_contributor_faces ?? [])->take($faceLimit) as $face) {
            $u = $face['user'];
            $p = ['@type' => 'Person', 'name' => $u->name];
            $jt = trim((string) ($u->job_title ?? ''));
            if ($jt !== '') {
                $p['jobTitle'] = $jt;
            }
            $members[] = $p;
        }

        $ld = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $community->community_name ?? 'Community',
            'description' => Str::limit(strip_tags($community->description ?? ''), 300),
            'url' => $canonicalUrl,
            'memberOf' => [
                '@type' => 'Organization',
                'name' => settings()->site_name ?? 'Africa CDC Knowledge Hub',
            ],
            'numberOfMembers' => $memberCount,
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Communities', 'item' => url('communities')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $community->community_name ?? 'Community', 'item' => $canonicalUrl],
                ],
            ],
        ];

        if ($members !== []) {
            $ld['member'] = $members;
        }

        $region = $community->region;
        if ($region) {
            $rn = trim((string) ($region->region_name ?? $region->name ?? ''));
            if ($rn !== '') {
                $ld['areaServed'] = [
                    '@type' => 'Place',
                    'name' => $rn,
                ];
            }
        }

        return $ld;
    }

    public function membersData(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id();
        $isMember = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->exists();
        if (!$isMember) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $community = $this->commsOfPracticeRepository->find($id);
        $currentMembership = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->first();
        $isSystemAdmin = is_admin() || (auth()->user() && method_exists(auth()->user(), 'can') && auth()->user()->can('view_publications'));
        $isCommunityAdmin = $isSystemAdmin || (bool) ($currentMembership->is_admin ?? false) || ((int) ($community->created_by ?? 0) === (int) $userId);

        $page = max((int) $request->input('page', 1), 1);
        $perPage = max((int) $request->input('per_page', 20), 1);
        $search = trim((string) $request->input('q', ''));

        $payload = $this->commsOfPracticeRepository->approvedMembersPaginatedWithStats(
            (int) $id,
            $page,
            $perPage,
            $search,
            $isCommunityAdmin
        );

        return response()->json($payload);
    }

    public function inviteColleagues(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login first.'], 401);
        }

        $member = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', Auth::id())
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->first();
        if (!$member) {
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
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['status' => 'error', 'message' => "Invalid email: {$email}"], 422);
            }
        }

        $result = $this->commsOfPracticeRepository->sendInvitationsBulk((int) $id, $emails, Auth::id());
        return response()->json(['status' => 'success', 'message' => ($result['sent'] ?? 0) . ' invitation(s) sent.', 'result' => $result]);
    }

    public function updateMemberStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login first.'], 401);
        }

        $request->validate([
            'member_id' => 'required|integer|exists:community_of_practice_members,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $current = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', Auth::id())
            ->where('is_approved', 1)
            ->first();
        $community = $this->commsOfPracticeRepository->find($id);
        $isSystemAdmin = is_admin() || (auth()->user() && method_exists(auth()->user(), 'can') && auth()->user()->can('view_publications'));
        $canManage = $isSystemAdmin || ($current && ($current->is_admin ?? false)) || ((int) ($community->created_by ?? 0) === (int) Auth::id());
        if (!$canManage) {
            return response()->json(['status' => 'error', 'message' => 'Only community admins can update member status.'], 403);
        }

        $target = CommunityOfPracticeMembers::where('id', (int) $request->member_id)
            ->where('community_of_practice_id', (int) $id)
            ->where('is_approved', 1)
            ->firstOrFail();
        if ((int) $target->user_id === (int) Auth::id() && $request->action === 'deactivate') {
            return response()->json(['status' => 'error', 'message' => 'You cannot deactivate yourself.'], 422);
        }

        $this->commsOfPracticeRepository->updateMemberStatus($target->id, $request->action);
        return response()->json(['status' => 'success', 'message' => 'Member status updated.']);
    }

    public function createCommunityEvent(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login first.'], 401);
        }

        $current = CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', Auth::id())
            ->where('is_approved', 1)
            ->first();
        $community = $this->commsOfPracticeRepository->find($id);
        $isSystemAdmin = is_admin() || (auth()->user() && method_exists(auth()->user(), 'can') && auth()->user()->can('view_publications'));
        $canManage = $isSystemAdmin || ($current && ($current->is_admin ?? false)) || ((int) ($community->created_by ?? 0) === (int) Auth::id());
        if (!$canManage) {
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
            'is_online' => !empty($request->event_link) ? 1 : 0,
            'contact_person' => Auth::user()->name ?? null,
            'country_id' => $community->country_id ?? null,
            'community_of_practice_id' => (int) $id,
            'created_by' => (string) Auth::id(),
            'updated_by' => (string) Auth::id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Community event created successfully.']);
    }

    public function comment(Request $request, $id)
    {
        if (! auth()->check()) {
            abort(403, 'You must be logged in to comment.');
        }

        if (! $this->commsOfPracticeRepository->assertUserIsCommunityMember((int) $id, (int) auth()->id())) {
            abort(403, 'Only active community members can comment.');
        }

        $request->merge(['id' => (int) $id]);

        $request->validate([
            'comment' => 'required|string|max:20000',
            'parent_id' => 'nullable|integer',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:2048|mimes:jpeg,jpg,png,gif,webp,pdf,mp4,m4v,mov,avi,webm,mkv,wmv,flv,3gp,3gpp,mpeg,mpg,mp3,m4a,wav,aac,ogg,oga,opus,flac,wma,doc,docx,xls,xlsx,ppt,pptx,odt,ods,odp,rtf',
        ]);

        $commentText = trim((string) $request->input('comment'));
        $wordCount = count(preg_split('/\s+/u', $commentText, -1, PREG_SPLIT_NO_EMPTY));
        if ($wordCount > 300) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Comments are limited to 300 words.',
                ], 422);
            }

            return back()->withErrors(['comment' => 'Comments are limited to 300 words.'])->withInput();
        }

        $request->merge(['comment' => $commentText]);

        try {
            $comment = $this->commsOfPracticeRepository->saveCommunityComment($request);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            $comment->refresh();
            $comment->load(['user', 'likes', 'replies.user', 'replies.likes']);
            $comment->attachments;

            return response()->json([
                'success' => true,
                'message' => 'Comment saved successfully',
                'comment' => $comment,
                'comment_html' => view('communities.partials.community_comment_item', [
                    'comment' => $comment,
                    'community' => \App\Models\CommunityOfPractice::findOrFail((int) $id),
                    'isCommunityMember' => true,
                    'isReply' => ! empty($comment->parent_id),
                ])->render(),
            ]);
        }

        $message = ($comment) ? 'Comment saved successfully' : 'Request failed try again';

        return back()->with([
            'alert_class' => ($comment) ? 'success' : 'danger',
            'message' => $message,
            'alert' => $message,
            'status' => 200,
        ]);
    }

    public function commentLike(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['error' => 'Please login to like'], 401);
        }

        $request->validate([
            'comment_id' => 'required|integer',
        ]);

        try {
            $result = $this->commsOfPracticeRepository->toggleCommunityCommentLike($request->comment_id);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        return response()->json($result);
    }
}
