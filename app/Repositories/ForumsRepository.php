<?php
namespace App\Repositories;

use App\Models\Faq;
use App\Models\Forum;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumsRepository{

    public function get(Request $request,$pending=1){

        $rows_count = ($request->rows)?$request->rows:20;
        $faqs = Forum::orderBy('id','desc');

        if($request->term){
            $faqs->where('forum_title','like','%'.$request->term.'%');
            $faqs->orWhere('forum_description','like','%'.$request->term.'%');
        }

        $faqs->where('status',$pending);

        $results =  $faqs->paginate($rows_count);

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

        return $forum;
    }

    public function reject($id){
        $forum = Forum::find($id);
        $forum->status =2;
        $forum->update();

        return $forum;
    }




}