<?php
namespace App\Repositories;

use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use Illuminate\Http\Request;

class CommsOfPracticeRepository{

    public function get(Request $request, $return_array = false)
    {
        $query = CommunityOfPractice::query();

        if ($request->input('withRelated', false)) {
            $query->with(['membership', 'approvedMembers','approvedMembers.user', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications']);
        }

        return $return_array ? $query->get() : $query->paginate($request->rows ?? 15);
    }
    
    public function save(Request $request){

        $access_grp = ($request->id)?CommunityOfPractice::find($request->id):new CommunityOfPractice();

        $access_grp->community_name = $request->community_name;
        $access_grp->description = $request->description;
        $access_grp->created_by = current_user()->id;
        $access_grp->save();

        clear_cache();
        
        return $access_grp;
    }

    public function find($id, $withRelated = false)
    {
        $query = CommunityOfPractice::where('id', $id);

        if ($withRelated) {
            $query->with(['membership', 'approvedMembers', 'pendingMembers', 'rejectedMembers', 'communityForums', 'communityPublications']);
        }

        return $query->first();
    }

   function delete($id)
    {
        
        $deleted = CommunityOfPractice::find($id)->delete();
        clear_cache();
        return $deleted;
    }
    
    public function getAllWithMembership()
    {
        return CommunityOfPractice::with([
            'membership',
            'approvedMembers',
            'pendingMembers',
            'rejectedMembers'
        ])->get();
    }

    public function updateMemberStatus($memberId, $action) {
       
        $member = CommunityOfPracticeMembers::find($memberId);

        
        if ($action === 'approve') {
            $member->is_approved = 1;
        } elseif ($action === 'reject') {
            $member->is_approved = 2; // Set to 2 for rejected
        }

        $member->save();

        return $member;
    }

    public function addMember($communityId, $userId) {
       
        CommunityOfPracticeMembers::create([
            'community_of_practice_id' => $communityId,
            'user_id' => $userId,
            'is_approved' => 0,
        ]);

        return true; // or any relevant response
    }

    public function removeMember($communityId, $userId) {
        $member = CommunityOfPracticeMembers::where('community_of_practice_id', $communityId)
                                            ->where('user_id', $userId)
                                            ->first();
        if ($member) {
            $member->delete();
            return true;
        }
        return false;
    }

    public function getPaginatedForums($communityId, $perPage = 10)
    {
        return CommunityOfPractice::find($communityId)->communityForums()->paginate($perPage);
    }

    public function getPaginatedPublications($communityId, $perPage = 10)
    {
        return CommunityOfPractice::find($communityId)->communityPublications()->paginate($perPage);
    }

    public function sendMessage(Request $request)
    {
        $communityIds = $request->input('community_ids', []);
        $memberIds = $request->input('member_ids', []);
        $message = $request->input('message');
        $title = $request->input('title');

        $message = str_replace('{name}', 'Member', $message);

        foreach ($communityIds as $communityId) {
            $community = CommunityOfPractice::with('approvedMembers.user')->find($communityId);

            if (!$community) {
                continue; // Skip if community not found
            }

            $members = $community->approvedMembers;
            $fcmTokens = [];

            if (empty($memberIds)) {
                // Collect FCM tokens for all approved members if no specific members are selected
                foreach ($members as $member) {
                    if ($member->user->fcm_token) { // Assuming 'fcm_token' is the field name
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            } else {
                // Collect FCM tokens for specific members
                foreach ($members as $member) {
                    if (in_array($member->user_id, $memberIds) && $member->user->fcm_token) {
                        $fcmTokens[] = $member->user->fcm_token;
                    }
                }
            }

            // Call the helper function to send the push notification
            if (!empty($fcmTokens)) {
                sendPushNotification($title, $message, $fcmTokens);
            }
        }

        return true; // Indicate success
    }
}
