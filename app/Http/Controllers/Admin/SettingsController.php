<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\SettingsRepository;

class SettingsController extends Controller
{
    private $settingsRepo;

    public function __construct(SettingsRepository $settingsRepo)
    {
        $this->settingsRepo = $settingsRepo;
    }

    public function index(Request $request){

        $data['settings'] = (Object) $this->settingsRepo->get($request);
        return view('admin.settings.index',$data);
    }
  
    public function store(Request $request){

        $saved = $this->settingsRepo->save($request);

        if($saved):
            $data = ['message'=>'Settings saved successfully','status'=>'success','data'=>$saved];
        else:
            $data = ['message'=>'Operation failed, try again','status'=>'failure','data'=>$saved];   
        endif;

        if($request->ajax()){
            return response($data,200);
        }
        
        return back()->with($data);
    }

  
}
