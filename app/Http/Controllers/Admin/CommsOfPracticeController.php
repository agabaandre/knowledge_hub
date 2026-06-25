<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\AreasRepository;
use App\Http\Controllers\Controller;
use App\Models\BadgeType;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use App\Models\UserBadge;
use App\Models\User;
use App\Services\UITableService;
use App\Support\ContentModeration;
use Illuminate\Support\Facades\DB;

class CommsOfPracticeController extends Controller
{
    private $commsOfPracticeRepository,$uiTableService,$areasRepo;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository, UITableService $uiTableService, AreasRepository $areasRepo)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
        $this->uiTableService  = $uiTableService;
        $this->areasRepo = $areasRepo;
    }

    public function index(Request $request){

        if ($request->boolean('datatable')) {
            return response()->json($this->commsOfPracticeRepository->adminCommunitiesDatatable($request));
        }

        $col = array();
        $col["title"]    = "Id"; 
        $col["name"]     = "id"; 
        $col["width"]    = "30"; 
        $col["editable"] = false;
        $col["hidden"]   = true;
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Community"; 
        $col["name"]     = "community_name"; 
        $col["width"]    = "30"; 
        $col["editable"] = true;
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Is Active"; 
        $col["name"]     = "is_active"; 
        $col["width"]    = "10"; 
        $col["editable"] = true;
        $col["edittype"] = "select";
        $col["editoptions"] = array("value"=>"1:Yes;0:No");
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Created By"; 
        $col["name"]     = "created_by"; 
        $col["width"]    = "10"; 
        $col["editable"] = true;
        //$col["hidden"]   = true;
        $col["edittype"] = "select";
        $col["editoptions"] = array("value"=>current_user()->id.":".current_user()->name);
        $cols[] = $col;

        // Get communities with pending member counts (admin can see all, including non-public)
        $request->merge(['admin' => true]);
        
        // Pass regions data for chained dropdowns
        $data['regions'] = $this->areasRepo->regions()->load('countries');
        
        $data['search']    = (Object) $request->all();
        $sql = "SELECT c.id,community_name,description,is_active,u.name as created_by FROM community_of_practices c left join users u on u.id=c.created_by";
        $data['uitable'] = $this->uiTableService->get_ui_table("community_of_practices",$cols,$sql);
        
        // Count pending member approvals for notification bell
        $data['pending_member_approvals_count'] = \App\Models\CommunityOfPracticeMembers::where('is_approved', 0)
            ->count();

        // Simple overview stats for admin communities page
        $data['total_communities_count'] = \App\Models\CommunityOfPractice::count();
        $data['active_communities_count'] = \App\Models\CommunityOfPractice::where('is_active', 1)->count();
        $data['public_communities_count'] = \App\Models\CommunityOfPractice::where('is_public', 1)->count();
        $data['approved_memberships_count'] = \App\Models\CommunityOfPracticeMembers::where('is_approved', 1)
            ->where('is_active', 1)
            ->count();
        
        // Get communities with pending member approvals
        $pendingMembers = \App\Models\CommunityOfPracticeMembers::where('is_approved', 0)
            ->with(['community', 'user'])
            ->get()
            ->groupBy('community_of_practice_id');
        
        $data['communities_with_pending'] = collect();
        foreach ($pendingMembers as $communityId => $members) {
            $community = \App\Models\CommunityOfPractice::find($communityId);
            if ($community) {
                $data['communities_with_pending']->push([
                    'community' => $community,
                    'pending_count' => $members->count(),
                    'members' => $members->take(3) // Get first 3 pending members
                ]);
            }
        }
        
        return view('admin.commsofpractice.index',$data);
    }

  
    public function store(Request $request){

        $saved = $this->commsOfPracticeRepository->save($request);

        if($saved):
            $data = ['message'=>'Comunity saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){
        if (!auth()->user() || !auth()->user()->can('delete_meta_data')) {
            return response(['status'=>'failure','message'=>'Unauthorized'], 403);
        }
        $deleted  = $this->commsOfPracticeRepository->delete($request->id);

        if($deleted):
            $data = ['alert-success'=>'Comunity deleted successfully','status'=>'success','data'=>$deleted];
        else:
            $data = ['alert-danger'=>'Operation failed, try again','status'=>'failure','data'=>$deleted];   
        endif;

        return response($data,200);
    }

    public function moderate(Request $request){

        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        return view('admin.commsofpractice.moderate', $data);
    }

    public function getAllWithMembership(Request $request)
    {
        $communities = $this->commsOfPracticeRepository->getAllWithMembership();
        return view('admin.commsofpractice.all_with_membership', compact('communities'));
    }

    public function show($id)
    {
        $community = $this->commsOfPracticeRepository->find($id);

        // Pending first (is_approved=0), then approved (1), then rejected (2) for easier approval
        $membership = $community->membership()->with('user')->orderBy('is_approved', 'asc')->get();
        // Use relationships to count members
        $totalMembers = $community->membership()->count();
        $approvedCount = $community->approvedMembers()->count();
        $pendingCount = $community->pendingMembers()->count();
        $rejectedCount = $community->rejectedMembers()->count();

        // Get invitations
        $invitations = $this->commsOfPracticeRepository->getInvitations($id);

        // Recent publications and forums in this community
        $pubIds = \App\Models\PublicationCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('publication_id');
        $publications = \App\Models\Publication::whereIn('id', $pubIds)
            ->orderBy('created_at','desc')->limit(6)->get();

        $forumIds = \App\Models\ForumCommunityOfPractice::where('community_of_practice_id', $id)
            ->pluck('forum_id');
        $forums = \App\Models\Forum::with('user')
            ->whereIn('id', $forumIds)
            ->orderBy('created_at','desc')->limit(6)->get();

        $allCommunities = $this->commsOfPracticeRepository->get(new \Illuminate\Http\Request(['admin' => true]), true);

        return view('admin.commsofpractice.details', compact(
            'community', 'totalMembers', 'approvedCount', 'pendingCount', 'rejectedCount', 'membership',
            'publications','forums', 'invitations', 'allCommunities'
        ));
    }

    public function participants(Request $request)
    {
        if ($request->boolean('datatable')) {
            if ($request->input('scope') === 'pending') {
                return response()->json($this->commsOfPracticeRepository->adminPendingParticipantsDatatable($request));
            }

            return response()->json($this->commsOfPracticeRepository->adminParticipantsDatatable($request));
        }

        $geo = $this->commsOfPracticeRepository->participantsGeoContext();
        $geoLabel = $geo['geoLabel'];
        $stats = $this->commsOfPracticeRepository->participantsDashboardStats($geo);
        $geoTable = $geo['geoTable'];

        $geographies = DB::table($geoTable)->orderBy('name')->get(['id', 'name']);
        $communities = CommunityOfPractice::query()->orderBy('community_name')->get(['id', 'community_name']);
        $badgeTypes = BadgeType::query()->orderBy('name')->get(['id', 'name']);
        $search = (object) $request->all();

        return view('admin.commsofpractice.participants', compact(
            'geographies',
            'communities',
            'badgeTypes',
            'search',
            'geoLabel',
            'stats',
        ));
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'community_id' => 'required|exists:community_of_practices,id',
            'email' => 'required|string', // comma-separated or single
        ]);

        $emailInput = $request->email;
        $emails = array_filter(array_map('trim', preg_split('/[\s,]+/', $emailInput)));
        if (empty($emails)) {
            $msg = 'Please enter at least one email address.';
            return $this->sendInvitationResponse($request, $msg, 422, 'error');
        }

        $validEmails = [];
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $e;
            }
        }
        if (count($validEmails) > 5) {
            $msg = 'You can invite up to 5 users at a time.';
            return $this->sendInvitationResponse($request, $msg, 422, 'error');
        }
        if (empty($validEmails)) {
            $msg = 'No valid email address found.';
            return $this->sendInvitationResponse($request, $msg, 422, 'error');
        }

        try {
            $result = $this->commsOfPracticeRepository->sendInvitationsBulk(
                (int) $request->community_id,
                $validEmails,
                auth()->id()
            );
        } catch (\Throwable $e) {
            \Log::error('Send invitation failed', [
                'community_id' => $request->community_id,
                'emails' => $validEmails,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $msg = 'Failed to create invitations: ' . $e->getMessage();
            return $this->sendInvitationResponse($request, $msg, 500, 'error');
        }

        $msg = $result['sent'] . ' invitation(s) sent.';
        if ($result['skipped_member'] > 0) {
            $msg .= ' ' . $result['skipped_member'] . ' already member(s) skipped.';
        }
        if ($result['skipped_pending'] > 0) {
            $msg .= ' ' . $result['skipped_pending'] . ' pending invitation(s) skipped.';
        }
        if ($result['invalid'] > 0) {
            $msg .= ' ' . $result['invalid'] . ' invalid email(s) skipped.';
        }
        if (!empty($result['errors'])) {
            $msg .= ' Errors: ' . implode('; ', array_slice($result['errors'], 0, 3));
            if (count($result['errors']) > 3) {
                $msg .= '…';
            }
        }

        return $this->sendInvitationResponse($request, $msg, 200, 'success', $result);
    }

    /**
     * Return JSON for AJAX or redirect for form POST.
     */
    private function sendInvitationResponse(Request $request, string $message, int $code = 200, string $status = 'success', $result = null)
    {
        if ($request->ajax() || $request->wantsJson()) {
            if ($code >= 400) {
                return response()->json(['status' => 'error', 'message' => $message], $code);
            }
            return response()->json(['status' => $status, 'message' => $message, 'result' => $result]);
        }
        $alertClass = $status === 'success' ? 'success' : 'danger';
        $redirect = redirect()->to(url()->previous() ?: route('admin.commsofpractice.details', $request->community_id));
        return $redirect->with('alert', $message)->with('alert_class', $alertClass);
    }

    public function resendInvitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|integer',
            'community_id' => 'required|exists:community_of_practices,id',
        ]);

        $result = $this->commsOfPracticeRepository->resendInvitation(
            (int) $request->invitation_id,
            (int) $request->community_id,
            auth()->id()
        );

        if ($result['status'] === 'success') {
            return response()->json(['status' => 'success', 'message' => $result['message']]);
        }
        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }

    public function deleteInvitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|integer',
            'community_id' => 'required|exists:community_of_practices,id',
        ]);

        $result = $this->commsOfPracticeRepository->deleteInvitation(
            (int) $request->invitation_id,
            (int) $request->community_id
        );

        if ($result['status'] === 'success') {
            return response()->json(['status' => 'success', 'message' => $result['message']]);
        }
        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }

    public function bulkInviteFromCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'scope' => 'required|in:this,all,selected',
            'community_ids' => 'nullable|array',
            'community_ids.*' => 'integer|exists:community_of_practices,id',
            'community_id' => 'nullable|integer|exists:community_of_practices,id', // for scope=this
        ]);

        $emails = $this->commsOfPracticeRepository->parseEmailsFromCsv($request->file('csv_file'));
        $validEmails = array_values(array_filter($emails, function ($e) {
            return filter_var(trim($e), FILTER_VALIDATE_EMAIL);
        }));
        $invalidCount = count($emails) - count($validEmails);

        $scope = $request->scope;
        $communityIds = [];
        if ($scope === 'this') {
            $cid = $request->community_id ?? $request->community_ids[0] ?? null;
            if (!$cid) {
                return response()->json(['status' => 'error', 'message' => 'Community is required for this scope.'], 422);
            }
            $communityIds = [(int) $cid];
        } elseif ($scope === 'selected') {
            $communityIds = array_values(array_unique(array_map('intval', $request->community_ids ?? [])));
            if (empty($communityIds)) {
                return response()->json(['status' => 'error', 'message' => 'Please select at least one community.'], 422);
            }
        }

        $result = $this->commsOfPracticeRepository->bulkInviteFromCsv($scope, $communityIds, $validEmails, auth()->id());

        if (isset($result['error'])) {
            return response()->json(['status' => 'error', 'message' => $result['error']], 400);
        }

        $msg = $result['sent'] . ' invitation(s) sent across ' . $result['communities_count'] . ' community(ies).';
        if ($result['skipped_member'] > 0) {
            $msg .= ' ' . $result['skipped_member'] . ' already member(s) skipped.';
        }
        if ($result['skipped_pending'] > 0) {
            $msg .= ' ' . $result['skipped_pending'] . ' pending invitation(s) skipped.';
        }
        if ($invalidCount > 0) {
            $msg .= ' ' . $invalidCount . ' invalid email(s) in CSV skipped.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $msg,
            'result' => array_merge($result, ['invalid_in_csv' => $invalidCount]),
        ]);
    }

    public function getOne(Request $request)
    {
        $id = $request->id;
        $community = $this->commsOfPracticeRepository->find($id);
        if (!$community) {
            return response()->json(['status'=>'failure','message'=>'Not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            'id' => $community->id,
            'community_name' => $community->community_name,
            'description' => $community->description,
            'is_active' => $community->is_active,
            'region_id' => $community->region_id,
            'country_id' => $community->country_id,
            'organisation' => $community->organisation,
            'department' => $community->department,
            'is_public' => $community->is_public,
            'tags' => $community->tags->map(function($tag) {
                return ['id' => $tag->id, 'tag_text' => $tag->tag_text];
            })
        ]);
    }

    public function memberAction(Request $request) {
        $request->validate([
            'action' => 'required|in:approve,reject,activate,deactivate,make_admin,remove_admin',
            'community_id' => 'required|integer|exists:community_of_practices,id',
            'member_id' => 'nullable|integer',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer',
        ]);

        $ids = [];
        if ($request->filled('member_ids')) {
            $ids = array_filter(array_map('intval', $request->member_ids));
        } elseif ($request->filled('member_id')) {
            $ids = [(int) $request->member_id];
        }

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No members selected.'], 422);
        }

        if (in_array($request->action, ['approve', 'reject'], true)) {
            ContentModeration::ensureCanModerateCopParticipants();
        }

        $communityId = (int) $request->get('community_id');
        $updated = 0;
        foreach ($ids as $memberId) {
            $member = \App\Models\CommunityOfPracticeMembers::find($memberId);
            if ($member && (!$communityId || (int) $member->community_of_practice_id === $communityId)) {
                $this->commsOfPracticeRepository->updateMemberStatus($memberId, $request->action);
                $updated++;
            }
        }

        $message = $updated === 1
            ? 'Member status updated successfully.'
            : $updated . ' member(s) updated successfully.';
        return response()->json(['status' => 'success', 'message' => $message, 'updated' => $updated]);
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'community_id' => 'required|integer|exists:community_of_practices,id',
            'user_email' => 'required|email',
            'is_admin' => 'nullable|boolean',
        ]);

        $user = User::where('email', trim((string) $request->user_email))->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User with this email was not found.'], 404);
        }

        $member = \App\Models\CommunityOfPracticeMembers::firstOrNew([
            'community_of_practice_id' => (int) $request->community_id,
            'user_id' => (int) $user->id,
        ]);
        $member->is_approved = 1;
        $member->is_active = 1;
        $member->is_admin = (bool) $request->boolean('is_admin', false);
        $member->save();

        return response()->json(['status' => 'success', 'message' => 'Member added successfully.']);
    }

    /**
     * Permanently delete a rejected membership request (clean up).
     */
    public function deleteMember(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer',
            'community_id' => 'required|exists:community_of_practices,id',
        ]);

        $member = \App\Models\CommunityOfPracticeMembers::where('id', $request->member_id)
            ->where('community_of_practice_id', $request->community_id)
            ->where('is_approved', 2) // only allow delete for rejected
            ->first();

        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Rejected request not found or already removed.'], 404);
        }

        $member->delete();
        return response()->json(['status' => 'success', 'message' => 'Rejected request removed.']);
    }
}
