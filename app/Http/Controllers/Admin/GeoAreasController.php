<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AreasRepository;
use App\Http\Controllers\Controller;

class GeoAreasController extends Controller
{
    private $areasRepo;

    public function __construct(AreasRepository $areasRepo)
    {
        $this->areasRepo = $areasRepo;
    }

    public function index(Request $request){

        $data['areas'] = $this->areasRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.areas.index',$data);
    }

    public function store(Request $request){

        $saved = $this->areasRepo->save($request);

        if($saved):
            $data = ['message'=>'Goegraphical area saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        return back()->with($data);
    }


    public function destroy(Request $request){

        return $this->areasRepo->delete($request->id);
    }


}
