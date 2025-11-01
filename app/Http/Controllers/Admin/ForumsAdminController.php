<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ForumsRepository;
use App\Repositories\QuizRepository;
use App\Models\ContentRequest;

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
        
        // Count pending forums for notification bell
        $data['pending_forums_count'] = \App\Models\Forum::where('is_approved', 0)
            ->where('status', 0)
            ->count();
        
        // Count pending forum comments
        $data['pending_forum_comments_count'] = \App\Models\ForumComment::where('status', 'pending')
            ->count();
        
        return view('admin.forums.index',$data);
    }

    public function moderation(Request $request){

        $data['forums'] = $this->forumsRepo->get($request,0);
        $data['search']       = (Object) $request->all();
        
        // Count pending forum comments
        $data['pending_forum_comments_count'] = \App\Models\ForumComment::where('status', 'pending')
            ->count();
        
        return view('admin.forums.moderation',$data);
    }

    public function destroy(Request $request){

        return $this->forumsRepo->delete($request->id);
    }

    
    public function details(Request $request){
        $forum          =  $this->forumsRepo->find($request->id);
        $data['forum']  = $forum;       
        return view('admin.forums.details',$data);
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
