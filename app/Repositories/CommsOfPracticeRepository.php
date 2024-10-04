<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Models\CommunityOfPractice;
use App\Models\CommunityOfPracticeMembers;
use Illuminate\Http\Request;

class CommsOfPracticeRepository{

    public function get(Request $request,$return_array=false){

        return ($return_array)?CommunityOfPractice::all():CommunityOfPractice::paginate(15);
    }
    
    public function save(Request $request){

        $access_grp = new CommunityOfPractice();

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
        return CommunityOfPractice::with('members')->get();
    }

}