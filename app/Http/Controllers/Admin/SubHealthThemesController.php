<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;

class SubHealthThemesController extends Controller
{
    private $themesRepo;

    public function __construct(ThemesRepository $themesRepo)
    {
        $this->themesRepo = $themesRepo;
    }

    public function index(Request $request){

        $data['themes'] = $this->themesRepo->get_all_subthemes($request);
        $data['search'] = (Object) $request->all();
        return view('admin.subthemes.index',$data);
    }

    public function store(Request $request){

        $saved = $this->themesRepo->save_subtheme($request);

        if($saved):
            $data = ['message'=>'Subtheme saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }

   
    public function destroy(Request $request){
        return $this->themesRepo->delete_subtheme($request->id);
    }

  
}
