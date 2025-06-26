<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\ToolsRepository;
use App\Http\Controllers\Controller;
use App\Models\ToolCategory;
use App\Models\User;
use App\Services\UITableService;

class ToolsAdminController extends Controller
{
    private $toolRepository,$uiTableService;

    public function __construct(ToolsRepository $toolRepository, UITableService $uiTableService)
    {
        $this->toolRepository = $toolRepository;
        $this->uiTableService  = $uiTableService;
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
        $col["title"]    = "Tool Name"; 
        $col["name"]     = "tool_name"; 
        $col["width"]    = "30"; 
        $col["editable"] = true;
         $cols[] = $col;

        $col = array();
        $col["title"]    = "Tool Description"; 
        $col["name"]     = "tool_desc"; 
        $col["width"]    = "30"; 
        $col["editable"] = true;
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Tool URL"; 
        $col["name"]     = "tool_url"; 
        $col["width"]    = "30"; 
        $col["editable"] = true;
         $cols[] = $col;

        $col = array();
        $col["title"]    = "Category"; 
        $col["name"]     = "tool_category_id"; 
        $col["width"]    = "10"; 
        $col["editable"] = true;
        $col["hidden"]   = false;
        $col["edittype"] = "select";
       
        $categories   = ToolCategory::select("id","category_name")->get();
        $options      = "";

        $count = 0;

        foreach($categories as $cat):
            $options.= (($count>0)?";":"").$cat->id.":".$cat->category_name;
            $count++;
        endforeach;

        $col["editoptions"] = array("value"=>$options);
        $cols[] = $col;

        $data['search']    = (Object) $request->all();
        $sql = "SELECT t.id,tool_name,tool_desc,tool_url,c.category_name as tool_category_id FROM tools t left join tool_categories c on c.id=t.tool_category_id";
        $data['uitable'] = $this->uiTableService->get_ui_table("tools",$cols,$sql);
        
        return view('admin.tools.index',$data);
    }

  
    public function store(Request $request){

        $saved = $this->toolRepository->save($request);

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

        return $this->toolRepository->delete($request->id);
    }

  
}
