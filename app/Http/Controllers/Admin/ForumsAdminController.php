<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ForumsRepository;
use App\Repositories\QuizRepository;

class ForumsAdminController extends Controller
{
    private $forumsRepo;

    public function __construct(ForumsRepository $forumsRepo)
    {
        $this->forumsRepo = $forumsRepo;
    }

    public function index(Request $request){

        $data['forums'] = $this->forumsRepo->get($request);
        $data['search']       = (Object) $request->all();
        return view('admin.forums.index',$data);
    }

 
    public function destroy(Request $request){

        return $this->forumsRepo->delete($request->id);
    }

  
}
