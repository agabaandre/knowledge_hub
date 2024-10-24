<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Http\Controllers\Controller;

class MessagingController extends Controller
{
    private $commsOfPracticeRepository;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
    }

    public function index(Request $request){

        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        
        return view('admin.messaging.index',$data);
    }

  
    public function sendMessage(Request $request){

        $saved = $this->commsOfPracticeRepository->sendMessage($request);

        if($saved):
            $data = ['message'=>'Message sent successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


   
  
}
