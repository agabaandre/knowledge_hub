<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\ContentRequest;
use App\Services\ContentRequestReferralForumService;
use App\Models\ContentRequestReferralMessage;
use App\Services\ContentRequestReferralNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentRequestReferralController extends Controller
{
    public function discuss(ContentRequest $contentRequest)
    {
        if (! $contentRequest->isReferred()) {
            abort(404);
        }

        $user = Auth::user();
        if (! $contentRequest->userMayParticipateInReferralDiscussion($user)) {
            abort(403, 'You are not assigned to this referral discussion.');
        }

        if ($contentRequest->referral_forum_id) {
            return redirect()->to(ContentRequestReferralForumService::forumThreadUrl((int) $contentRequest->referral_forum_id));
        }

        $contentRequest->load(['referralMessages.user', 'country', 'referredToCommunity', 'referredToUser', 'referredByUser']);

        return view('content_requests.discuss', [
            'contentRequest' => $contentRequest,
        ]);
    }

    public function storeMessage(Request $request, ContentRequest $contentRequest)
    {
        if (! $contentRequest->isReferred()) {
            abort(404);
        }

        $user = Auth::user();
        if (! $contentRequest->userMayParticipateInReferralDiscussion($user)) {
            abort(403);
        }

        if ($contentRequest->referral_forum_id) {
            return redirect()
                ->to(ContentRequestReferralForumService::forumThreadUrl((int) $contentRequest->referral_forum_id))
                ->with('info', 'This request is discussed in the community forum thread. Please post your comment there.');
        }

        $request->validate([
            'body' => 'required|string|min:3|max:10000',
        ]);

        $message = ContentRequestReferralMessage::create([
            'content_request_id' => $contentRequest->id,
            'user_id' => $user->id,
            'posted_via_track' => false,
            'body' => strip_tags($request->input('body')),
        ]);

        ContentRequestReferralNotifier::notifyRequestorNewMessage($contentRequest, $message);

        return redirect()
            ->route('content-request.referral.discuss', $contentRequest)
            ->with('success', 'Message posted. The requester has been emailed if an address is on file.');
    }

    /**
     * Mark a referred request as processed (same outcome as admin "Process"): links + optional comments + email to requester.
     * Allowed for system admins / manage_content_requests, or community admins when referral_type is community.
     */
    public function markProcessed(Request $request, ContentRequest $contentRequest)
    {
        if (! $contentRequest->isReferred()) {
            abort(404);
        }

        $user = Auth::user();
        if (! $contentRequest->userMayMarkReferralAsProcessed($user)) {
            abort(403);
        }

        if ($contentRequest->isProcessed()) {
            return redirect()
                ->route('content-request.referral.discuss', $contentRequest)
                ->with('error', 'This request is already marked as processed.');
        }

        $request->validate([
            'content_links' => 'required|string|min:10',
            'admin_comments' => 'nullable|string|max:1000',
        ]);

        $contentRequest->update([
            'processed_at' => now(),
            'processed_by' => $user->id,
            'content_links' => $request->content_links,
            'admin_comments' => $request->admin_comments,
        ]);

        $contentRequest->refresh();

        if ($contentRequest->email) {
            SendMailJob::dispatch([
                'to' => $contentRequest->email,
                'subject' => 'Your Content Request Has Been Processed - '.$contentRequest->subject,
                'title' => 'Content Request Processed',
                'body' => view('emails.content_request_processed', [
                    'contentRequest' => $contentRequest,
                    'contentLinks' => $request->content_links,
                    'adminComments' => $request->admin_comments,
                ])->render(),
            ])->onQueue('default');
        }

        $redirect = $contentRequest->referral_forum_id
            ? redirect()->to(ContentRequestReferralForumService::forumThreadUrl((int) $contentRequest->referral_forum_id))
            : redirect()->route('content-request.referral.discuss', $contentRequest);

        return $redirect->with('success', 'Request marked as processed. The requester has been emailed if an address is on file.');
    }
}
