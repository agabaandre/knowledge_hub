<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UITableService;

class CommsOfPracticeController extends Controller
{
    private $commsOfPracticeRepository,$uiTableService;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository, UITableService $uiTableService)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
        $this->uiTableService  = $uiTableService;
    }

    public function index(Request $request){

        $col = array();
        $col["title"]    = "Id"; 
        $col["name"]     = "id"; 
        $col["width"]    = "30"; 
        $col["editable"] = false;
        $col["hidden"] = true;
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Community"; 
        $col["name"]     = "community_name"; 
        $col["width"]    = "30"; 
        $col["editable"] = true;
       // $col["edittype"]   = "file"; // render as file
        //$col["upload_dir"] = storage_path()."/app/public/uploads"; // upload here
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
        $col["edittype"] = "select";
        //$cols["editoptions"]["sql"] = "select id as k, name as v from users";
        
        $users = User::select("id","name")->get();
        $options = "";

        $count =0;
        foreach($users as $user):
            $options.= (($count>0)?";":"").$user->id.":".$user->name;
            $count++;
        endforeach;

        $col["editoptions"] = array("value"=>$options);
     
      //  $col["edittype"] = "lookup"; 
       // $col["editoptions"] = array("table"=>"users", "id"=>"id", "label"=>"concat(name,' ','')");
        //$col["editrules"]["readonly"] = true;
        $cols[] = $col;

        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        $data['search']    = (Object) $request->all();
        $sql = "SELECT c.id,community_name,description,is_active,u.name as created_by FROM community_of_practices c join users u on u.id=c.created_by";
        $data['uitable'] = $this->uiTableService->get_ui_table("community_of_practices",$cols,$sql);
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

        return $this->commsOfPracticeRepository->delete($request->id);
    }

  
}
