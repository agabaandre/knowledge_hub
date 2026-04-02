<div class="list-group list-group-flush border rounded mb-4">
    @forelse($contentRequest->referralMessages as $msg)
        <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <strong>{{ $msg->authorLabel() }}</strong>
                <small class="text-muted">{{ $msg->created_at->format('M j, Y g:i a') }}</small>
            </div>
            <div class="mt-2" style="white-space: pre-wrap;">{{ $msg->body }}</div>
        </div>
    @empty
        <div class="list-group-item text-muted">No messages yet.</div>
    @endforelse
</div>
