<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Http\Controllers\Controller;

class CommsOfPracticeController extends Controller
{
    private $commsOfPracticeRepository;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
    }

    public function index(Request $request){

        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        $data['search']    = (Object) $request->all();
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
