<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AccessGroupRepository;
use App\Http\Controllers\Controller;

class AccessGroupsController extends Controller
{
    private $accessGroupRepository;

    public function __construct(AccessGroupRepository $accessGroupRepository)
    {
        $this->accessGroupRepository = $accessGroupRepository;
    }

    public function index(Request $request){

        $data['access_groups'] = $this->accessGroupRepository->get($request);
        $data['search']    = (Object) $request->all();
        return view('admin.accessgroups.index',$data);
    }

  
    public function store(Request $request){

        $saved = $this->accessGroupRepository->save($request);

        if($saved):
            $data = ['message'=>'Group saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){

        return $this->accessGroupRepository->delete($request->id);
    }

  
}
