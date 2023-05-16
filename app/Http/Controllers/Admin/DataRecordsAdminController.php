<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DataRecordsRepository;

class DataRecordsAdminController extends Controller
{
    private $dataRecordsRepo;

    public function __construct(DataRecordsRepository $dataRecordsRepo)
    {
        $this->dataRecordsRepo = $dataRecordsRepo;
    }



    public function index(Request $request){

        $data['records'] = $this->dataRecordsRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.datarecords.index',$data);
    }

    public function create(Request $request){

        $data['record'] = null;
        return view('admin.datarecords.create',$data);
    }

    public function edit(Request $request){

        $data['record']  =  $this->dataRecordsRepo->find($request->id);
        return view('admin.datarecords.create',$data);
    }

    public function store(Request $request){

        $saved = $this->dataRecordsRepo->save($request);


        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back(200)->with($data);
    }


    public function destroy(Request $request){

        return $this->dataRecordsRepo->delete($request->id);
    }

}
