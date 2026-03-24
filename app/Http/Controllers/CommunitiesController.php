<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\AreasRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CommunityInvitation;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class CommunitiesController extends Controller
{
    private $commsOfPracticeRepository;
    private $areasRepo;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository, AreasRepository $areasRepo)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
        $this->areasRepo = $areasRepo;
    }

    public function index()
    {
        // Pass admin=false to ensure only public communities are shown
        $request = request();
        $request->merge(['admin' => false]);
        $communities = $this->commsOfPracticeRepository->get($request);
        
        // Get regions and countries for filter dropdowns
        $regions = $this->areasRepo->regions()->load('countries');
        $countries = \App\Models\Country::where('region_id', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
        
        // Get unique organisations and departments for filter dropdowns
        $organisations = \App\Models\CommunityOfPractice::where('is_public', 1)
            ->whereNotNull('organisation')
            ->distinct()
            ->orderBy('organisation', 'asc')
            ->pluck('organisation')
            ->filter()
            ->values();
        
        $departments = \App\Models\CommunityOfPractice::where('is_public', 1)
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department', 'asc')
            ->pluck('department')
            ->filter()
            ->values();
        
        // SEO variables
        $pageTitle = 'Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $pageDescription = 'Join professional communities of practice focused on public health topics across Africa. Connect with experts, share knowledge, and collaborate on health initiatives.';
        $pageKeywords = 'communities of practice, public health communities, Africa CDC communities, health professionals, networking, collaboration, ' . (settings()->seo_keywords ?? '');
        $pageImage = settings()->logo ?? asset('assets/images/logo.png');
        $canonicalUrl = url('communities');
        $ogType = 'website';
        
        return view('communities.index', compact('communities', 'regions', 'countries', 'organisations', 'departments', 'pageTitle', 'pageDescription', 'pageKeywords', 'pageImage', 'canonicalUrl', 'ogType'));
    }

    public function myCommunities()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }
        
        $communities = $this->commsOfPracticeRepository->getByUser($userId, request());
        return view('communities.index', compact('communities'));
    }

    public function join(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['redirect' => url('/login')]);
        }

        $result = $this->commsOfPracticeRepository->addMember($request->community_id, Auth::id());

        return response()->json(['status' => 'success', 'message' => 'You request has been successfully submited to the community.']);
    }

    public function leave(Request $request)
    {
        if (!Auth::check()) {
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
            return redirect()->route('login')->with('info', 'Please login to accept the invitation.');
        }

        $result = $this->commsOfPracticeRepository->acceptInvitation($token);

        if ($result['status'] === 'success') {
            return redirect()->route('community.index')
                ->with('alert-success', $result['message']);
        }

        return redirect()->route('community.index')
            ->with('alert-danger', $result['message']);
    }

    public function detail($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        // Get the community
        $community = $this->commsOfPracticeRepository->find($id);
        if (!$community) {
            return redirect()->route('account.my-communities')->with('error', 'Community not found.');
        }

        // Load counts for the community
        $community->loadCount(['communityPublications', 'communityForums', 'approvedMembers']);

        // Check if user is a member
        $isMember = \App\Models\CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('user_id', $userId)
            ->where('is_approved', 1)
            ->where('is_active', 1)
            ->exists();

        if (!$isMember) {
            return redirect()->route('account.my-communities')->with('error', 'You are not a member of this community.');
        }

        // Get community publications
        $publicationIds = \App\Models\PublicationCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('publication_id');
        $publications = \App\Models\Publication::whereIn('id', $publicationIds)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // SEO variables
        $pageTitle = ($community->community_name ?? 'Community') . ' - Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $pageDescription = \Illuminate\Support\Str::limit(strip_tags($community->description ?? ''), 160) ?: ($community->community_name . ' - A professional community of practice focused on public health topics.');
        $pageKeywords = 'community of practice, ' . ($community->community_name ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
        $pageImage = settings()->logo ?? asset('assets/images/logo.png');
        $canonicalUrl = url('communities/detail/' . $community->id);
        $ogType = 'profile';

        // Get forum engagements (forums in this community)
        $forumIds = \App\Models\ForumCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('forum_id');
        $forums = \App\Models\Forum::whereIn('id', $forumIds)
            ->with('user')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Get other communities user belongs to
        $otherCommunityIds = \App\Models\CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('community_of_practice_id', '!=', $id)
            ->where('is_approved', 1)
            ->pluck('community_of_practice_id');
        $otherCommunities = \App\Models\CommunityOfPractice::whereIn('id', $otherCommunityIds)
            ->withCount(['communityPublications', 'communityForums', 'approvedMembers'])
            ->limit(5)
            ->get();

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

        // Members list: ranked by highest publications in this community first, paginated (20/page)
        $memberSearch = trim((string) request('member_search', ''));
        $membersQuery = DB::table('community_of_practice_members as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->leftJoin('publication_community_of_practices as pcp', function ($join) {
                $join->on('pcp.community_of_practice_id', '=', 'm.community_of_practice_id');
            })
            ->leftJoin('publication as p', function ($join) {
                $join->on('p.id', '=', 'pcp.publication_id')
                     ->on('p.user_id', '=', 'm.user_id');
            })
            ->where('m.community_of_practice_id', (int) $id)
            ->where('m.is_approved', 1)
            ->select(
                'm.id as membership_id',
                'm.user_id',
                'm.is_active',
                'm.is_admin',
                'u.name',
                'u.email',
                'u.job_title',
                DB::raw('COUNT(DISTINCT p.id) as publication_count')
            )
            ->groupBy('m.id', 'm.user_id', 'm.is_active', 'm.is_admin', 'u.name', 'u.email', 'u.job_title');

        if ($memberSearch !== '') {
            $membersQuery->where(function ($q) use ($memberSearch) {
                $q->where('u.name', 'like', '%' . $memberSearch . '%')
                    ->orWhere('u.email', 'like', '%' . $memberSearch . '%')
                    ->orWhere('u.job_title', 'like', '%' . $memberSearch . '%');
            });
        }

        $members = $membersQuery
            ->orderBy('publication_count', 'desc')
            ->orderBy('u.name', 'asc')
            ->paginate(20, ['*'], 'members_page')
            ->appends(request()->only(['member_search']));

        // Get all badge types for displaying requirements
        $badgeTypes = \App\Models\BadgeType::getAllBadgesInOrder();

        return view('communities.detail', compact(
            'community',
            'publications',
            'forums',
            'otherCommunities',
            'badgeTypes',
            'isCommunityAdmin',
            'communityEvents',
            'members'
        ));
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
        $isCommunityAdmin = (bool) ($currentMembership->is_admin ?? false) || ((int) ($community->created_by ?? 0) === (int) $userId);

        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 20);
        if ($length <= 0) {
            $length = 20;
        }
        $search = trim((string) $request->input('search.value', ''));

        $base = DB::table('community_of_practice_members as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->leftJoin('publication as p', 'p.user_id', '=', 'u.id')
            ->leftJoin('publication_community_of_practices as pcp', function ($join) {
                $join->on('pcp.publication_id', '=', 'p.id');
                $join->on('pcp.community_of_practice_id', '=', 'm.community_of_practice_id');
            })
            ->where('m.community_of_practice_id', (int) $id)
            ->where('m.is_approved', 1)
            ->groupBy('m.id', 'm.user_id', 'm.is_active', 'm.is_admin', 'u.name', 'u.email', 'u.job_title')
            ->select(
                'm.id as membership_id',
                'm.user_id',
                'm.is_active',
                'm.is_admin',
                'u.name',
                'u.email',
                'u.job_title',
                DB::raw('COUNT(DISTINCT pcp.publication_id) as publication_count')
            );

        $recordsTotal = DB::table('community_of_practice_members as m')
            ->where('m.community_of_practice_id', (int) $id)
            ->where('m.is_approved', 1)
            ->count();

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('u.name', 'like', '%' . $search . '%')
                    ->orWhere('u.email', 'like', '%' . $search . '%')
                    ->orWhere('u.job_title', 'like', '%' . $search . '%');
            });
        }

        $recordsFiltered = DB::table(DB::raw('(' . $base->toSql() . ') as x'))
            ->mergeBindings($base)
            ->count();

        $rows = $base
            ->orderBy('publication_count', 'desc')
            ->orderBy('name', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $status = ((int) $row->is_active === 1) ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
            $role = ((int) $row->is_admin === 1)
                ? '<span class="badge text-light" style="background-color: ' . e((string) (settings()->primary_color ?? '#119A48')) . ';">Admin</span>'
                : '<span class="badge badge-secondary">Member</span>';
            $actions = '-';
            if ($isCommunityAdmin && (int) $row->user_id !== (int) $userId) {
                if ((int) $row->is_active === 1) {
                    $actions = '<button class="btn btn-sm btn-outline-danger js-member-toggle" data-member-id="' . (int) $row->membership_id . '" data-action="deactivate">Mark inactive</button>';
                } else {
                    $actions = '<button class="btn btn-sm btn-outline-success js-member-toggle" data-member-id="' . (int) $row->membership_id . '" data-action="activate">Mark active</button>';
                }
            }

            $data[] = [
                'name' => e((string) $row->name),
                'job_title' => e((string) ($row->job_title ?: 'Not specified')),
                'email' => e((string) $row->email),
                'publications' => (int) $row->publication_count,
                'role' => $role,
                'status' => $status,
                'actions' => $actions,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
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
        $canManage = ($current && ($current->is_admin ?? false)) || ((int) ($community->created_by ?? 0) === (int) Auth::id());
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
}
