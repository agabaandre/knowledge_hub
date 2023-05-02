<?php

namespace App\Http\Controllers;

use App\Repositories\ForumsRepository;
use Illuminate\Http\Request;

class ForumsController extends Controller
{
    private $forumsRepo;

    public function __construct( ForumsRepository $forumsRepo)
    {
        $this->forumsRepo       = $forumsRepo;
    }

    public function index(Request $request){

        $data['forums'] = $this->forumsRepo->get($request);
        return view('forums.index',$data);
    }

    public function thread(Request $request){

        $data['forum'] = $this->forumsRepo->find($request->id);
        $request['rows'] = 6;
        $data['search'] = (object) $request->all();
        $data['forums'] = $this->forumsRepo->get($request);
        return view('forums.show',$data);
    }

    public function comment(Request $request){
        
        $this->forumsRepo->save_comment($request);
        return back();
    }

}
