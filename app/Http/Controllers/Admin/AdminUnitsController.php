<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AdminUnitsRepository;
use App\Http\Controllers\Controller;

class AdminUnitsController extends Controller
{
    private $adminUnitsRepository;

    public function __construct(AdminUnitsRepository $adminUnitsRepository)
    {
        $this->adminUnitsRepository = $adminUnitsRepository;
    }

    public function index(Request $request){

        $data['adminunits'] = $this->adminUnitsRepository->get($request);
        $data['search']    = (Object) $request->all();
        return view('admin.adminunits.index',$data);
    }

  
    public function store(Request $request){

        $saved = $this->adminUnitsRepository->save($request);

        if($saved):
            $data = ['message'=>'Unit saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){

        return $this->adminUnitsRepository->delete($request->id);
    }

  
}
