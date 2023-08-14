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

        $data['forums'] = $this->forumsRepo->get($request,3);
        $data['search']       = (Object) $request->all();
        return view('admin.forums.index',$data);
    }

    public function moderation(Request $request){

        $data['forums'] = $this->forumsRepo->get($request,0);
        $data['search']       = (Object) $request->all();
        return view('admin.forums.moderation',$data);
    }

    public function destroy(Request $request){

        return $this->forumsRepo->delete($request->id);
    }

    public function approve(Request $request){
        $this->forumsRepo->approve($request->id);
        return back();
    }

    public function reject(Request $request){

        $this->forumsRepo->reject($request->id);
        return back();
    }

}
