<?php

namespace App\Http\Controllers;

use App\Models\ContentRequest;
use App\Models\ContentRequestReferralMessage;
use App\Services\ContentRequestReferralNotifier;
use Illuminate\Http\Request;

class ContentRequestTrackController extends Controller
{
    public function show(string $token)
    {
        $contentRequest = ContentRequest::query()
            ->where('requestor_track_token', $token)
            ->whereNotNull('referral_type')
            ->with(['referralMessages.user', 'country', 'referredToCommunity', 'referredToUser'])
            ->firstOrFail();

        return view('content_requests.track', [
            'contentRequest' => $contentRequest,
            'token' => $token,
        ]);
    }

    public function storeMessage(Request $request, string $token)
    {
        $contentRequest = ContentRequest::query()
            ->where('requestor_track_token', $token)
            ->whereNotNull('referral_type')
            ->firstOrFail();

        $request->validate([
            'body' => 'required|string|min:3|max:10000',
        ]);

        $message = ContentRequestReferralMessage::create([
            'content_request_id' => $contentRequest->id,
            'user_id' => null,
            'posted_via_track' => true,
            'body' => sanitize_rich_text_for_storage($request->input('body')),
        ]);

        ContentRequestReferralNotifier::notifyParticipantsRequestorReplied($contentRequest, $message);

        return redirect()
            ->route('content-request.track', ['token' => $token])
            ->with('success', 'Your message was posted. Participants have been notified.');
    }
}
