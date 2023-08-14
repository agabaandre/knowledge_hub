<?php

namespace App\Http\Controllers;

use App\Repositories\ForumsRepository;
use Illuminate\Http\Request;

class ForumsController extends Controller
{
    private $forumsRepo;

    public function __construct(ForumsRepository $forumsRepo)
    {
        $this->forumsRepo = $forumsRepo;
    }

    public function index(Request $request)
    {

        $data['forums']    = $this->forumsRepo->get($request);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $data['search']    = (object) $request->all();

        return view('forums.index', $data);
    }

    public function thread(Request $request)
    {

        $data['forum']     = $this->forumsRepo->find($request->id);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $request['rows']   = 6;
        $data['search']    = (object) $request->all();
        $data['forums']    = $this->forumsRepo->get($request);

        return view('forums.show', $data);
    }

    public function join(Request $request)
    {
        if(!@current_user()->id)
         return redirect('login');
       
        $this->forumsRepo->join_forum($request);

       return redirect('forums/thread?id='.$request->id);
    }

    public function create(Request $request)
    {
        return view('forums.create');
    }

    public function comment(Request $request)
    {
        $this->forumsRepo->save_comment($request);
        return back();
    }

    public function publish(Request $request)
    {
        $this->forumsRepo->save($request);
        return back();
    }

}
