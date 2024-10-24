<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\CommsOfPracticeRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
class MessagingController extends Controller
{
    private $commsOfPracticeRepository;

    public function __construct(CommsOfPracticeRepository $commsOfPracticeRepository)
    {
        $this->commsOfPracticeRepository = $commsOfPracticeRepository;
    }

    public function index(Request $request){

        $request->merge(['withRelated'=>true]);
        $data['communities'] = $this->commsOfPracticeRepository->get($request);
        
        return view('admin.messaging.index',$data);
    }

  
    public function sendMessage(Request $request){

        $validated = Validator::make($request->all(), [
            'community_ids' => 'required|array',
            'member_ids' => 'sometimes|array',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        if($validated->fails()):
            return back()->withErrors($validated)->withInput();
        endif;

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
