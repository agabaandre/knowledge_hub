<div class="card border rounded mb-3 federation-forum-card">
    <div class="card-body py-3">
        <div class="mb-2">
            @include('partials.federation.source_badge', ['item' => $forum, 'hubName' => $forum->federation_hub_name ?? null])
        </div>
        <a href="{{ $forum->federation_source_url }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
            <h6 class="mb-1" style="color:#0f172a;font-size:1rem;">{!! Str::limit(strip_tags($forum->forum_title ?? ''), 120) !!}</h6>
        </a>
        @if(!empty($forum->forum_description))
            <p class="mb-2 text-muted" style="font-size:0.875rem;">{{ Str::limit(strip_tags($forum->forum_description), 140) }}</p>
        @endif
        <div class="d-flex align-items-center flex-wrap" style="font-size:0.8rem;color:#64748b;gap:0.75rem;">
            @if(!empty($forum->federation_country))
                <span><i class="fa fa-map-marker-alt me-1"></i>{{ $forum->federation_country }}</span>
            @endif
            @if(!empty($forum->created_at))
                <span><i class="fa fa-clock me-1"></i>{{ time_ago($forum->created_at) }}</span>
            @endif
            <span><i class="fa fa-comments me-1"></i>{{ $forum->total_comments ?? 0 }} Comments</span>
        </div>
        <a href="{{ $forum->federation_source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm fed-btn-primary mt-2">
            <i class="fa-regular fa-arrow-up-right-from-square me-1"></i>View on {{ $forum->federation_hub_name }}
        </a>
    </div>
</div>
