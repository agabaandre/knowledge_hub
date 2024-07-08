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

        $access_grp = new UserAccessGroup();

        $access_grp->group_name = $request->group_name;
        $access_grp->group_description = $request->group_description;
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