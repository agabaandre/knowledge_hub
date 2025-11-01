<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use Illuminate\Support\Facades\Auth;

class CommunitiesController extends Controller
{
    private $commsOfPracticeRepository;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
    }

    public function index()
    {
        $communities = $this->commsOfPracticeRepository->get(request());
        return view('communities.index', compact('communities'));
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

        // Get community members with job titles and badges
        $members = \App\Models\CommunityOfPracticeMembers::where('community_of_practice_id', $id)
            ->where('is_approved', 1)
            ->with('user')
            ->get()
            ->map(function($member) use ($id) {
                // Get user's badges for this community
                $badges = \App\Models\UserBadge::getUserBadgesForCommunity($member->user_id, $id);
                
                return [
                    'id' => $member->user_id,
                    'name' => $member->user->name ?? 'Unknown',
                    'job_title' => $member->user->job_title ?? 'Not specified',
                    'email' => $member->user->email ?? '',
                    'badges' => $badges,
                ];
            });

        // Get all badge types for displaying requirements
        $badgeTypes = \App\Models\BadgeType::getAllBadgesInOrder();

        return view('communities.detail', compact(
            'community',
            'publications',
            'forums',
            'otherCommunities',
            'members',
            'badgeTypes'
        ));
    }
}
