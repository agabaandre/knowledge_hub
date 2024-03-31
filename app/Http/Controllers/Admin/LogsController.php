<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\LogsRepository;
use App\Http\Controllers\Controller;
use App\Models\User;

class LogsController extends Controller
{
    private $logsRepo;

    public function __construct(LogsRepository $logsRepo)
    {
        $this->logsRepo = $logsRepo;
    }

    public function index(Request $request){

        $data['logs'] = $this->logsRepo->get($request);
        $data['search']    = (Object) $request->all();
        return view('admin.logs.access',$data);
    }

    
    public function trail(Request $request){

        log_user_trail('Accessed',$description=null);

        $data['users']  = User::all();
        $data['search'] = (Object) $request->all();
        $data['trails'] = $this->logsRepo->audit_trail($request);

        return view('admin.permissions.audit')->with($data);
    }

  
}
