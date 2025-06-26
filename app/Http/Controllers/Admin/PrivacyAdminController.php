<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AuthorsRepository;
use App\Repositories\PolicyRepository;

class PrivacyAdminController extends Controller
{
    private $policyRepo;

    public function __construct(PolicyRepository $policyRepo)
    {
        $this->policyRepo = $policyRepo;
    }

    public function index(Request $request){

        $data['policy'] = file_get_contents(storage_link('uploads/privacy/privacy_policy.md'));
        return view('admin.policy.index',$data);
    }


    public function store(Request $request){

        $saved = file_put_contents(storage_path('app/public/uploads/privacy/privacy_policy.md'),$request->policy);

        if($saved):
            $data = ['message'=>'Policy updated successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back(200)->with($data);
    }
 
  
}
