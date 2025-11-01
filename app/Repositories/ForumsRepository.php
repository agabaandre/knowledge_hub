<?php
namespace App\Repositories;

use App\Jobs\SendMailJob;
use App\Models\CommunityOfPracticeMembers;
use App\Models\CustomAttachment;
use App\Models\Faq;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumCommunityOfPractice;
use App\Models\ForumSubscription;
use App\Models\ForumTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema as DBSchema;

class ForumsRepository extends SharedRepo{

    public function get(Request $request,$approved=1){

        $rows_count = ($request->rows)?$request->rows:20;
        $forums = Forum::with(['user', 'tags', 'comments']);
        
        // If approved=3 (all forums), prioritize pending approvals at the top
        if($approved === 3) {
            $forums->orderByRaw('CASE WHEN is_approved = 0 AND status = 0 THEN 0 ELSE 1 END')
                   ->orderBy('created_at','desc');
        } else {
            $forums->orderBy('created_at','desc');
        }

        if($request->term){
            $forums->where('forum_title','like','%'.$request->term.'%');
            $forums->orWhere('forum_description','like','%'.$request->term.'%');
        }

        if($request->tag){
            $tagged_forums = ForumTag::where('tag',$request->tag)->get()->pluck('forum_id');
            $forums->whereIn('id',$tagged_forums);
        }

        if(current_user() && current_user()->id){

            //Protect Forums from non target audiences if targte audience was defined
            
            if(!$request->community_id):

                $communties = CommunityOfPracticeMembers::where("user_id",current_user()->id)
                ->pluck("community_of_practice_id");
            
                //forums for user communities
                $commForums = ForumCommunityOfPractice::whereIn("community_of_practice_id",$communties)->pluck('forum_id');

                $forums->whereIn('id',$commForums)
                ->orWhere('created_by',current_user()->id)
                ->orWhereDoesntHave("communities");
            else:
                $forums->whereHas("communities",function($query) use($request){
                    $query->where("community_of_practice_id",$request->community_id);
                });
            endif;

        }else
        {
            //only those without targets
            $forums->whereDoesntHave("communities");
        }

        if($approved !== 3)
        $forums->where('status',$approved);

         //Access levels effect to query results
         $this->access_filter($forums);

        $results =  $forums->paginate($rows_count);

        return $results;
    }

    public function getByUser($userId, Request $request, $approved=1){
        // Get forums where user has posted or commented
        $rows_count = ($request->rows) ? $request->rows : 20;
        
        // Get forum IDs where user created posts
        $userForumIds = Forum::where('created_by', $userId)->pluck('id');
        
        // Get forum IDs where user made comments
        $userCommentForumIds = ForumComment::where('created_by', $userId)->pluck('forum_id');
        
        // Combine and get unique forum IDs
        $allForumIds = $userForumIds->merge($userCommentForumIds)->unique();
        
        $forums = Forum::with(['user', 'tags', 'comments'])
            ->whereIn('id', $allForumIds)
            ->orderBy('created_at', 'desc');

        if($request->term){
            $forums->where(function($q) use ($request) {
                $q->where('forum_title','like','%'.$request->term.'%')
                  ->orWhere('forum_description','like','%'.$request->term.'%');
            });
        }

        if($request->tag){
            $tagged_forums = ForumTag::where('tag',$request->tag)->get()->pluck('forum_id');
            $forums->whereIn('id',$tagged_forums);
        }

        if($approved !== 3) {
            $forums->where('status', $approved);
        }

        //Access levels effect to query results
        $this->access_filter($forums);

        return $forums->paginate($rows_count);
    }

    public function save(Request $request){

        $forum = new Forum();
        $forum->forum_title = clean_unicode($request->title ?? '');
        $forum->forum_description = clean_unicode($request->description ?? '');
        $forum->created_by = current_user()->id;
        $forum->status = 0;

        if($request->hasFile('image')):

            $file           = $request->file('image');  
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
            $file->move(storage_path().'/app/public/uploads/forums/',$file_path);
            $forum->forum_image     = $file_path;

        endif;

        $forum->save();

        // Track forum post engagement
        if ($forum->created_by) {
            \App\Models\ForumEngagement::incrementForumPost($forum->created_by);
        }
        
        if($request->communities && count($request->communities)){

            // Filter out empty values (e.g., "All")
            $copIds = array_values(array_filter($request->communities, function($val){
                return !is_null($val) && $val !== '' && intval($val) > 0;
            }));

            foreach ($copIds as $copId){
                $forumComm = new ForumCommunityOfPractice();
                $forumComm->forum_id = $forum->id;
                $forumComm->community_of_practice_id = intval($copId);
                $forumComm->save();
            }
            
            // Send notifications to community members
            if (!empty($copIds)) {
                // Load user relationship for author name
                $forum->load('user');
                \App\Jobs\NotifyCommunityMembers::dispatch(
                    $copIds,
                    'forum',
                    $forum->id,
                    $forum->forum_title ?? 'Untitled Forum',
                    $forum->forum_description ?? '',
                    $forum->user->name ?? current_user()->name ?? 'Unknown'
                )->onQueue('default');
            }
        }

        // Save tags if provided (expects array of tag IDs)
        if ($request->tags && $forum->id) {
            $tagIds = is_array($request->tags) ? $request->tags : (json_decode($request->tags, true) ?? []);
            if (count($tagIds)) {
                $tagTexts = Tag::whereIn('id', $tagIds)->pluck('tag_text')->toArray();
                foreach ($tagTexts as $text) {
                    $ft = new ForumTag();
                    $ft->forum_id = $forum->id;
                    $ft->tag = $text;
                    $ft->save();
                }
            }
        }

        if($request->hasFile('attachments') && $forum->id ?? null):
            $files           = $request->file('attachments');  
            $this->save_attachments($files,$forum->id,'forums');
        endif;
        
        @$this->join_forum($forum);

        // Send notification to approvers if forum is pending approval (status = 0)
        if ($forum->id && $forum->status == 0 && $forum->is_approved == 0) {
            // Load user relationship for author name
            $forum->load('user');
            
            // Build approval URL
            $approveUrl = url('admin/forums/moderate') . '?id=' . $forum->id;
            
            // Dispatch notification to approvers
            \App\Jobs\NotifyApprovers::dispatch(
                'forum',
                $forum->id,
                $forum->forum_title ?? 'Untitled Forum',
                $forum->forum_description ?? '',
                $forum->user->name ?? current_user()->name ?? 'Unknown',
                $approveUrl
            )->onQueue('default');
        }

        return $forum;
    }

    public function  save_comment(Request $request){

        $comment = new ForumComment();

        $comment->created_by = current_user()->id;
        $comment->forum_id = $request->id;
        $comment->comment  = clean_unicode($request->comment ?? '');
        $comment->parent_id = $request->parent_id ?? null;
        $comment->save();

        // Track forum comment engagement
        if ($comment->created_by) {
            \App\Models\ForumEngagement::incrementForumComment($comment->created_by);
        }

        // Attachments removed - no longer saving attachments for comments

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
        $forum->is_approved =1;
        $forum->is_rejected =0;
        if (DBSchema::hasColumn('forums', 'approved_by')) {
            $forum->approved_by = current_user()->id;
        }
        if (DBSchema::hasColumn('forums', 'rejected_by')) {
            $forum->rejected_by = null;
        }
        $forum->update();

        $alert = array(
            'title' => "Forum Post $forum->title Approved",
            'body'=>'We are happy to inform you that your forum post has been approved and is live now',
            'email'=>$forum->user->email
        );

        SendMailJob::dispatch( $alert)->onQueue('default');

        return $forum;
    }

    public function reject($id){

        $forum = Forum::find($id);
        $forum->status =0;
        $forum->is_approved =0;
        $forum->is_rejected =1;
        if (DBSchema::hasColumn('forums', 'rejected_by')) {
            $forum->rejected_by = current_user()->id;
        }
        if (DBSchema::hasColumn('forums', 'approved_by')) {
            $forum->approved_by = null;
        }
        $forum->update();

        $alert = array(
            'title' => "Forum Post $forum->title Rejected",
            'body'=>'We are sorry to inform you that your forum post has been rejected',
            'email'=>$forum->user->email
        );

        SendMailJob::dispatch( $alert)->onQueue('default');

        return $forum;
    }

    public function getJoinedForums(Request $request){
     
    if(@current_user()->id):
        return ForumSubscription::where('user_id', current_user()->id)->get()->pluck('forum_id')->toArray();
    else:
        return [];
     endif;

    }

    public function join_forum($request){

        $joining = ForumSubscription::create([
            'user_id'=>current_user()->id,
            'forum_id'=>$request->id
        ]);

        $joining->save();

    }

    private function save_attachments($files,$record_id,$model){

        $upfiles   = (!is_array($files))?[$files]:$files;
        $file_path = null;
        
        foreach ($upfiles as $file) {

            $description = $file->getClientOriginalName();
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $model.'/'.$file_name.'.'.$extension;
           
            $file->move(storage_path('/app/public/uploads/'.$file_path));

        //insert if to be in different table
        if($record_id):

            $attachment   =  [
            "model"=>$model,
            "path"=> $file_path,
            "record_id"=>$record_id
           ];
       
         CustomAttachment::insert($attachment);

        endif;

       }

       return $file_path;
    }




}
