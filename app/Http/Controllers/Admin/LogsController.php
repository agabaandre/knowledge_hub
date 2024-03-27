<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Repositories\LogsRepository;
use App\Http\Controllers\Controller;

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

  
}
