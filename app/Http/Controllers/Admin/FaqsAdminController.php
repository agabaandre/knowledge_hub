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
            $message = ($request->id) ? 'FAQ updated successfully' : 'FAQ saved successfully';
            $data = ['message'=>$message,'status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response()->json($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){
        return $this->faqsRepo->delete($request->id);
    }

    public function get(Request $request){
        $id = $request->id;
        
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ ID is required'
            ], 400);
        }
        
        $faq = $this->faqsRepo->find($id);
        
        if ($faq) {
            return response()->json([
                'success' => true,
                'faq' => [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'FAQ not found'
        ], 404);
    }

  
}
