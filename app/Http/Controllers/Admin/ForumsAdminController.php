<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ForumsRepository;
use App\Repositories\QuizRepository;
use App\Models\ContentRequest;
use App\Models\Forum;
use App\Models\ForumComment;
use App\Services\ChatGPTService;

class ForumsAdminController extends Controller
{
    private $forumsRepo;

    public function __construct(ForumsRepository $forumsRepo)
    {
        $this->forumsRepo = $forumsRepo;
    }

    public function index(Request $request)
    {
        $data = [
            'forums' => $this->forumsRepo->get($request, 3, 'pending'),
            'search' => (object) $request->all(),
            'title' => 'Pending approval',
            'forum_admin_queue' => 'pending',
            'forum_list_subtitle' => 'Discussion threads awaiting moderator approval',
        ];

        return view('admin.forums.index', $this->withForumAdminCounts($data));
    }

    public function approved(Request $request)
    {
        $data = [
            'forums' => $this->forumsRepo->get($request, 3, 'approved'),
            'search' => (object) $request->all(),
            'title' => 'Approved forums',
            'forum_admin_queue' => 'approved',
            'forum_list_subtitle' => 'Published discussion threads',
        ];

        return view('admin.forums.index', $this->withForumAdminCounts($data));
    }

    public function rejected(Request $request)
    {
        $data = [
            'forums' => $this->forumsRepo->get($request, 3, 'rejected'),
            'search' => (object) $request->all(),
            'title' => 'Rejected forums',
            'forum_admin_queue' => 'rejected',
            'forum_list_subtitle' => 'Threads that were not approved',
        ];

        return view('admin.forums.index', $this->withForumAdminCounts($data));
    }

    public function moderation(Request $request)
    {
        $data = [
            'forums' => $this->forumsRepo->get($request, 3, 'pending'),
            'search' => (object) $request->all(),
        ];
        $data = $this->withForumAdminCounts($data);

        return view('admin.forums.moderation', $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withForumAdminCounts(array $data): array
    {
        $data['pending_forums_count'] = Forum::pendingApproval()->count();
        $data['pending_forum_comments_count'] = ForumComment::where('status', 'pending')->count();

        return $data;
    }

    public function destroy(Request $request){

        return $this->forumsRepo->delete($request->id);
    }

    
    public function details(Request $request){
        $forum          =  $this->forumsRepo->find($request->id);
        $data['forum']  = $forum;
        
        // Get recent forums for sidebar (excluding current forum)
        $request['rows'] = 6;
        $forumsPaginator = $this->forumsRepo->get($request, 3);
        
        // Filter out current forum from the results
        if ($forumsPaginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $data['forums'] = $forumsPaginator->getCollection()
                ->filter(function($f) use ($forum) {
                    return $f->id != $forum->id;
                })
                ->take(5);
        } else {
            $data['forums'] = collect();
        }
        
        return view('admin.forums.details',$data);
    }


    public function approve(Request $request){
        $this->forumsRepo->approve($request->id);
        return back();
    }

    public function reject(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'rejected_reason' => 'required|string|min:10|max:5000',
        ]);

        $forum = $this->forumsRepo->reject((int) $validated['id'], $validated['rejected_reason']);
        if (! $forum) {
            return back()->with('error', 'Forum not found.');
        }

        return back()->with('success', 'The forum post was rejected and the author was notified.');
    }

    public function updatePending(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'forum_title' => 'required|string|max:500',
            'forum_description' => 'required|string|max:200000',
        ]);

        $ok = $this->forumsRepo->updatePendingModeration(
            (int) $validated['id'],
            $validated['forum_title'],
            $validated['forum_description']
        );

        $wantsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        if (! $ok) {
            if ($wantsJson) {
                return response()->json(['message' => 'This forum is not pending or could not be updated.'], 422);
            }

            return back()->with('error', 'This forum is not pending or could not be updated.');
        }

        if ($wantsJson) {
            return response()->json(['ok' => true, 'message' => 'Forum post updated.']);
        }

        return back()->with('success', 'Forum post updated. You can approve when ready.');
    }

    public function grammarAssist(Request $request, ChatGPTService $chatGPT)
    {
        $request->validate([
            'html' => 'required|string|max:200000',
        ]);

        if (empty(config('ai.open_api_key'))) {
            return response()->json(['ok' => false, 'error' => 'AI is not configured (missing API key).'], 503);
        }

        $result = $chatGPT->proofreadHtmlForGrammar($request->input('html'));

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

}
