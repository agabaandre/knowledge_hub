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
        $forums = Forum::with([
            'user', 
            'tags', 
            'comments' => function($query) {
                $query->whereNull('parent_id') // Only top-level comments (no replies)
                      ->with(['user', 'likes'])
                      ->orderBy('created_at', 'desc')
                      ->limit(5); // Limit to latest 5 comments
            },
            'likes'
        ])->withCount([
            'comments as total_comments' => function($query) {
                $query->whereNull('parent_id'); // Count only top-level comments
            },
            'likes as total_likes'
        ]);
        
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
        
        $forums = Forum::with([
            'user', 
            'tags', 
            'comments' => function($query) {
                $query->whereNull('parent_id') // Only top-level comments (no replies)
                      ->with(['user', 'likes'])
                      ->orderBy('created_at', 'desc')
                      ->limit(5); // Limit to latest 5 comments
            },
            'likes'
        ])->withCount([
            'comments as total_comments' => function($query) {
                $query->whereNull('parent_id'); // Count only top-level comments
            },
            'likes as total_likes'
        ])
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
        
        // Check if auto-approve comments is enabled (defaults to true)
        $autoApprove = settings()->auto_approve_comments ?? true;
        if ($autoApprove) {
            $comment->status = 'approved';
        }
        
        $comment->save();

        // Track forum comment engagement
        if ($comment->created_by) {
            \App\Models\ForumEngagement::incrementForumComment($comment->created_by);
        }

        // Save attachments if provided
        // This MUST happen after the comment is saved so we have a valid comment ID
        if($request->hasFile('attachments') && $comment && $comment->id) {
            $files = $request->file('attachments');
            \Log::info('Forum comment attachments received', [
                'comment_id' => $comment->id,
                'forum_id' => $comment->forum_id,
                'files_count' => is_array($files) ? count($files) : ($files ? 1 : 0),
                'files' => is_array($files) ? array_map(function($f) { 
                    return [
                        'name' => $f->getClientOriginalName(), 
                        'size' => $f->getSize(), 
                        'type' => $f->getMimeType(),
                        'extension' => $f->guessExtension()
                    ]; 
                }, $files) : [[
                    'name' => $files->getClientOriginalName(), 
                    'size' => $files->getSize(), 
                    'type' => $files->getMimeType(),
                    'extension' => $files->guessExtension()
                ]]
            ]);
            
            try {
                $savedCount = $this->save_comment_attachments($files, $comment->id);
                \Log::info('Forum comment attachments save completed', [
                    'comment_id' => $comment->id,
                    'forum_id' => $comment->forum_id,
                    'files_provided' => is_array($files) ? count($files) : ($files ? 1 : 0),
                    'files_saved' => $savedCount,
                    'saved_to_table' => 'custom_attachments'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to save forum comment attachments', [
                    'comment_id' => $comment->id,
                    'forum_id' => $comment->forum_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the comment save if attachment save fails, but log it
            }
        } else {
            if ($request->hasFile('attachments')) {
                \Log::warning('Forum comment - files uploaded but comment save failed', [
                    'has_file' => true,
                    'comment_exists' => $comment ? true : false,
                    'comment_id' => $comment->id ?? null,
                    'files_count' => is_array($request->file('attachments')) ? count($request->file('attachments')) : 1
                ]);
            }
        }

        // Reload comment to ensure attachments are available
        if ($comment && $comment->id) {
            $comment->refresh();
            // Trigger attachments accessor to verify they're loaded
            $comment->attachments;
        }

        return $comment;
    }

    /**
     * Save forum comment attachments to filesystem and database
     * 
     * @param array|\Illuminate\Http\UploadedFile $files Uploaded file(s)
     * @param int $comment_id The comment ID to associate attachments with
     * @return int Number of successfully saved attachments
     */
    private function save_comment_attachments($files, $comment_id){
        // Validate comment exists
        if (!$comment_id || !ForumComment::find($comment_id)) {
            \Log::error('Invalid comment ID provided for attachment save', [
                'comment_id' => $comment_id
            ]);
            return 0;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'apk', 'dll', 'sh', 'php', 'asp', 'jsp', 'py', 'rb', 'pl', 'cgi', 'bin', 'msi', 'deb', 'rpm'];
        
        $upfiles = (!is_array($files)) ? [$files] : $files;
        $savedCount = 0;
        
        \Log::info('Starting to save comment attachments', [
            'comment_id' => $comment_id,
            'files_count' => count($upfiles)
        ]);
        
        foreach ($upfiles as $file) {
            if (!$file || !$file->isValid()) {
                \Log::warning('Invalid or missing file in attachment batch', [
                    'comment_id' => $comment_id,
                    'file' => $file ? $file->getClientOriginalName() : 'null'
                ]);
                continue;
            }

            $extension = strtolower($file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $fileSize = $file->getSize();

            // Validate file
            if (!in_array($extension, $allowedExtensions)) {
                \Log::warning('File extension not allowed for forum comment', [
                    'extension' => $extension,
                    'filename' => $file->getClientOriginalName()
                ]);
                continue;
            }

            if ($fileSize > $maxFileSize) {
                \Log::warning('File too large for forum comment', [
                    'size' => $fileSize,
                    'max' => $maxFileSize,
                    'filename' => $file->getClientOriginalName()
                ]);
                continue;
            }

            if (in_array($extension, $dangerousExtensions)) {
                \Log::warning('Dangerous file type blocked for forum comment', [
                    'extension' => $extension,
                    'filename' => $file->getClientOriginalName()
                ]);
                continue;
            }

            try {
                // Store the human-readable original filename
                $original_filename = $file->getClientOriginalName();
                $file_name = md5_file($file->getRealPath());
                $file_path = 'forum/'.$file_name.'.'.$extension;
               
                $storagePath = storage_path('/app/public/uploads/forum/');
                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0755, true);
                    \Log::info('Created forum upload directory', ['path' => $storagePath]);
                }
                
                $moved = $file->move($storagePath, $file_name.'.'.$extension);
                
                if (!$moved) {
                    throw new \Exception('Failed to move uploaded file to ' . $storagePath);
                }
                
                $finalPath = $storagePath . $file_name.'.'.$extension;
                if (!file_exists($finalPath)) {
                    throw new \Exception('File does not exist after move: ' . $finalPath);
                }

                \Log::info('Forum comment attachment saved successfully', [
                    'comment_id' => $comment_id,
                    'original_filename' => $original_filename,
                    'saved_filename' => $file_name.'.'.$extension,
                    'saved_path' => $finalPath,
                    'file_size' => filesize($finalPath),
                    'file_path_for_db' => $file_path
                ]);

                try {
                    $attachment = CustomAttachment::create([
                        'model' => 'forum_comments',
                        'path' => $file_path,
                        'name' => $original_filename, // Save human-readable filename
                        'record_id' => $comment_id
                    ]);
                    
                    if (!$attachment || !$attachment->id) {
                        throw new \Exception('CustomAttachment::create() returned null or had no ID');
                    }
                    
                    $savedCount++;
                    \Log::info('Forum comment attachment record created in database', [
                        'comment_id' => $comment_id,
                        'attachment_id' => $attachment->id,
                        'file_path' => $file_path,
                        'saved_successfully' => true
                    ]);
                } catch (\Exception $dbException) {
                    \Log::error('Database error saving forum comment attachment: ' . $dbException->getMessage(), [
                        'comment_id' => $comment_id,
                        'file_path' => $file_path,
                        'exception' => get_class($dbException),
                        'trace' => $dbException->getTraceAsString()
                    ]);
                    throw $dbException; // Re-throw to be caught by outer try-catch
                }
            } catch (\Exception $e) {
                \Log::error('Error saving forum comment attachment: ' . $e->getMessage(), [
                    'comment_id' => $comment_id,
                    'filename' => $file->getClientOriginalName(),
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        \Log::info('Completed saving comment attachments', [
            'comment_id' => $comment_id,
            'total_files' => count($upfiles),
            'saved_count' => $savedCount
        ]);
        
        return $savedCount;
    }



    public function find($id, $update_views = true){
        // Eager load comments with attachments - trigger the accessor by loading comments first
        $forum = Forum::with([
            'user', 
            'tags', 
            'comments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user', 
            'comments.likes', 
            'comments.replies.user', 
            'comments.replies.likes', 
            'likes'
        ])->find($id);
        
        // Load attachments for all comments and replies after the forum is loaded
        // This ensures attachments are available when rendering comments
        if ($forum && $forum->comments) {
            foreach ($forum->comments as $comment) {
                // Trigger the attachments accessor to load attachments for main comment
                $comment->attachments;
                
                // Load attachments for replies as well
                if ($comment->replies && $comment->replies->count() > 0) {
                    foreach ($comment->replies as $reply) {
                        $reply->attachments;
                    }
                }
            }
        }
        
        if ($forum && $update_views) {
            // Track views using cookie to prevent duplicate counting from same user in same session
            $cookie_name = "ForumViewed" . $forum->id . ((auth()->check() && auth()->id()) ? auth()->id() : '');
            $viewed = get_cookie($cookie_name);
            
            if (!$viewed && $forum) {
                // Increment views counter without triggering updated_at timestamp
                \DB::table('forums')
                    ->where('id', $forum->id)
                    ->increment('views');
                
                // Refresh forum model to get updated views count
                $forum->refresh();
                
                // Set cookie to prevent duplicate views (same user viewing same forum again)
                set_cookie($cookie_name);
            }
        }
        
        return $forum;
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

    public function toggleLike($forumId)
    {
        $userId = auth()->id();
        $like = \App\Models\ForumLike::where('forum_id', $forumId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            \App\Models\ForumLike::create([
                'forum_id' => $forumId,
                'user_id' => $userId
            ]);
            $liked = true;
        }

        $count = \App\Models\ForumLike::where('forum_id', $forumId)->count();

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

    public function toggleCommentLike($commentId)
    {
        $userId = auth()->id();
        $like = \App\Models\ForumCommentLike::where('forum_comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            \App\Models\ForumCommentLike::create([
                'forum_comment_id' => $commentId,
                'user_id' => $userId
            ]);
            $liked = true;
        }

        $count = \App\Models\ForumCommentLike::where('forum_comment_id', $commentId)->count();

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

}
