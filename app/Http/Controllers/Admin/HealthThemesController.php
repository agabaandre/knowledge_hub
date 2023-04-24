<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\AuthorsRepository;
use App\Repositories\QuotesRepository;
use App\Http\Controllers\Controller;
use App\Repositories\ThemesRepository;

class HealthThemesController extends Controller
{
    private $themesRepo;

    public function __construct(ThemesRepository $themesRepo)
    {
        $this->themesRepo = $themesRepo;
    }

    public function index(Request $request){

        $data['themes'] = $this->themesRepo->get($request);
        $data['search'] = (Object) $request->all();
        return view('admin.themes.index',$data);
    }

    public function store(Request $request){

        $saved = $this->themesRepo->save($request);

        if($saved):
            $data = ['message'=>'Subject Area saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }

        return back()->with($data);
    }

   
    public function destroy(Request $request){
        return $this->themesRepo->delete($request->id);
    }

  
}
