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
