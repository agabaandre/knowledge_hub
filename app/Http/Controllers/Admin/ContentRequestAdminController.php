<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\CommunityOfPractice;
use App\Models\ContentRequest;
use App\Models\ContentRequestReferralMessage;
use App\Models\User;
use App\Jobs\SendContentRequestForumAiSummaryJob;
use App\Services\ContentRequestReferralForumService;
use App\Services\ContentRequestReferralNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContentRequestAdminController extends Controller
{
    public function index(Request $request)
    {
        // Start building the query
        $query = ContentRequest::with(['country', 'processedBy', 'referredToUser', 'referredToCommunity']);

        // Filter by status (processed/pending/referred)
        if ($request->filled('status')) {
            if ($request->status === 'processed') {
                $query->whereNotNull('processed_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('processed_at');
            } elseif ($request->status === 'referred') {
                $query->whereNotNull('referral_type')->whereNotNull('referred_at');
            }
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Search filter (subject, email, or description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order and paginate
        $contentRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));

        return view('admin.content_requests.index', compact('contentRequests'));
    }

    public function create()
    {
        // Show the form to create a new content request
        return view('admin.content_requests.create');
    }

    public function store(Request $request)
    {
        // Validate and store the new content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
        ]);

        ContentRequest::create($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request created successfully.');
    }

    public function edit($id)
    {
        // Fetch the content request for editing
        $contentRequest = ContentRequest::findOrFail($id);
        return view('admin.content_requests.edit', compact('contentRequest'));
    }

    public function update(Request $request, $id)
    {
        // Validate and update the content request
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string',
            'country_id' => 'required|exists:countries,id',
            'email' => 'nullable|email',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->update($request->all());

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request updated successfully.');
    }

    public function destroy($id)
    {
        // Delete the content request
        $contentRequest = ContentRequest::findOrFail($id);
        $contentRequest->delete();

        return redirect()->route('admin.content-requests.index')->with('success', 'Content request deleted successfully.');
    }

    /**
     * Process a content request - mark as processed and send email to requester
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'content_links' => 'required|string|min:10',
            'admin_comments' => 'nullable|string|max:1000',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);

        // Update the content request with processing information
        $contentRequest->update([
            'processed_at' => now(),
            'processed_by' => Auth::id(),
            'content_links' => $request->content_links,
            'admin_comments' => $request->admin_comments,
        ]);

        // Send email to requester
        if ($contentRequest->email) {
            $emailData = [
                'to' => $contentRequest->email,
                'subject' => 'Your Content Request Has Been Processed - ' . $contentRequest->subject,
                'title' => 'Content Request Processed',
                'body' => view('emails.content_request_processed', [
                    'contentRequest' => $contentRequest,
                    'contentLinks' => $request->content_links,
                    'adminComments' => $request->admin_comments,
                ])->render(),
            ];

            SendMailJob::dispatch($emailData)->onQueue('default');
        }

        return redirect()->route('admin.content-requests.index')
            ->with('success', 'Content request processed successfully and email sent to requester.');
    }

    /**
     * Refer a request to a hub user or a community for discussion; notifies requester and assignees.
     */
    public function refer(Request $request, $id)
    {
        $request->validate([
            'referral_type' => 'required|in:user,community',
            'referred_to_user_id' => 'required_if:referral_type,user|nullable|exists:users,id',
            'referred_to_community_id' => 'required_if:referral_type,community|nullable|exists:community_of_practices,id',
            'referral_notes' => 'nullable|string|max:5000',
        ]);

        $contentRequest = ContentRequest::findOrFail($id);

        if ($contentRequest->isReferred()) {
            return redirect()->route('admin.content-requests.index')
                ->with('error', 'This request has already been referred. Open the hub discussion to add messages.');
        }

        $token = $contentRequest->requestor_track_token ?: Str::random(48);

        $contentRequest->update([
            'referral_type' => $request->referral_type,
            'referred_to_user_id' => $request->referral_type === 'user' ? $request->referred_to_user_id : null,
            'referred_to_community_id' => $request->referral_type === 'community' ? $request->referred_to_community_id : null,
            'referred_at' => now(),
            'referred_by' => Auth::id(),
            'referral_notes' => $request->referral_notes,
            'requestor_track_token' => $token,
        ]);

        $contentRequest->refresh();

        $forum = null;
        if ($request->referral_type === 'community') {
            $forum = ContentRequestReferralForumService::createForCommunityReferral($contentRequest->fresh(), (int) Auth::id());
            if ($forum) {
                $contentRequest->update(['referral_forum_id' => $forum->id]);
                $contentRequest->refresh();
            }
        }

        $intro = trim((string) $request->referral_notes) !== ''
            ? strip_tags($request->referral_notes)
            : 'This request has been referred on the Knowledge Hub for follow-up and discussion.';
        if ($forum) {
            $intro .= "\n\nCommunity forum thread: ".ContentRequestReferralForumService::forumThreadUrl($forum->id);
        }

        ContentRequestReferralMessage::create([
            'content_request_id' => $contentRequest->id,
            'user_id' => Auth::id(),
            'posted_via_track' => false,
            'body' => $intro,
        ]);

        ContentRequestReferralNotifier::notifyReferralCreated(
            $contentRequest->fresh(['referredToUser', 'referredToCommunity', 'referredByUser', 'country'])
        );

        if ($forum && $contentRequest->email) {
            SendContentRequestForumAiSummaryJob::dispatch($contentRequest->id)->delay(now()->addSeconds(20));
        }

        return redirect()->route('admin.content-requests.index')
            ->with('success', 'Request referred. The requester and assignee(s) have been notified by email.');
    }
}
