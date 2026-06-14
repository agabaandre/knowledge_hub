<?php
namespace App\Repositories;

use App\Jobs\NotifyApprovers;
use App\Jobs\SendMailJob;
use App\Models\CommunityOfPracticeMembers;
use App\Models\ContentRequest;
use App\Models\CustomAttachment;
use App\Models\Faq;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Models\ForumCommunityOfPractice;
use App\Models\ForumLike;
use App\Models\ForumSubscription;
use App\Models\ForumTag;
use App\Models\ForumApprovalLog;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\Support\Str;
use App\Services\ContentRequestReferralNotifier;
use App\Services\ForumThreadActivityNotifier;
use App\Services\OfficeDocumentToPdfService;
use App\Support\CommunityTargeting;
use App\Support\SeoSlugger;

class ForumsRepository extends SharedRepo{

    /**
     * Whether the forums table has a FULLTEXT index (cached).
     */
    protected function forumsFulltextAvailable(): bool
    {
        return Cache::remember('forums_fulltext_index_available', 3600, function () {
            $driver = DB::connection()->getDriverName();
            if ($driver !== 'mysql') {
                return false;
            }
            $db = DB::connection()->getDatabaseName();
            $result = DB::select(
                "SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'forums' AND INDEX_NAME = 'ft_forum_search' LIMIT 1",
                [$db]
            );
            return !empty($result);
        });
    }

    /**
     * Apply full search on forums: title, description, and author (creator) name.
     * Supports a single author name (e.g. "Raissa") so main page search finds forums by that author.
     */
    protected function applyForumTermSearch($query, string $term): void
    {
        $term = trim($term);
        if ($term === '') {
            return;
        }
        $query->where(function ($q) use ($term) {
            if ($this->forumsFulltextAvailable()) {
                $q->whereRaw(
                    'MATCH(forum_title, forum_description) AGAINST(? IN NATURAL LANGUAGE MODE)',
                    [$term]
                );
            } else {
                $q->where('forum_title', 'like', '%' . $term . '%')
                  ->orWhere('forum_description', 'like', '%' . $term . '%');
            }
            // Include forums where the creator's name matches (e.g. one author name)
            $q->orWhereHas('user', function ($uq) use ($term) {
                $uq->where('name', 'like', '%' . $term . '%');
            });
        });
    }

    /**
     * Public forums index: threads from the last 14 days first (newest first), then older
     * threads by views, joined-thread preference, and engagement.
     */
    protected function applyPublicForumListingOrder($query, ?int $userId = null): void
    {
        $cutoff = Carbon::now()->subDays(14)->toDateTimeString();

        $query->orderByRaw('CASE WHEN forums.created_at >= ? THEN 0 ELSE 1 END ASC', [$cutoff]);
        $query->orderByRaw('CASE WHEN forums.created_at >= ? THEN forums.created_at END DESC', [$cutoff]);

        $joinedIds = [];
        if ($userId) {
            $joinedIds = ForumSubscription::query()
                ->where('user_id', $userId)
                ->pluck('forum_id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->values()
                ->all();
        }

        if ($joinedIds !== []) {
            $placeholders = implode(',', array_fill(0, count($joinedIds), '?'));
            $query->orderByRaw(
                "CASE WHEN forums.created_at < ? AND forums.id IN ($placeholders) THEN 1 ELSE 0 END DESC",
                array_merge([$cutoff], $joinedIds)
            );
        }

        $query->orderByRaw('CASE WHEN forums.created_at < ? THEN COALESCE(forums.views, 0) END DESC', [$cutoff]);
        $query->orderByRaw('CASE WHEN forums.created_at < ? THEN total_comments END DESC', [$cutoff]);
        $query->orderByRaw('CASE WHEN forums.created_at < ? THEN total_likes END DESC', [$cutoff]);
        $query->orderByDesc('forums.created_at');
    }

    public function get(Request $request, $approved = 1, ?string $adminQueue = null, bool $applyAccessFilter = true){

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

        if ($adminQueue !== null && $adminQueue !== '') {
            if ($adminQueue === 'pending') {
                $forums->pendingApproval();
            } elseif ($adminQueue === 'approved') {
                $forums->where('status', 1)
                    ->where('is_approved', 1)
                    ->where(function ($q) {
                        $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                    });
            } elseif ($adminQueue === 'rejected') {
                $forums->where('is_rejected', 1);
            }
        } elseif ($approved === 3) {
            // All forums: pending first, then by date
            $forums->orderByRaw('CASE WHEN is_approved = 0 AND status = 0 THEN 0 ELSE 1 END')
                   ->orderBy('created_at', 'desc');
        } elseif ((int) $approved !== 1 || ($adminQueue !== null && $adminQueue !== '')) {
            $forums->orderBy('created_at', 'desc');
        }

        if ($request->filled('term') && strlen(trim($request->term)) > 0) {
            $this->applyForumTermSearch($forums, trim($request->term));
        }

        if($request->tag){
            $tagged_forums = ForumTag::where('tag',$request->tag)->get()->pluck('forum_id');
            $forums->whereIn('id',$tagged_forums);
        }

        // Admin pending / approved / rejected lists must see all forums site-wide — do not apply
        // community targeting or country/access filters (those use OR clauses and break queue filters).
        $adminForumQueue = $adminQueue !== null && $adminQueue !== '';

        if (! $adminForumQueue) {
            if (current_user() && current_user()->id) {

                //Protect Forums from non target audiences if targte audience was defined

                if (! $request->community_id) {

                    $userId = (int) current_user()->id;
                    $communties = CommunityOfPracticeMembers::where('user_id', $userId)
                        ->pluck('community_of_practice_id');

                    // Forums linked to communities the user belongs to
                    $commForums = ForumCommunityOfPractice::whereIn('community_of_practice_id', $communties)->pluck('forum_id');

                    // Logged-in users must still see the same open (non–community-targeted) forums as guests,
                    // plus forums they created and forums for COPs they joined. Never use whereIn(id, [])
                    // (Laravel compiles that to "0 = 1"), which breaks the OR logic and can hide every thread.
                    $forums->where(function ($q) use ($commForums, $userId) {
                        $q->where('created_by', $userId)
                            ->orWhereDoesntHave('communities')
                            ->orWhere('also_public_on_hub', 1);
                        if ($commForums->isNotEmpty()) {
                            $q->orWhereIn('id', $commForums);
                        }
                    });
                } else {
                    $forums->whereHas('communities', function ($query) use ($request) {
                        $query->where('community_of_practice_id', $request->community_id);
                    });
                }
            } else {
                $forums->where(function ($q) {
                    $q->whereDoesntHave('communities')
                        ->orWhere('also_public_on_hub', 1);
                });
            }
        }

        if ($adminQueue === null || $adminQueue === '') {
            if ($approved !== 3) {
                $forums->where('status', $approved);
                // Public / live directory: only show moderator-approved threads (matches records search)
                if ((int) $approved === 1) {
                    $forums->where('is_approved', 1)
                        ->where(function ($q) {
                            $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                        });
                }
            }
        }

        if (! $adminForumQueue && $applyAccessFilter) {
            // Access filter restricts by creator country (Viewer / Country / RCC). Skip for public
            // directory/API so logged-in users see the same open discussions as guests.
            $this->access_filter($forums);
        }

        if ($adminQueue !== null && $adminQueue !== '') {
            $forums->orderBy('created_at', 'desc');
        } elseif ((int) $approved === 1) {
            $userId = auth()->check() ? (int) auth()->id() : null;
            $this->applyPublicForumListingOrder($forums, $userId);
        }

        $results = $forums->paginate($rows_count)->withQueryString();

        return $results;
    }

    /**
     * Search forums by term for the records search page (publications + forums combined).
     * Returns approved forums only, with same community/access rules as get().
     */
    public function searchForRecords(Request $request, $limit = 5, bool $applyAccessFilter = true)
    {
        $forums = Forum::with(['user'])
            ->withCount([
                'comments as total_comments' => function ($q) { $q->whereNull('parent_id'); },
                'likes as total_likes'
            ])
            ->where('status', 1)
            ->where('is_approved', 1)
            ->orderBy('created_at', 'desc');

        $hasTerm = $request->filled('term') && strlen(trim((string) $request->term)) > 0;
        $tagText = $this->resolveForumTagText($request);

        if ($hasTerm) {
            $this->applyForumTermSearch($forums, trim((string) $request->term));
        }

        if ($tagText !== null) {
            $taggedForumIds = ForumTag::where('tag', $tagText)->pluck('forum_id');
            if ($taggedForumIds->isEmpty()) {
                return collect();
            }
            $forums->whereIn('id', $taggedForumIds);
        }

        if (! $hasTerm && $tagText === null) {
            return collect();
        }

        if (current_user() && current_user()->id) {
            if (!$request->community_id) {
                $userId = (int) current_user()->id;
                $communities = CommunityOfPracticeMembers::where('user_id', $userId)->pluck('community_of_practice_id');
                $commForums = ForumCommunityOfPractice::whereIn('community_of_practice_id', $communities)->pluck('forum_id');
                $forums->where(function ($q) use ($commForums, $userId) {
                    $q->where('created_by', $userId)
                        ->orWhereDoesntHave('communities')
                        ->orWhere('also_public_on_hub', 1);
                    if ($commForums->isNotEmpty()) {
                        $q->orWhereIn('id', $commForums);
                    }
                });
            } else {
                $forums->whereHas('communities', function ($q) use ($request) {
                    $q->where('community_of_practice_id', $request->community_id);
                });
            }
        } else {
            $forums->where(function ($q) {
                $q->whereDoesntHave('communities')
                    ->orWhere('also_public_on_hub', 1);
            });
        }

        if ($applyAccessFilter) {
            $this->access_filter($forums);
        }

        return $forums->limit($limit)->get();
    }

    private function resolveForumTagText(Request $request): ?string
    {
        if (! $request->filled('tag')) {
            return null;
        }

        $raw = $request->tag;
        if (is_numeric($raw)) {
            $tag = Tag::find((int) $raw);

            return $tag ? trim((string) $tag->tag_text) : null;
        }

        $text = trim((string) $raw);

        return $text !== '' ? $text : null;
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

    /**
     * Forum threads created by the user (all moderation states), for account "My posts" list.
     */
    public function getAuthoredForumThreads(int $userId, Request $request)
    {
        $rows_count = $request->rows ? (int) $request->rows : 20;

        $forums = Forum::with(['tags'])
            ->where('created_by', $userId)
            ->orderBy('created_at', 'desc');

        if ($request->filled('term')) {
            $term = trim((string) $request->term);
            if ($term !== '') {
                $forums->where(function ($q) use ($term) {
                    $q->where('forum_title', 'like', '%' . $term . '%')
                        ->orWhere('forum_description', 'like', '%' . $term . '%');
                });
            }
        }

        if ($request->filled('status')) {
            $status = (string) $request->status;
            if ($status === 'published') {
                $forums->where('is_approved', 1)
                    ->where('status', 1)
                    ->where(function ($q) {
                        $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                    });
            } elseif ($status === 'pending') {
                $forums->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })->where(function ($q) {
                    $q->where('is_approved', '!=', 1)
                        ->orWhere('status', '!=', 1)
                        ->orWhereNull('is_approved')
                        ->orWhereNull('status');
                });
            } elseif ($status === 'rejected') {
                $forums->where('is_rejected', 1);
            }
        }

        return $forums->paginate($rows_count)->withQueryString();
    }

    public function save(Request $request){

        $forum = new Forum();
        $forum->forum_title = format_title_with_ai_fallback($request->title ?? '');
        $forum->forum_description = sanitize_rich_text_for_storage(clean_unicode($request->description ?? ''));
        $forum->created_by = current_user()->id;
        $forum->status = 0;
        CommunityTargeting::mergeTagAllIntoRequest($request);
        $forum->also_public_on_hub = CommunityTargeting::wantsAlsoPublicOnHubWithCommunities($request) ? 1 : 0;

        if (\Illuminate\Support\Facades\Schema::hasColumn('forums', 'public_availability')) {
            $forum->public_availability = resolve_public_availability_from_request($request);
        }

        if($request->hasFile('image')):

            $file           = $request->file('image');  
            $file_name   = md5_file($file->getRealPath());
            $extension   = $file->guessExtension();
            $file_path   = $file_name.'.'.$extension;
           
            $file->move(hub_storage_path('uploads/forums').'/',$file_path);
            $forum->forum_image     = $file_path;

        endif;

        $this->assignForumSlug($forum);

        $forum->save();

        // Track forum post engagement
        if ($forum->created_by) {
            \App\Models\ForumEngagement::incrementForumPost($forum->created_by);
        }

        $copIds = CommunityTargeting::filterValidCommunityIds($request->input('communities', []));
        $copIdsInt = array_map('intval', $copIds);

        foreach ($copIdsInt as $copId) {
            $forumComm = new ForumCommunityOfPractice();
            $forumComm->forum_id = $forum->id;
            $forumComm->community_of_practice_id = $copId;
            $forumComm->save();
        }

        if ($copIdsInt !== []) {
            $forum->load('user');
            \App\Jobs\NotifyCommunityMembers::dispatch(
                $copIdsInt,
                'forum',
                $forum->id,
                $forum->forum_title ?? 'Untitled Forum',
                $forum->forum_description ?? '',
                $forum->user->name ?? current_user()->name ?? 'Unknown',
                (int) $forum->created_by ?: null
            )->onQueue('default');
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
            $this->logForumApprovalEvent($forum->id, 'submitted', null, null, (int) $forum->created_by);

            // Load user relationship for author name
            $forum->load('user');
            
            // Build approval URL
            $approveUrl = url('admin/forums/moderate') . '?id=' . $forum->id;
            
            // Dispatch notification to approvers
            NotifyApprovers::dispatch(
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
        $comment->comment  = sanitize_rich_text_for_storage(clean_unicode($request->comment ?? ''));
        $comment->parent_id = $request->parent_id ?? null;
        
        // Check if auto-approve comments is enabled (defaults to true)
        $autoApprove = settings()->auto_approve_comments ?? true;
        if ($autoApprove) {
            $comment->status = 'approved';
        } else {
            $comment->status = 'pending';
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

        if ($comment && $comment->id && ($comment->status ?? '') === 'approved') {
            $fid = (int) $comment->forum_id;
            $linkedRequest = ContentRequest::query()
                ->where(function ($q) use ($fid) {
                    $q->where('referral_forum_id', $fid)
                        ->orWhereHas('referralTargets', fn ($t) => $t->where('referral_forum_id', $fid));
                })
                ->first();
            if ($linkedRequest) {
                $comment->loadMissing('user');
                ContentRequestReferralNotifier::notifyRequestorNewForumComment($linkedRequest, $comment);
            }
        }

        if ($comment && $comment->id) {
            $st = (string) ($comment->status ?? '');
            if ($st === '' || strcasecmp($st, 'approved') === 0) {
                $forumForNotify = Forum::find($comment->forum_id);
                if ($forumForNotify) {
                    ForumThreadActivityNotifier::notifyNewComment($forumForNotify, $comment);
                }
            }
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

        $officeService = app(OfficeDocumentToPdfService::class);
        $allowedExtensions = array_values(array_unique(array_merge([
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
            'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
            'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma',
        ], OfficeDocumentToPdfService::CONVERTIBLE_EXTENSIONS)));
        $rasterPdfMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $octetStreamExtensions = array_values(array_unique(array_merge([
            'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
            'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma',
        ], OfficeDocumentToPdfService::CONVERTIBLE_EXTENSIONS)));
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

            $mimeNorm = strtolower((string) $file->getMimeType());
            if (in_array($mimeNorm, ['image/jpg', 'image/pjpeg'], true)) {
                $mimeNorm = 'image/jpeg';
            }

            $isOfficeConvertible = $officeService->isConvertibleExtension($extension);

            $mimeAllowed = in_array($mimeNorm, $rasterPdfMimes, true)
                || Str::startsWith($mimeNorm, 'video/')
                || Str::startsWith($mimeNorm, 'audio/')
                || ($mimeNorm === 'application/octet-stream' && in_array($extension, $octetStreamExtensions, true))
                || $isOfficeConvertible;

            if (Str::startsWith($mimeNorm, 'image/') && ! in_array($mimeNorm, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true) && ! $isOfficeConvertible) {
                $mimeAllowed = false;
            }

            if (! $mimeAllowed) {
                \Log::warning('File MIME type not allowed for forum comment', [
                    'mime' => $mimeNorm,
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
                // Human-readable original filename + hashed storage basename
                $original_filename = str_replace(["\0", "\r"], '', (string) $file->getClientOriginalName());
                $file_name = md5_file($file->getRealPath());
                $file_path = 'forum/'.$file_name.'.'.$extension;
               
                $storagePath = hub_storage_path('uploads/forum').'/';
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

                $converter = app(OfficeDocumentToPdfService::class);
                if ($converter->isConvertibleExtension($extension)) {
                    $pdfPath = $converter->convertToPdf($finalPath);
                    if ($pdfPath && is_file($pdfPath) && filesize($pdfPath) > 0) {
                        if (is_file($finalPath) && $finalPath !== $pdfPath) {
                            @unlink($finalPath);
                        }
                        $extension = 'pdf';
                        $file_path = 'forum/'.$file_name.'.pdf';
                        $original_filename = pathinfo($original_filename, PATHINFO_FILENAME).'.pdf';
                        $finalPath = $pdfPath;
                    }
                }

                \Log::info('Forum comment attachment saved successfully', [
                    'comment_id' => $comment_id,
                    'original_filename' => $original_filename,
                    'saved_filename' => basename($finalPath),
                    'saved_path' => $finalPath,
                    'file_size' => filesize($finalPath),
                    'file_path_for_db' => $file_path
                ]);

                try {
                    $attachment = CustomAttachment::create([
                        'model' => 'forum_comments',
                        'path' => $file_path,
                        'name' => $original_filename,
                        'stored_filename' => basename($file_path),
                        'record_id' => $comment_id,
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



    public function findBySlug(string $slug, $update_views = true)
    {
        $id = Forum::query()->where('slug', $slug)->value('id');

        if (! $id) {
            return null;
        }

        return $this->find($id, $update_views);
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

                ForumThreadActivityNotifier::notifyViewMilestoneIfApplicable(
                    $forum,
                    (int) ($forum->views ?? 0)
                );
                
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

        $forum = Forum::with('user')->find($id);
        if (! $forum) {
            return null;
        }
        $forum->status =1;
        $forum->is_approved =1;
        $forum->is_rejected =0;
        if (DBSchema::hasColumn('forums', 'approved_by')) {
        $forum->approved_by = current_user()->id;
        }
        if (DBSchema::hasColumn('forums', 'rejected_by')) {
            $forum->rejected_by = null;
        }
        if (DBSchema::hasColumn('forums', 'is_resubmission_pending')) {
            $forum->is_resubmission_pending = 0;
        }
        $forum->update();

        $this->logForumApprovalEvent($forum->id, 'approved');

        $this->dispatchForumAuthorEmailIfPossible(
            optional($forum->user)->email,
            'Forum post approved: '.($forum->forum_title ?? 'Your discussion'),
            'We are happy to inform you that your forum post has been approved and is live now.'
        );

        return $forum;
    }

    /**
     * Update title/body for a forum still awaiting approval (moderation queue).
     */
    public function updatePendingModeration(int $id, string $title, string $descriptionHtml): bool
    {
        $forum = Forum::find($id);
        if (! $forum) {
            return false;
        }
        if ((int) $forum->is_approved !== 0 || (int) $forum->status !== 0) {
            return false;
        }

        $forum->forum_title = format_title_with_ai_fallback($title);
        $forum->forum_description = sanitize_rich_text_for_storage(clean_unicode($descriptionHtml));
        $this->assignForumSlug($forum);
        $forum->save();

        return true;
    }

    /**
     * Author updates their own forum post (pending, rejected, or previously published).
     * Rejected and published edits are reset to pending approval and notify moderators.
     */
    public function updateUnpublishedForumByAuthor(Request $request, Forum $forum): bool
    {
        if (! current_user() || ! current_user()->id) {
            return false;
        }
        if ((int) $forum->created_by !== (int) current_user()->id) {
            return false;
        }

        $wasRejected = (int) ($forum->is_rejected ?? 0) === 1;
        $wasPublished = (int) ($forum->is_approved ?? 0) === 1 && (int) ($forum->status ?? 0) === 1;
        $needsReapproval = $wasRejected || $wasPublished;

        CommunityTargeting::mergeTagAllIntoRequest($request);
        if ($request->has('community_targeting_options')) {
            $forum->also_public_on_hub = CommunityTargeting::wantsAlsoPublicOnHubWithCommunities($request) ? 1 : 0;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('forums', 'public_availability') && $request->has('public_availability')) {
            $forum->public_availability = resolve_public_availability_from_request($request, $forum->public_availability ?? null);
        }

        $forum->forum_title = format_title_with_ai_fallback($request->title ?? '');
        $forum->forum_description = sanitize_rich_text_for_storage(clean_unicode($request->description ?? ''));

        if ($needsReapproval) {
            $forum->status = 0;
            $forum->is_approved = 0;
            if ($wasRejected) {
                $forum->is_rejected = 0;
                if (DBSchema::hasColumn('forums', 'rejected_reason')) {
                    $forum->rejected_reason = null;
                }
                if (DBSchema::hasColumn('forums', 'rejected_by')) {
                    $forum->rejected_by = null;
                }
            }
            if (DBSchema::hasColumn('forums', 'approved_by')) {
                $forum->approved_by = null;
            }
            if (DBSchema::hasColumn('forums', 'is_resubmission_pending')) {
                $forum->is_resubmission_pending = 1;
            }
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = md5_file($file->getRealPath());
            $extension = $file->guessExtension();
            $file_path = $file_name . '.' . $extension;
            $file->move(hub_storage_path('uploads/forums').'/', $file_path);
            $forum->forum_image = $file_path;
        }

        $this->assignForumSlug($forum);

        $forum->save();

        if ($wasRejected) {
            $this->logForumApprovalEvent($forum->id, 'resubmitted', null, ['after_rejection' => true], (int) $forum->created_by);
        } elseif ($wasPublished) {
            $this->logForumApprovalEvent($forum->id, 'resubmitted', null, ['after_publication' => true], (int) $forum->created_by);
        }

        ForumCommunityOfPractice::where('forum_id', $forum->id)->delete();

        $copIds = CommunityTargeting::filterValidCommunityIds($request->input('communities', []));
        $copIdsInt = array_map('intval', $copIds);
        foreach ($copIdsInt as $copId) {
            $forumComm = new ForumCommunityOfPractice();
            $forumComm->forum_id = $forum->id;
            $forumComm->community_of_practice_id = $copId;
            $forumComm->save();
        }

        if ($copIdsInt === [] && $request->has('community_targeting_options')) {
            $forum->also_public_on_hub = 0;
            $forum->save();
        }

        ForumTag::where('forum_id', $forum->id)->delete();

        if ($request->tags) {
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

        if ($request->hasFile('attachments') && $forum->id) {
            $files = $request->file('attachments');
            $this->save_attachments($files, $forum->id, 'forums');
        }

        if ($needsReapproval && $forum->id && (int) $forum->status === 0 && (int) $forum->is_approved === 0) {
            $forum->load('user');
            $approveUrl = url('admin/forums/moderate') . '?id=' . $forum->id;
            NotifyApprovers::dispatch(
                'forum',
                $forum->id,
                $forum->forum_title ?? 'Untitled Forum',
                $forum->forum_description ?? '',
                $forum->user->name ?? current_user()->name ?? 'Unknown',
                $approveUrl
            )->onQueue('default');
        }

        return true;
    }

    public function reject($id, string $rejectedReason = ''): ?Forum
    {
        $forum = Forum::with('user')->find($id);
        if (! $forum) {
            return null;
        }

        $reason = trim(clean_unicode(strip_tags($rejectedReason)));

        $forum->status = 0;
        $forum->is_approved = 0;
        $forum->is_rejected = 1;
        if (DBSchema::hasColumn('forums', 'rejected_by')) {
            $forum->rejected_by = current_user()->id;
        }
        if (DBSchema::hasColumn('forums', 'approved_by')) {
            $forum->approved_by = null;
        }
        if (DBSchema::hasColumn('forums', 'rejected_reason')) {
            $forum->rejected_reason = $reason !== '' ? $reason : null;
        }
        if (DBSchema::hasColumn('forums', 'is_resubmission_pending')) {
            $forum->is_resubmission_pending = 0;
        }
        $forum->update();

        $this->logForumApprovalEvent($forum->id, 'rejected', $reason !== '' ? $reason : null);

        $body = 'We are sorry to inform you that your forum post was not approved.';
        if ($reason !== '') {
            $body .= "\n\nReason provided by the moderator:\n".$reason;
        }

        $this->dispatchForumAuthorEmailIfPossible(
            optional($forum->user)->email,
            'Forum post not approved: '.($forum->forum_title ?? 'Your discussion'),
            $body
        );

        return $forum;
    }

    /**
     * Queue email to the forum / forum-comment author when an address exists (skips quietly otherwise).
     */
    private function dispatchForumAuthorEmailIfPossible(?string $email, string $title, string $body): void
    {
        $email = $email ? trim($email) : '';
        if ($email === '') {
            \Log::warning('Forum author email notification skipped: no recipient address', [
                'title' => $title,
            ]);

            return;
        }

        SendMailJob::dispatch([
            'title' => $title,
            'body' => $body,
            'email' => $email,
        ])->onQueue('default');
    }

    /**
     * Approve a pending forum comment and notify the author by email.
     */
    public function approveForumComment(int $id): ?ForumComment
    {
        $comment = ForumComment::with(['user', 'forum'])->find($id);
        if (! $comment) {
            return null;
        }

        if (strtolower((string) $comment->status) === 'approved') {
            return $comment;
        }

        $comment->status = 'approved';
        $comment->update();

        $threadTitle = $comment->forum->forum_title ?? 'a discussion';
        $this->dispatchForumAuthorEmailIfPossible(
            optional($comment->user)->email,
            'Your forum comment was approved',
            'We are happy to inform you that your comment on the discussion "'.$threadTitle.'" has been approved and is now visible.'
        );

        return $comment;
    }

    /**
     * Reject a forum comment and notify the author by email.
     */
    public function rejectForumComment(int $id): ?ForumComment
    {
        $comment = ForumComment::with(['user', 'forum'])->find($id);
        if (! $comment) {
            return null;
        }

        if (strtolower((string) $comment->status) === 'rejected') {
            return $comment;
        }

        $comment->status = 'rejected';
        $comment->update();

        $threadTitle = $comment->forum->forum_title ?? 'a discussion';
        $this->dispatchForumAuthorEmailIfPossible(
            optional($comment->user)->email,
            'Your forum comment was not approved',
            'We are sorry to inform you that your comment on the discussion "'.$threadTitle.'" was not approved and will not be shown.'
        );

        return $comment;
    }

    public function getJoinedForums(Request $request){
     
    if(@current_user()->id):
        return ForumSubscription::where('user_id', current_user()->id)->get()->pluck('forum_id')->toArray();
    else:
        return [];
     endif;

    }

    public function join_forum($request)
    {
        if (! current_user() || ! current_user()->id) {
            return null;
        }

        $forumId = null;
        if ($request instanceof Forum) {
            $forumId = (int) $request->id;
        } elseif ($request instanceof Request) {
            $forumId = (int) $request->input('id');
        } elseif (is_object($request) && isset($request->id)) {
            $forumId = (int) $request->id;
        }

        if ($forumId < 1) {
            return null;
        }

        ForumSubscription::firstOrCreate(
            [
                'user_id' => (int) current_user()->id,
                'forum_id' => $forumId,
            ],
            []
        );

        return true;
    }

    private function save_attachments($files, $record_id, $model)
    {
        $upfiles = (! is_array($files)) ? [$files] : $files;
        $lastPath = null;
        $dir = storage_path('app/public/uploads/' . $model);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $converter = app(OfficeDocumentToPdfService::class);

        foreach ($upfiles as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $description = str_replace(["\0", "\r"], '', (string) $file->getClientOriginalName());
            $file_name = md5_file($file->getRealPath());
            $extension = strtolower((string) ($file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)));
            $basename = $file_name . '.' . $extension;
            $moved = $file->move($dir, $basename);
            if (! $moved) {
                continue;
            }

            $finalPath = $dir . DIRECTORY_SEPARATOR . $basename;
            $file_path = $model . '/' . $basename;

            if ($converter->isConvertibleExtension($extension)) {
                $pdfPath = $converter->convertToPdf($finalPath);
                if ($pdfPath && is_file($pdfPath) && filesize($pdfPath) > 0) {
                    if (is_file($finalPath) && $finalPath !== $pdfPath) {
                        @unlink($finalPath);
                    }
                    $extension = 'pdf';
                    $basename = $file_name . '.pdf';
                    $file_path = $model . '/' . $basename;
                    $description = pathinfo($description, PATHINFO_FILENAME) . '.pdf';
                    $finalPath = $pdfPath;
                }
            }

            $lastPath = $file_path;

            if ($record_id) {
                CustomAttachment::create([
                    'model' => $model,
                    'path' => $file_path,
                    'name' => $description,
                    'stored_filename' => basename($file_path),
                    'record_id' => $record_id,
                ]);
            }
        }

        return $lastPath;
    }

    public function toggleLike($forumId, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
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

        if ($liked && $userId) {
            $forumModel = Forum::find($forumId);
            if ($forumModel) {
                ForumThreadActivityNotifier::notifyForumLiked($forumModel, (int) $userId);
            }
        }

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

    public function toggleCommentLike($commentId, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
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

        if ($liked && $userId) {
            $commentModel = ForumComment::query()->with('forum')->find($commentId);
            if ($commentModel && $commentModel->forum) {
                ForumThreadActivityNotifier::notifyCommentLiked($commentModel->forum, $commentModel, (int) $userId);
            }
        }

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

    /**
     * Forum threads the user joined via ForumSubscription (approved / live threads only).
     */
    public function getSubscribedForUser(int $userId, Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $rows = (int) ($request->input('page_size') ?: $request->input('rows') ?: 20);
        $rows = max(1, min(50, $rows));

        $forumIds = ForumSubscription::query()
            ->where('user_id', $userId)
            ->pluck('forum_id');

        if ($forumIds->isEmpty()) {
            return Forum::query()->whereRaw('0 = 1')->paginate($rows);
        }

        $forums = Forum::with(['user', 'tags'])
            ->withCount([
                'comments as total_comments' => function ($query) {
                    $query->whereNull('parent_id');
                },
                'likes as total_likes',
            ])
            ->whereIn('id', $forumIds)
            ->where('status', 1)
            ->where('is_approved', 1)
            ->where(function ($q) {
                $q->where('is_rejected', 0)->orWhereNull('is_rejected');
            });

        if ($request->filled('term')) {
            $term = trim((string) $request->input('term'));
            if ($term !== '') {
                $this->applyForumTermSearch($forums, $term);
            }
        }

        return $forums->orderBy('created_at', 'desc')->paginate($rows)->withQueryString();
    }

    public function logForumApprovalEvent(
        int $forumId,
        string $action,
        ?string $reason = null,
        ?array $metadata = null,
        ?int $performedBy = null
    ): ForumApprovalLog {
        $actor = $performedBy ?? (current_user() ? (int) current_user()->id : null);
        $actorName = null;
        if ($actor) {
            $actorName = User::query()->whereKey($actor)->value('name');
        }

        return ForumApprovalLog::create([
            'forum_id' => $forumId,
            'action' => $action,
            'performed_by' => $actor,
            'performed_by_name' => $actorName,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, ForumApprovalLog|object>
     */
    public function approvalTrailForForum(Forum $forum)
    {
        $logs = ForumApprovalLog::query()
            ->where('forum_id', $forum->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        if ($logs->isNotEmpty()) {
            return $logs;
        }

        return $this->synthesizeLegacyForumApprovalTrail($forum);
    }

    /**
     * @return \Illuminate\Support\Collection<int, object>
     */
    protected function synthesizeLegacyForumApprovalTrail(Forum $forum): Collection
    {
        $forum->loadMissing(['user', 'approver', 'rejector']);
        $entries = collect();

        if ($forum->user) {
            $entries->push((object) [
                'action' => 'submitted',
                'action_label' => 'Submitted for review',
                'performed_by_name' => $forum->user->name,
                'reason' => null,
                'created_at' => $forum->created_at,
                'is_legacy' => true,
            ]);
        }

        if ((int) ($forum->is_rejected ?? 0) === 1) {
            $entries->push((object) [
                'action' => 'rejected',
                'action_label' => 'Rejected',
                'performed_by_name' => $forum->rejector->name
                    ?? ($forum->rejected_by ? 'User #'.$forum->rejected_by : 'Moderator not recorded'),
                'reason' => $forum->rejected_reason,
                'created_at' => $forum->updated_at ?? $forum->created_at,
                'is_legacy' => true,
            ]);
        } elseif ((int) ($forum->is_approved ?? 0) === 1 && (int) ($forum->status ?? 0) === 1) {
            $entries->push((object) [
                'action' => !empty($forum->approved_by) ? 'approved' : 'legacy_approved',
                'action_label' => !empty($forum->approved_by) ? 'Approved' : 'Approved (moderator not recorded)',
                'performed_by_name' => $forum->approver->name
                    ?? 'Moderator not recorded — may predate approval tracking',
                'reason' => null,
                'created_at' => $forum->updated_at ?? $forum->created_at,
                'is_legacy' => true,
            ]);
        }

        return $entries->sortByDesc(function ($entry) {
            return $entry->created_at;
        })->values();
    }

    public function moderatorDisplayName(Forum $forum): string
    {
        if ((int) ($forum->is_rejected ?? 0) === 1) {
            if (!empty($forum->rejected_by) && $forum->rejector) {
                return 'Rejected by '.($forum->rejector->name ?? 'Unknown');
            }

            return 'Rejected (moderator not recorded)';
        }

        if ((int) ($forum->is_approved ?? 0) === 1 && (int) ($forum->status ?? 0) === 1) {
            if (!empty($forum->approved_by) && $forum->approver) {
                return 'Approved by '.($forum->approver->name ?? 'Unknown');
            }

            return 'Approved (moderator not recorded)';
        }

        return '—';
    }

    /**
     * @return array{approved:int,pending:int,rejected:int}
     */
    public function adminForumIndexStats(): array
    {
        $base = Forum::query();

        return [
            'approved' => (clone $base)->where('status', 1)->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })->count(),
            'pending' => (clone $base)->pendingApproval()->count(),
            'rejected' => (clone $base)->where('is_rejected', 1)->count(),
        ];
    }

    public function buildAdminForumsQuery(Request $request, string $queue)
    {
        $forums = Forum::query()
            ->with(['user', 'approver', 'rejector']);

        if ($queue === 'pending') {
            $forums->pendingApproval();
        } elseif ($queue === 'approved') {
            $forums->where('status', 1)
                ->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                });
        } elseif ($queue === 'rejected') {
            $forums->where('is_rejected', 1);
        }

        if ($request->filled('term')) {
            $this->applyForumTermSearch($forums, trim((string) $request->term));
        }

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $this->applyForumTermSearch($forums, $search);
        }

        return $forums;
    }

    protected function adminForumsRecordsTotal(string $queue): int
    {
        $request = new Request();

        return $this->buildAdminForumsQuery($request, $queue)->count();
    }

    public function adminForumsDatatable(Request $request, string $queue): array
    {
        $draw = (int) $request->input('draw', 1);
        $start = max(0, (int) $request->input('start', 0));
        $length = min(max(1, (int) $request->input('length', 20)), 100);

        $base = $this->buildAdminForumsQuery($request, $queue);
        $recordsTotal = $this->adminForumsRecordsTotal($queue);
        $recordsFiltered = (clone $base)->count();

        $orderColIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderMap = [1 => 'id', 2 => 'forum_title', 3 => 'forum_description', 4 => 'created_by', 5 => 'created_at'];
        $orderCol = $orderMap[$orderColIndex] ?? 'id';
        $base->orderBy($orderCol, $orderDir);

        $rows = $base->skip($start)->take($length)->get();

        $data = [];
        $index = $start + 1;
        foreach ($rows as $forum) {
            $created = $forum->created_at
                ? Carbon::parse($forum->created_at)->format('M d, Y')
                : '-';

            $title = e($forum->forum_title ?? '');
            if ((int) ($forum->is_resubmission_pending ?? 0) === 1) {
                $title .= ' <span class="badge badge-warning text-dark ml-1" title="Author resubmitted after a previous rejection">Resubmission</span>';
            }

            $description = e(truncate(strip_tags((string) ($forum->forum_description ?? '')), 100));
            $author = e($forum->user->name ?? '');
            $moderator = '<div class="pub-cell-wrap"><span class="text-muted">'.e($this->moderatorDisplayName($forum)).'</span>'
                .' <a href="'.url('admin/forums/details').'?id='.$forum->id.'#approval-trail" class="small" title="View approval trail">Trail</a></div>';

            $rowPending = (int) ($forum->is_rejected ?? 0) === 0
                && (int) ($forum->is_approved ?? 0) === 0
                && (int) ($forum->status ?? 0) === 0;

            $actions = '<div class="pub-actions-group">';
            if ($rowPending) {
                $actions .= '<a href="'.url('admin/forums/details').'?id='.$forum->id.'" class="btn btn-sm btn-outline-primary mr-1" title="Review"><i class="fa fa-edit"></i></a>';
            }
            $actions .= '<a href="'.url('admin/forums/details').'?id='.$forum->id.'" class="btn btn-sm btn-outline-dark mr-1" title="Details"><i class="fa fa-info-circle"></i></a>'
                .'<button type="button" class="btn btn-sm btn-outline-danger mr-1" onclick="openDeleteModal(\''.$forum->id.'\')" title="Delete"><i class="fa fa-trash"></i></button>'
                .'<a href="'.e(forum_thread_url($forum)).'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="View on site"><i class="fa fa-external-link-alt"></i></a>'
                .'</div>';

            $data[] = [
                'index' => '<span class="text-muted">'.$index++.'</span>',
                'title' => $title,
                'description' => $description,
                'author' => $author,
                'created_at' => $created,
                'moderator' => $moderator,
                'actions' => $actions,
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    private function assignForumSlug(Forum $forum): void
    {
        if (! DBSchema::hasColumn('forums', 'slug') || ! empty($forum->slug)) {
            return;
        }

        $forum->slug = SeoSlugger::forForum(
            (string) ($forum->forum_title ?? ''),
            $forum->id ?: null
        );
    }

    public function forumEngagementScore(object $forum): int
    {
        $views = (int) ($forum->views ?? 0);
        $comments = (int) ($forum->total_comments ?? count($forum->comments ?? []));
        $likes = (int) ($forum->total_likes ?? count($forum->likes ?? []));

        return $views + ($comments * 3) + ($likes * 2);
    }

    /**
     * @param  \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|array<int, Forum>  $forums
     */
    public function attachForumListingEnhancements($forums): void
    {
        if ($forums instanceof \Illuminate\Contracts\Pagination\Paginator) {
            $this->decorateForumListingCollection($forums->getCollection());

            return;
        }

        $this->decorateForumListingCollection(collect($forums));
    }

    /**
     * @param  Collection<int, Forum>  $forums
     */
    protected function decorateForumListingCollection(Collection $forums): void
    {
        if ($forums->isEmpty()) {
            return;
        }

        $rankMap = $this->forumPopularityRankMap();

        foreach ($forums as $forum) {
            if (! $forum instanceof Forum) {
                continue;
            }
            $score = $this->forumEngagementScore($forum);
            $forum->setAttribute('engagement_score', $score);
            $forum->setAttribute('popularity_rank', $rankMap[(int) $forum->id] ?? null);
        }

        $this->attachForumContributorFaces($forums);
    }

    /**
     * @return array<int, int> forum_id => rank (1 = highest engagement)
     */
    protected function forumPopularityRankMap(): array
    {
        return Cache::remember('forums_popularity_ranks_v1', 300, function () {
            $rows = Forum::query()
                ->where('status', 1)
                ->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })
                ->withCount([
                    'comments as total_comments' => function ($query) {
                        $query->whereNull('parent_id');
                    },
                    'likes as total_likes',
                ])
                ->get(['id', 'views']);

            $scored = $rows->map(function ($forum) {
                return [
                    'id' => (int) $forum->id,
                    'score' => $this->forumEngagementScore($forum),
                ];
            })->sortByDesc('score')->values();

            $map = [];
            foreach ($scored as $index => $row) {
                $map[(int) $row['id']] = $index + 1;
            }

            return $map;
        });
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getRecentForumsForSidebar(int $limit = 5): Collection
    {
        return Cache::remember('forums_recent_sidebar_v1_' . $limit, 300, function () use ($limit) {
            return Forum::query()
                ->where('status', 1)
                ->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get(['id', 'forum_title', 'slug', 'forum_image', 'created_at']);
        });
    }

    /**
     * @return Collection<int, object{tag: string, topics_count: int}>
     */
    public function getSidebarTopicCategories(int $limit = 14): Collection
    {
        return Cache::remember('forums_sidebar_categories_v1_' . $limit, 600, function () use ($limit) {
            return ForumTag::query()
                ->join('forums', 'forums.id', '=', 'forum_tags.forum_id')
                ->where('forums.status', 1)
                ->where('forums.is_approved', 1)
                ->where(function ($q) {
                    $q->where('forums.is_rejected', 0)->orWhereNull('forums.is_rejected');
                })
                ->select('forum_tags.tag', DB::raw('COUNT(DISTINCT forum_tags.forum_id) as topics_count'))
                ->groupBy('forum_tags.tag')
                ->orderByDesc('topics_count')
                ->orderBy('forum_tags.tag')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getTopForumsByEngagement(int $limit = 5): Collection
    {
        return Cache::remember('forums_top_by_engagement_v1_' . $limit, 300, function () use ($limit) {
            $forums = Forum::query()
                ->where('status', 1)
                ->where('is_approved', 1)
                ->where(function ($q) {
                    $q->where('is_rejected', 0)->orWhereNull('is_rejected');
                })
                ->with(['user'])
                ->withCount([
                    'comments as total_comments' => function ($query) {
                        $query->whereNull('parent_id');
                    },
                    'likes as total_likes',
                ])
                ->get(['id', 'forum_title', 'slug', 'views', 'created_at', 'created_by']);

            return $forums
                ->sortByDesc(fn ($forum) => $this->forumEngagementScore($forum))
                ->take($limit)
                ->values()
                ->each(function ($forum) {
                    $forum->setAttribute('engagement_score', $this->forumEngagementScore($forum));
                });
        });
    }

    /**
     * @param  Collection<int, Forum>  $forums
     */
    protected function attachForumContributorFaces(Collection $forums, int $maxFaces = 14): void
    {
        $forumIds = $forums->pluck('id')->map(fn ($id) => (int) $id)->filter(fn ($id) => $id > 0)->values()->all();
        if ($forumIds === []) {
            return;
        }

        $likesByForum = ForumLike::query()
            ->whereIn('forum_id', $forumIds)
            ->orderByDesc('id')
            ->get(['forum_id', 'user_id'])
            ->groupBy('forum_id');

        $allUserIds = [];
        foreach ($forums as $forum) {
            if (! empty($forum->created_by)) {
                $allUserIds[(int) $forum->created_by] = true;
            }
            foreach ($forum->comments ?? [] as $comment) {
                $uid = (int) ($comment->created_by ?? 0);
                if ($uid > 0) {
                    $allUserIds[$uid] = true;
                }
            }
            foreach ($likesByForum->get($forum->id, collect()) as $like) {
                $uid = (int) ($like->user_id ?? 0);
                if ($uid > 0) {
                    $allUserIds[$uid] = true;
                }
            }
        }

        $userById = collect();
        if ($allUserIds !== []) {
            $userById = User::query()
                ->whereIn('id', array_keys($allUserIds))
                ->get(['id', 'name', 'photo', 'updated_at', 'job_title', 'is_photo_external', 'author_id'])
                ->keyBy('id');
        }

        $onlineBefore = Carbon::now()->subMinutes(20);

        foreach ($forums as $forum) {
            $ordered = [];
            $seen = [];

            $push = function (int $uid, string $role) use (&$ordered, &$seen, $maxFaces, $userById, $onlineBefore) {
                if ($uid <= 0 || isset($seen[$uid]) || count($ordered) >= $maxFaces) {
                    return;
                }
                $user = $userById->get($uid);
                if (! $user) {
                    return;
                }
                $seen[$uid] = true;
                $ordered[] = [
                    'user' => $user,
                    'role' => $role,
                    'online' => $user->updated_at && $user->updated_at->gt($onlineBefore),
                ];
            };

            if (! empty($forum->created_by)) {
                $push((int) $forum->created_by, 'author');
            }

            foreach ($forum->comments ?? [] as $comment) {
                $push((int) ($comment->created_by ?? 0), 'contributor');
            }

            foreach ($likesByForum->get($forum->id, collect()) as $like) {
                $push((int) ($like->user_id ?? 0), 'member');
            }

            $faces = collect($ordered);
            $forum->setAttribute('listing_contributor_faces', $faces);
            $forum->setAttribute('listing_more_contributors_not_shown', max(0, count($seen) - $faces->count()));
        }
    }

}
