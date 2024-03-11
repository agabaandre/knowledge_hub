<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\FaqsRepository;

class FaqsAdminController extends Controller
{
    private $faqsRepo;

    public function __construct(FaqsRepository $faqsRepo)
    {
        $this->faqsRepo = $faqsRepo;
    }

    public function index(Request $request){

        $data['faqs']    = $this->faqsRepo->get($request);
        $data['search']  = (Object) $request->all();
        return view('admin.faqs.index',$data);
    }

  
    public function store(Request $request){

        $request->validate([
            'question'=>'required',
            'answer'=>'required'
        ]);

        $saved = $this->faqsRepo->save($request);

        if($saved):
            $data = ['message'=>'FAQ saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){
        return $this->faqsRepo->delete($request->id);
    }


  
}
