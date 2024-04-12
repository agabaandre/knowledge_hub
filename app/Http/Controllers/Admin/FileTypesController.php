<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\FileTypesRepository;
use App\Http\Controllers\Controller;

class FileTypesController extends Controller
{
    private $fileTypesRepo;

    public function __construct(FileTypesRepository $fileTypesRepo)
    {
        $this->fileTypesRepo = $fileTypesRepo;
    }

    public function index(Request $request){

        $data['filetypes'] = $this->fileTypesRepo->get($request);
        $data['search']    = (Object) $request->all();
        return view('admin.filetypes.index',$data);
    }

  
    public function store(Request $request){

        $saved = $this->fileTypesRepo->save($request);
    

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

        return $this->fileTypesRepo->delete($request->id);
    }

  
}
