{{-- Linked hub content request: context + mark-as-processed for eligible users (no requester PII on this page) --}}
@if(!empty($linkedContentRequest))
<div class="alert alert-info border-info mb-4" id="content-request-referral-banner" style="border-width: 2px;">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="alert-heading mb-2"><i class="fa fa-link mr-2"></i>Community-linked content request</h5>
            @php
                $forumCommunityName = optional($linkedForumCommunity ?? null)->community_name
                    ?? optional($linkedContentRequest->referredToCommunity)->community_name
                    ?? 'this community';
            @endphp
            <p class="mb-1 small">This forum thread was opened so <strong>{{ $forumCommunityName }}</strong> can discuss a Knowledge Hub <strong>content request</strong>. The requester’s identity and contact details are <strong>not</strong> shown here for privacy—the full topic (with contact details removed where possible) is in the first post below.</p>
            <p class="mb-0 small text-muted">Please keep comments focused on the subject matter and useful resources; do not ask for or share personal information about the requester.</p>
        </div>
    </div>

    @if(!$linkedContentRequest->isProcessed() && auth()->check() && $linkedContentRequest->userMayMarkReferralAsProcessed(auth()->user()))
    <div class="mt-3 pt-3 border-top border-info" id="mark-processed">
        <h6 class="font-weight-bold"><i class="fa fa-check-circle mr-1"></i>Mark request as processed</h6>
        <p class="small text-muted">When you are ready, record resource links for the requester (they receive the same email as in the admin workflow).</p>
        <form method="post" action="{{ route('content-request.referral.mark-processed', $linkedContentRequest) }}">
            @csrf
            <div class="form-group">
                <label for="cr_content_links" class="small font-weight-bold">Content links <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-sm" id="cr_content_links" name="content_links" rows="4" required placeholder="Publications, tools, or other URLs…">{{ old('content_links') }}</textarea>
            </div>
            <div class="form-group mb-2">
                <label for="cr_admin_comments" class="small font-weight-bold">Comments (optional)</label>
                <textarea class="form-control form-control-sm" id="cr_admin_comments" name="admin_comments" rows="2" maxlength="1000">{{ old('admin_comments') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fa fa-check mr-1"></i>Mark processed &amp; email requester
            </button>
        </form>
    </div>
    @endif
</div>
@endif
