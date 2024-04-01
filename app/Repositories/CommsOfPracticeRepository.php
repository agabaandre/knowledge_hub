<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Models\CommunityOfPractice;
use Illuminate\Http\Request;

class CommsOfPracticeRepository{

    public function get(Request $request){

        return CommunityOfPractice::paginate(15);
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
        return CommunityOfPractice::find($id)->delete();
    }

}