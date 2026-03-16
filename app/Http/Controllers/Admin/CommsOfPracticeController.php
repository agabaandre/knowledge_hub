<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Repositories\AreasRepository;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UITableService;

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
        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        
        // Pass regions data for chained dropdowns
        $data['regions'] = $this->areasRepo->regions()->load('countries');
        
        // Load pending member counts for each community
        $data['communities']->getCollection()->transform(function ($community) {
            $community->pending_members_count = \App\Models\CommunityOfPracticeMembers::where('community_of_practice_id', $community->id)
                ->where('is_approved', 0)
                ->count();
            return $community;
        });
        
        $data['search']    = (Object) $request->all();
        $sql = "SELECT c.id,community_name,description,is_active,u.name as created_by FROM community_of_practices c left join users u on u.id=c.created_by";
        $data['uitable'] = $this->uiTableService->get_ui_table("community_of_practices",$cols,$sql);
        
        // Count pending member approvals for notification bell
        $data['pending_member_approvals_count'] = \App\Models\CommunityOfPracticeMembers::where('is_approved', 0)
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

        $membership = $community->membership;
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

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'community_id' => 'required|exists:community_of_practices,id',
            'email' => 'required|string', // comma-separated or single
        ]);

        $emailInput = $request->email;
        $emails = array_filter(array_map('trim', preg_split('/[\s,]+/', $emailInput)));
        if (empty($emails)) {
            return response()->json(['status' => 'error', 'message' => 'Please enter at least one email address.'], 422);
        }

        $validEmails = [];
        foreach ($emails as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $e;
            }
        }
        if (empty($validEmails)) {
            return response()->json(['status' => 'error', 'message' => 'No valid email address found.'], 422);
        }

        $result = $this->commsOfPracticeRepository->sendInvitationsBulk(
            (int) $request->community_id,
            $validEmails,
            auth()->id()
        );

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

        return response()->json(['status' => 'success', 'message' => $msg, 'result' => $result]);
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
            'action' => 'required|in:approve,reject',
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
