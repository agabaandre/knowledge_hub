<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;
use App\Repositories\FactsRepository;

class FactsAdminController extends Controller
{
    private $factsRepo;

    public function __construct(FactsRepository $factsRepo)
    {
        $this->factsRepo = $factsRepo;
    }

    public function index(Request $request){

        $data['facts']    = $this->factsRepo->get($request);
        $data['search']  = (Object) $request->all();
        return view('admin.facts.index',$data);
    }

    public function store(Request $request){

        $request->validate([
            'title'=>'required',
            'summary'=>'required',
            'description'=>'required',
        ]);

        $saved = $this->factsRepo->save($request);

        if($saved):
            $data = ['message'=>'Fact saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }


    public function destroy(Request $request){
        return $this->factsRepo->delete($request->id);
    }



}
