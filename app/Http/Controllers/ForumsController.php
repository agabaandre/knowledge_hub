<?php

namespace App\Http\Controllers;

use App\Models\ContentRequest;
use App\Models\CustomAttachment;
use App\Models\Forum;
use App\Models\ForumCommunityOfPractice;
use App\Models\Tag;
use App\Repositories\ForumsRepository;
use App\Services\OfficeDocumentToPdfService;
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
        $data['forums']    = $this->forumsRepo->get($request, 1, null, false);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $data['search']    = (object) $request->all();

        // Get related content based on forum tags
        $forumTagTexts = [];
        $forumTagIds = [];
        foreach ($data['forums'] as $forum) {
            if ($forum->tags && $forum->tags->count() > 0) {
                foreach ($forum->tags as $tag) {
                    $tagText = $tag->tag ?? null;
                    if ($tagText && !in_array($tagText, $forumTagTexts)) {
                        $forumTagTexts[] = $tagText;
                        // Get tag ID from Tag model
                        $tagModel = \App\Models\Tag::where('tag_text', $tagText)->first();
                        if ($tagModel) {
                            $forumTagIds[] = $tagModel->id;
                        }
                    }
                }
            }
        }
        
        // Get unique tag IDs
        $uniqueTagIds = array_unique($forumTagIds);
        $uniqueTagTexts = array_unique($forumTagTexts);
        
        // Get related forums (exclude current forums)
        $currentForumIds = [];
        if ($data['forums'] instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $currentForumIds = $data['forums']->getCollection()->pluck('id')->toArray();
        } elseif (is_iterable($data['forums'])) {
            foreach ($data['forums'] as $forum) {
                $currentForumIds[] = $forum->id;
            }
        }
        $relatedForums = collect();
        if (!empty($uniqueTagTexts)) {
            $relatedForums = \App\Models\Forum::whereHas('tags', function($query) use ($uniqueTagTexts) {
                                                $query->whereIn('tag', $uniqueTagTexts);
                                            })
                                            ->where('is_approved', 1)
                                            ->where('status', 1)
                                            ->whereNotIn('id', $currentForumIds)
                                            ->with(['user', 'tags'])
                                            ->withCount([
                                                'comments as total_comments' => function($query) {
                                                    $query->whereNull('parent_id');
                                                }, 
                                                'likes as total_likes'
                                            ])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
        }
        $data['relatedForums'] = $relatedForums;
        
        // Get related publications
        $relatedPublications = collect();
        if (!empty($uniqueTagIds)) {
            $relatedPublications = \App\Models\Publication::whereHas('tags', function($query) use ($uniqueTagIds) {
                                                $query->whereIn('tag_id', $uniqueTagIds);
                                            })
                                            ->where('is_version', 0)
                                            ->where('is_active', 'Active')
                                            ->where('is_approved', 1)
                                            ->when(!is_admin(), function($query) {
                                                $query->where('is_admin_only_access', 0);
                                            })
                                            ->with(['author', 'tags'])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
        }
        $data['relatedPublications'] = $relatedPublications;
        
        // Get related communities
        $relatedCommunities = collect();
        if (!empty($uniqueTagIds)) {
            $relatedCommunities = \App\Models\CommunityOfPractice::whereHas('tags', function($query) use ($uniqueTagIds) {
                                                $query->whereIn('tags.id', $uniqueTagIds);
                                            })
                                            ->where('is_active', 1)
                                            ->with(['creator', 'region', 'country', 'tags'])
                                            ->withCount([
                                                'approvedMembers as members_count',
                                                'communityForums as forums_count',
                                                'communityPublications as publications_count'
                                            ])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
        }
        $data['relatedCommunities'] = $relatedCommunities;

        // SEO variables
        $data['pageTitle'] = 'Discussion Forums - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
        $data['pageDescription'] = 'Join public health discussion forums, share insights, ask questions, and collaborate with experts across Africa. Participate in health-related discussions and knowledge exchange.';
        $data['pageKeywords'] = 'discussion forums, public health forums, health discussions, Africa CDC forums, health experts, ' . (settings()->seo_keywords ?? '');
        $data['pageImage'] = settings()->logo ?? asset('assets/images/logo.png');
        $data['canonicalUrl'] = url('forums');
        $data['ogType'] = 'website';

        return view('forums.index', $data);
    }

    public function myForums(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $data['forums'] = $this->forumsRepo->getByUser($userId, $request);
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        $data['search'] = (object) $request->all();

        return view('forums.index', $data);
    }

    public function thread(Request $request)
    {
        $data['forum']     = $this->forumsRepo->find($request->id);
        $data['linkedContentRequest'] = ContentRequest::query()
            ->where('referral_forum_id', (int) $request->id)
            ->with(['country', 'referredToCommunity'])
            ->first();
        $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
        
        // SEO variables (linked content-request forums use privacy-safe copy in the view’s @php as well)
        if ($data['forum']) {
            $forum = $data['forum'];
            $data['pageTitle'] = ($forum->forum_title ?? 'Forum Discussion') . ' - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
            if (! empty($data['linkedContentRequest'])) {
                $data['pageDescription'] = 'Community discussion for a Knowledge Hub content request. Requester contact details are not shown on this page.';
            } else {
                $data['pageDescription'] = \Illuminate\Support\Str::limit(strip_tags($forum->forum_description ?? ''), 160) ?: ($forum->forum_title . ' - Join the discussion on this public health forum topic.');
            }
            $data['pageKeywords'] = 'forum discussion, ' . ($forum->forum_title ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
            
            // Use forum image if available, otherwise default
            $forumImage = null;
            if (!empty($forum->forum_image) && is_image($forum->forum_image)) {
                $forumImage = filter_var($forum->forum_image, FILTER_VALIDATE_URL) ? $forum->forum_image : asset($forum->forum_image);
            }
            $data['pageImage'] = $forumImage ?? settings()->logo ?? asset('assets/images/logo.png');
            $data['canonicalUrl'] = url('forums/thread?id=' . $forum->id);
            $data['ogType'] = 'article';
        }
        $request['rows']   = 6;
        $data['search']    = (object) $request->all();
        $data['forums']    = $this->forumsRepo->get($request, 1, null, false);

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

    public function myDiscussions(Request $request)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $data['threads'] = $this->forumsRepo->getAuthoredForumThreads((int) auth()->id(), $request);

        return view('account.my_discussions', $data);
    }

    public function editMyDiscussion(Forum $forum)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }
        if ((int) $forum->created_by !== (int) auth()->id()) {
            abort(403);
        }
        if ((int) ($forum->is_approved ?? 0) === 1 && (int) ($forum->status ?? 0) === 1) {
            abort(403, 'Published posts cannot be edited here.');
        }

        $forum->load(['tags']);
        $selectedCommunityIds = ForumCommunityOfPractice::where('forum_id', $forum->id)
            ->pluck('community_of_practice_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->toArray();

        $tagTexts = $forum->tags->pluck('tag')->filter()->values()->toArray();
        $selectedTagIds = [];
        if (count($tagTexts)) {
            $selectedTagIds = Tag::whereIn('tag_text', $tagTexts)->pluck('id')->map(function ($id) {
                return (int) $id;
            })->toArray();
        }

        return view('account.edit_forum_post', [
            'forum' => $forum,
            'selectedCommunityIds' => $selectedCommunityIds,
            'selectedTagIds' => $selectedTagIds,
        ]);
    }

    public function saveMyDiscussion(Request $request, Forum $forum)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'required|string|max:200000',
        ]);

        $wasRejected = (int) ($forum->is_rejected ?? 0) === 1;

        $ok = $this->forumsRepo->updateUnpublishedForumByAuthor($request, $forum);
        if (! $ok) {
            return back()
                ->withErrors(['form' => 'Could not update this post. You may not be the author, or it may already be published.'])
                ->withInput();
        }

        $message = $wasRejected
            ? 'Your discussion was resubmitted for approval.'
            : 'Your changes were saved. Your post is still awaiting approval.';

        return redirect()
            ->route('account.my-discussions')
            ->with('message', $message)
            ->with('alert_class', 'success');
    }

    
    public function publish(Request $request)
    {
       $saved = $this->forumsRepo->save($request);
   
        $message = ($saved)?'Forum submitted for approval':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = 200;
        return back();
    }

    public function comment(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'You must be logged in to comment.');
        }

        $request->validate([
            'id' => 'required|integer',
            'comment' => 'required|string|max:20000',
            'parent_id' => 'nullable|integer',
        ]);

        $commentText = trim((string) $request->input('comment'));
        $wordCount = count(preg_split('/\s+/u', $commentText, -1, PREG_SPLIT_NO_EMPTY));
        if ($wordCount > 300) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Comments are limited to 300 words.',
                ], 422);
            }

            return back()->withErrors(['comment' => 'Comments are limited to 300 words.'])->withInput();
        }

        $request->merge(['comment' => $commentText]);

        // Log request details for debugging
        \Log::info('Forum comment request received', [
            'ajax' => $request->ajax(),
            'has_files' => $request->hasFile('attachments'),
            'files_count' => $request->hasFile('attachments') ? (is_array($request->file('attachments')) ? count($request->file('attachments')) : 1) : 0,
            'comment_length' => strlen($request->comment ?? ''),
            'forum_id' => $request->id,
            'parent_id' => $request->parent_id ?? null
        ]);

        $comment = $this->forumsRepo->save_comment($request);

        if ($request->ajax() || $request->wantsJson()) {
            // Reload comment with relationships for AJAX response
            $comment->refresh();
            $comment->load(['user', 'likes']);
            
            // Ensure user photo accessor is triggered by accessing it
            if ($comment->user) {
                // Access the photo attribute to trigger the getPhotoAttribute accessor
                $photo = $comment->user->photo;
            }
            
            // Reload comment to ensure all data is fresh, including attachments
            $comment->refresh();
            
            // Trigger attachments accessor to load attachments from custom_attachments table
            // The getAttachmentsAttribute accessor queries CustomAttachment where model='forum_comments'
            $attachments = $comment->attachments;
            
            \Log::info('Comment attachments loaded for AJAX response', [
                'comment_id' => $comment->id,
                'attachments_count' => $attachments ? $attachments->count() : 0,
                'attachments_table' => 'custom_attachments',
                'attachments' => $attachments ? $attachments->map(function($a) {
                    // Get raw path before accessor transformation
                    $rawPath = isset($a->attributes['path']) ? $a->attributes['path'] : $a->getOriginal('path');
                    return [
                        'id' => $a->id, 
                        'path' => $a->path, // This uses the accessor which adds storage_link
                        'raw_path' => $rawPath, // Raw path from database
                        'model' => $a->model ?? $a->getOriginal('model'), 
                        'record_id' => $a->record_id ?? $a->getOriginal('record_id'),
                        'created_at' => $a->created_at ? $a->created_at->toDateTimeString() : null
                    ];
                })->toArray() : []
            ]);
            
            // Load attachments manually since it's an accessor
            $forum = $this->forumsRepo->find($request->id, false);
            $data['my_forums'] = $this->forumsRepo->getJoinedForums($request);
            return response()->json([
                'success' => true,
                'message' => 'Comment saved successfully',
                'comment' => $comment,
                'comment_html' => view('forums.partials.comment_item', [
                    'comment' => $comment,
                    'forum' => $forum,
                    'my_forums' => $data['my_forums'] ?? [],
                    'currentUser' => current_user()
                ])->render()
            ]);
        }

        $message = ($comment)?'Comment saved successfully':'Request failed try again';
        $data['alert_class'] = ($comment)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = 200;
        return back()->with($data);
    }

    public function like(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Please login to like'], 401);
        }

        $result = $this->forumsRepo->toggleLike($request->forum_id);
        return response()->json($result);
    }

    public function likeComment(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Please login to like'], 401);
        }

        $result = $this->forumsRepo->toggleCommentLike($request->comment_id);
        return response()->json($result);
    }

    /**
     * Resolve forum comment attachment as PDF: converts legacy office files once, then redirects to storage URL.
     */
    public function commentAttachmentPdf(CustomAttachment $attachment, OfficeDocumentToPdfService $converter)
    {
        abort_unless($attachment->getAttribute('model') === 'forum_comments', 404);

        $relative = $attachment->getRawOriginal('path') ?: $attachment->getAttribute('path');
        if ($relative === null || $relative === '') {
            abort(404);
        }

        $absolute = storage_path('app/public/uploads/' . $relative);
        if (!is_file($absolute)) {
            abort(404);
        }

        $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return redirect()->away(storage_link('uploads/' . $relative));
        }

        if (!$converter->isConvertibleExtension($ext)) {
            return redirect()->away(storage_link('uploads/' . $relative));
        }

        $stem = pathinfo($relative, PATHINFO_FILENAME);
        $dir = str_replace('\\', '/', dirname($relative));
        $pdfRelative = ($dir === '.' || $dir === '') ? $stem . '.pdf' : $dir . '/' . $stem . '.pdf';
        $pdfAbs = storage_path('app/public/uploads/' . $pdfRelative);

        if (!is_file($pdfAbs) || filesize($pdfAbs) === 0) {
            $converter->convertToPdf($absolute);
        }

        if (!is_file($pdfAbs) || filesize($pdfAbs) === 0) {
            return redirect()->away(storage_link('uploads/' . $relative));
        }

        if ($relative !== $pdfRelative) {
            @unlink($absolute);
            $attachment->path = $pdfRelative;
            $baseName = pathinfo($attachment->name ?? pathinfo($relative, PATHINFO_FILENAME), PATHINFO_FILENAME);
            $attachment->name = $baseName . '.pdf';
            $attachment->save();
        }

        return redirect()->away(storage_link('uploads/' . $pdfRelative));
    }

}
