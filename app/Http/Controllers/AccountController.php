<?php

namespace App\Http\Controllers;

use App\Repositories\PublicationsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use App\Models\AccessLevel;
use App\Models\PdfChatSession;
use App\Support\ContributorStats;
use App\Support\PublicationSubmissionValidation;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    private $publicationsRepo,$usersRepo;

    public function __construct( PublicationsRepository $publicationsRepo,UsersRepository $usersRepo)
    {
        $this->publicationsRepo       = $publicationsRepo;
        $this->usersRepo            = $usersRepo;
    }


    public function profile(Request $request){

        $user = current_user();
        $data['user'] = $user;
        // Preload selected preferences as an array of SubThemeticArea IDs
        // Avoid ambiguous column 'id' by qualifying the table name
        $data['preferences'] = [];
        if ($user) {
            $data['preferences'] = $user->preferences()->pluck('subtheme_id')->toArray();
        }
        $data['access_groups'] = AccessLevel::all();
        $data['contributorPublicProfile'] = ContributorStats::forUser($user);
        
        return view('account.profile',$data);
    }

    public function verifyAccount(Request $request){

        $verified = $this->usersRepo->verify_account($request);
        $message  = ($verified)?'Account verified and activated successfully! You can now login.':'Verification failed. Invalid or expired token. Please try again.';
        $alert_class  = ($verified)?'success':'danger';

        return redirect()->route('login')->with(['alert'=>$message,'alert_class'=>$alert_class]);
    }


    public function favourites(Request $request){

        $data['favourites'] = $this->publicationsRepo->favourites($request);
        
        // Get recommended content by preferences (10 max)
        $data['recommendedByPreferences'] = $this->publicationsRepo->recommendedByPreferences(auth()->user()->id, 10);
        
        // Get related content by favorite tags (10 max)
        $data['relatedByFavoriteTags'] = $this->publicationsRepo->relatedByFavoriteTags(auth()->user()->id, 10);
        
        return view('account.favourites',$data);
    }


    public function publications(Request $request){
        if ($request->ajax() && $request->boolean('datatable')) {
            return response()->json($this->publicationsRepo->accountMyPublicationsDatatable($request));
        }

        // Get statistics for the user's publications
        $userId = auth()->id();
        $userPublications = \App\Models\Publication::where('user_id', $userId)->get();
        
        $stats = [
            'total' => $userPublications->count(),
            'approved' => $userPublications->where('is_approved', 1)->count(),
            'pending' => $userPublications->where('is_approved', 0)->where('is_rejected', 0)->count(),
            'rejected' => $userPublications->where('is_rejected', 1)->count(),
            'total_views' => 0,
        ];
        
        // Calculate total views across all publications
        foreach ($userPublications as $pub) {
            $stats['total_views'] += \App\Models\PublicationView::getTotalViews($pub->id);
        }
        
        // Get forum engagement statistics
        $stats['forum_posts'] = \App\Models\ForumEngagement::getTotalForumPosts($userId);
        $stats['forum_comments'] = \App\Models\ForumEngagement::getTotalForumComments($userId);
        $stats['forum_engagements'] = \App\Models\ForumEngagement::getTotalEngagements($userId);
        
        // Get communities the user belongs to
        $stats['communities'] = \App\Models\CommunityOfPracticeMembers::where('user_id', $userId)
            ->where('is_approved', 1)
            ->with('community')
            ->get()
            ->map(fn ($membership) => $membership->community)
            ->filter()
            ->map(fn ($community) => [
                'id' => (int) $community->id,
                'name' => (string) $community->community_name,
                'url' => community_detail_url($community),
            ])
            ->values()
            ->all();
        
        $data['stats'] = $stats;
        return view('account.mypublications', $data);
    }


    public function publish(Request $request){

        return view('account.create');
    }

    public function edit_publication(Request $request){
        // Support both legacy ?ref= and newer ?id= links.
        $publicationId = $request->input('id', $request->input('ref'));
        if (!$publicationId) {
            abort(404);
        }

        $publication = $this->publicationsRepo->find($publicationId, false);
        if (!$publication) {
            abort(404);
        }

        // Security: only owner or admin can edit.
        $user = current_user();
        $canAdminEdit = is_admin() || ($user && method_exists($user, 'can') && $user->can('view_publications'));
        if (!$canAdminEdit && (int)$publication->user_id !== (int)($user->id ?? 0)) {
            abort(403);
        }

        $data['publication'] = $publication;
        return view('account.editpub',$data);
    }

    public function create_version(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        
        return view('account.create_version',$data);
    }


    public function submit_publication(Request $request){

        // For non-admin users, ensure they have an author_id set
        if (!is_admin() && !auth()->user()->author_id) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.',
                    'alert_class' => 'danger'
                ], 422);
            }
            return back()->withErrors([
                'author' => 'Your account is not associated with an author. Please contact the administrator to link your account to an author.'
            ])->withInput();
        }

        if ($request->original_id) {
            $request->merge(['file_type' => 1]);
        }

        if (! $request->is_active) {
            $request['is_active'] = 'In-Active';
        }

        $val_rules = PublicationSubmissionValidation::rules($request);
        $messages = PublicationSubmissionValidation::messages($request);

        try {
            $request->validate($val_rules, $messages);
            PublicationSubmissionValidation::assertAttachmentFilesAllowed($request);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                $flat = collect($e->errors())->flatten();

                return response()->json([
                    'status' => 'error',
                    'message' => $flat->first() ?: 'Please fix the errors below.',
                    'errors' => $e->errors(),
                    'alert_class' => 'danger',
                ], 422);
            }

            throw $e;
        }

        // Default: show disclaimer if checkbox not sent
        if (!$request->has('show_disclaimer')) {
            $request['show_disclaimer'] = 1;
        }

        $saved   = $this->publicationsRepo->save($request);
        $message = ($saved)?'Publication saved successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert'] = $message;
        $data['status']      = ($saved)?'success':'error';

        if($request->ajax())
           return response()->json($data, $saved ? 200 : 400);

        return redirect()->route('account.publications')->with($data);

    }

    public function create_summary(Request $request){

        $data['publication'] = $this->publicationsRepo->find($request->id);
        return view('account.create_summary',$data);
    }


    public function submit_summary(Request $request){

        $val_rules = [
            //'file_type'=>'required',
            'summary'  =>'required',
            'title'    =>'required'
        ];

        $request->validate($val_rules);

        $saved   = $this->publicationsRepo->save_summary($request);
        $message = ($saved)?'Resource summary submitted successfully':'Request failed try again';

        $data['alert_class'] = ($saved)?'success':'danger';
        $data['message']     = $data['alert']= $message;
        $data['status']      = 200;

        if($request->ajax())
           return response($data);

        return redirect(publication_url($request->original_id))->with($data);

    }

    public function delete_publication(Request $request)
    {
        $this->publicationsRepo->delete($request->id);
    }

    /**
     * List user's PDF chat sessions grouped by document (publication).
     */
    public function chats(Request $request)
    {
        $userId = auth()->id();
        $sessions = PdfChatSession::where('user_id', $userId)
            ->with(['publication:id,title', 'attachment:id,publication_id,file'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Group by publication_id + attachment_id (each doc/attachment = one "document")
        $grouped = [];
        foreach ($sessions as $session) {
            $pub = $session->publication;
            $key = $session->publication_id . '-' . ($session->attachment_id ?? 'main');
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'publication_id' => $session->publication_id,
                    'attachment_id'  => $session->attachment_id,
                    'title'          => $pub ? $pub->title : 'Document #' . $session->publication_id,
                    'sessions'       => [],
                ];
            }
            $grouped[$key]['sessions'][] = [
                'id'           => $session->id,
                'message_count' => $session->messages()->count(),
                'created_at'   => $session->created_at,
                'updated_at'   => $session->updated_at,
            ];
        }

        $data['chatsByDocument'] = array_values($grouped);
        return view('account.chats', $data);
    }

    /**
     * Delete a PDF chat session (and its messages). User must own the session.
     */
    public function deleteChat(Request $request)
    {
        $request->validate(['session_id' => 'required|integer']);
        $session = PdfChatSession::where('id', $request->session_id)
            ->where('user_id', auth()->id())
            ->first();
        if (!$session) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Chat not found or access denied.'], 404);
            }
            return redirect()->route('account.chats')->with('alert', 'Chat not found or access denied.')->with('alert_class', 'danger');
        }
        $session->messages()->delete();
        $session->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('account.chats')->with('alert', 'Chat deleted.')->with('alert_class', 'success');
    }

}
