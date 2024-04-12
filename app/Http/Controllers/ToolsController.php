<?php

namespace App\Http\Controllers;

use App\Repositories\ToolsRepository;
use Illuminate\Http\Request;
class ToolsController extends Controller
{
    private $toolsRepo;

    public function __construct( ToolsRepository $toolsRepo)
    {
        $this->toolsRepo       = $toolsRepo;
    }

    public function index(Request $request){

        $data['search'] = (object) $request->all();
        $data['tools'] = $this->toolsRepo->get($request);
        return view('tools.index',$data);
    }

}
