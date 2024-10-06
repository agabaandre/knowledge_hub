<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use Illuminate\Http\Request;

class CommsOfPracticeRepository{

    public function get(Request $request,$return_array=false){

        return ($return_array)?CommunityOfPractice::all():CommunityOfPractice::with(['membership','approvedMembers','pendingMembers','rejectedMembers','communityForums','communityPublications'])->paginate(15);
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

    public function find($id){

        return CommunityOfPractice::find($id);
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

}