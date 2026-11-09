<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;

class QuotesController extends Controller
{
    private $quotesRepo;

    public function __construct(QuotesRepository $quotesRepo)
    {
        $this->quotesRepo = $quotesRepo;
    }

    public function index(Request $request){

        $data['quotes']    = $this->quotesRepo->get($request);
        $data['search']  = (Object) $request->all();
        return view('admin.quotes.index',$data);
    }

  
    public function store(Request $request){

        $request->validate([
            'quote' => 'required|string|max:5000',
            'link_url' => 'nullable|string|max:2048',
            'image' => 'nullable|file|mimes:'.\App\Support\SafeUpload::mimesRule(\App\Support\SafeUpload::IMAGES).'|max:5120',
        ]);

        $saved = $this->quotesRepo->save($request);

        if($saved):
            $data = ['message'=>'File type saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }


    public function destroy(Request $request){
        return $this->quotesRepo->delete($request->id);
    }


  
}
