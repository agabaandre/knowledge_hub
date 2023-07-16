<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\Faq;
use App\Models\Forum;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumsRepository extends SharedRepo{

    public function get(Request $request,$pending=1){

        $rows_count = ($request->rows)?$request->rows:20;
        $forums = Forum::with(['user', 'tags', 'comments'])->orderBy('created_at','desc');

        if($request->term){
            $forums->where('forum_title','like','%'.$request->term.'%');
            $forums->orWhere('forum_description','like','%'.$request->term.'%');
        }

        $forums->where('status',$pending);

         //Access levels effect to query results
         $this->access_filter($forums);

        $results =  $forums->paginate($rows_count);

        return $results;
    }

    public function save(Request $request){
        $faq = new Faq();

        return $faq;
    }

    public function  save_comment(Request $request){

        $comment = new ForumComment();

        $comment->created_by = current_user()->id;
        $comment->forum_id = $request->id;
        $comment->comment = $request->comment;
        $comment->save();

        return $comment;
    }



    public function find($id){

        return Forum::find($id);
    }

    public function delete($id){
        return Forum::find($id)->delete();
    }

    public function count(){
        return count(Forum::all());
    }

    public function approve($id){
        $forum = Forum::find($id);
        $forum->status =1;
        $forum->update();

        $alert = array(
            'title' => "Forum Post $forum->title Approved",
            'body'=>'We are happy to inform you that your forum post has been approved and is live now',
            'email'=>$forum->user->email
        );

        SendMailJob::dispatch( $alert);

        return $forum;
    }

    public function reject($id){
        $forum = Forum::find($id);
        $forum->status =2;
        $forum->update();


        $alert = array(
            'title' => "Forum Post $forum->title Rejected",
            'body'=>'We are sorry to inform you that your forum post has been rejected',
            'email'=>$forum->user->email
        );

        SendMailJob::dispatch( $alert);

        return $forum;
    }




}
