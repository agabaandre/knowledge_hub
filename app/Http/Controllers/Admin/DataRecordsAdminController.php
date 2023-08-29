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

        return back()->with($data);
    }


    public function destroy(Request $request){

        return $this->dataRecordsRepo->delete($request->id);
    }

    public function categories(Request $request){

        $data['categories'] = $this->dataRecordsRepo->get_categories($request);
        $data['search']       = (Object) $request->all();
        return view('admin.datarecords.categories',$data);
    }


    public function subcategories(Request $request){

        $data['subcategories'] = $this->dataRecordsRepo->get_subcategories($request);
        $data['categories']    = $this->dataRecordsRepo->get_categories($request);
        $data['search']        = (Object) $request->all();
        return view('admin.datarecords.subcategories',$data);
    }

    public function delete_category(Request $request){
        return $this->dataRecordsRepo->delete_category($request->id);
    }

    public function save_category(Request $request){

        $saved = $this->dataRecordsRepo->save_category($request);

        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back()->with($data);
    }

    public function save_subcategory(Request $request){

        $saved = $this->dataRecordsRepo->save_subcategory($request);

        if($saved):
            $data = ['message'=>'Record saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back()->with($data);
    }


}
