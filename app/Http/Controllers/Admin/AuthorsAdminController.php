<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;
use App\Services\UITableService;

class AuthorsAdminController extends Controller
{
    private $authorsRepo, $uiTableService;

    public function __construct(AuthorsRepository $authorsRepo, UITableService $uiTableServie)
    {
        $this->authorsRepo    = $authorsRepo;
        $this->uiTableService = $uiTableServie;
    }

    public function index(Request $request){

        $col = array();
        $col["title"] = "Id"; 
        $col["name"] = "id"; 
        $col["width"] = "2"; 
        $col["hidden"] = true;
        $col["editable"] = false;
        $cols[] = $col;

        $col = array();
        $col["title"] = "Name"; 
        $col["name"] = "name"; 
        $col["width"] = "30"; 
        $col["editable"] = true;
        $cols[] = $col;

        $col = array();
        $col["title"]    = "Organization"; 
        $col["name"]     = "is_organsiation"; 
        $col["width"]    = "10"; 
        $col["editable"] = true;
        $col["edittype"] = "checkbox";
        $col["editoptions"] = array("value"=>"Yes:No");
        $cols[] = $col;

        //$col["edittype"] = "lookup";
        //$col["editoptions"] = array("table"=>"employees", "id"=>"employee_id", "label"=>"concat(first_name,' ',last_name)");

        //$data['authors'] = $this->authorsRepo->get($request);
        $sql = "SELECT name,is_organsiation FROM author";
        $data['uitable'] = $this->uiTableService->get_ui_table("author",$cols,$sql);
        $data['search']  = (Object) $request->all();
        return view('admin.authors.index',$data);
    }

    public function destroy(Request $request){

        return $this->authorsRepo->delete($request->id);
    }

  
}
