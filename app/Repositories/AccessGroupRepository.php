<?php
namespace App\Repositories;

use App\Models\Setting;
use App\Models\UserAccessGroup;
use Illuminate\Http\Request;

class AccessGroupRepository{

    public function get(Request $request){

        return UserAccessGroup::paginate(15);
    }
    
    public function save(Request $request){

        $access_grp = ($request->id) ? UserAccessGroup::find($request->id) : new UserAccessGroup();

        $access_grp->group_name = $request->group_name;
        // accept either 'group_description' or fallback to 'description'; default to empty string
        $desc = $request->input('group_description', $request->input('description', ''));
        $access_grp->group_description = $desc ?? '';
        $access_grp->save();

        return $access_grp;
    }

    public function find($id){

        return UserAccessGroup::find($id);
    }

   function delete($id)
    {
        return UserAccessGroup::find($id)->delete();
    }

}