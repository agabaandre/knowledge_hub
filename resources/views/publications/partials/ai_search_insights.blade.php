@if(!empty($aiSearchInsights) && !empty($aiSearchInsights['overview']))
<div class="ai-search-insights mb-4" style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid var(--theme-color-primary,#119A48);border-radius:0.25rem;padding:1rem 1.15rem;box-shadow:0 2px 10px rgba(15,23,42,.04);">
    <div class="d-flex align-items-start gap-2 mb-2">
        <span style="width:2rem;height:2rem;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--theme-color-primary,#119A48),color-mix(in srgb,var(--theme-color-primary,#119A48) 70%,#0ea5e9));color:#fff;flex-shrink:0;">
            <i class="fa-solid fa-microchip" aria-hidden="true"></i>
        </span>
        <div>
            <h2 class="h6 mb-1 fw-bold" style="color:#0f172a;">AI overview</h2>
            <p class="mb-0" style="color:#334155;font-size:0.92rem;line-height:1.55;">{{ $aiSearchInsights['overview'] }}</p>
        </div>
    </div>

    @if(!empty($aiSearchInsights['key_points']))
        <ul class="mb-3 ps-3" style="color:#475569;font-size:0.86rem;line-height:1.5;">
            @foreach($aiSearchInsights['key_points'] as $point)
                <li>{{ $point }}</li>
            @endforeach
        </ul>
    @endif

    @php
        $hasHubPicks = !empty($aiSearchInsights['publications']) || !empty($aiSearchInsights['forums']) || !empty($aiSearchInsights['communities']);
    @endphp
    @if($hasHubPicks)
        <div class="ai-insights-picks" style="border-top:1px solid #eef2f6;padding-top:0.75rem;">
            <p class="small fw-semibold mb-2" style="color:var(--theme-color-primary,#119A48);">Highlighted on Khub</p>
            <div class="d-flex flex-column gap-2">
                @foreach($aiSearchInsights['publications'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <i class="fa fa-file-lines mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['title'] ?? 'Publication' }}</span>
                                <span class="d-block small text-muted">Publication</span>
                            </span>
                        </a>
                    @endif
                @endforeach
                @foreach($aiSearchInsights['forums'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <i class="fa fa-comments mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['title'] ?? 'Discussion' }}</span>
                                <span class="d-block small text-muted">Forum discussion</span>
                            </span>
                        </a>
                    @endif
                @endforeach
                @foreach($aiSearchInsights['communities'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                            <i class="fa fa-users mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['name'] ?? 'Community' }}</span>
                                <span class="d-block small text-muted">Community</span>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($aiSearchInsights['external_resources']))
        <div class="mt-3 pt-2" style="border-top:1px solid #eef2f6;">
            <p class="small fw-semibold mb-2 text-muted">Suggested external resources</p>
            <ul class="list-unstyled mb-0 small">
                @foreach($aiSearchInsights['external_resources'] as $ext)
                    <li class="mb-1">
                        <a href="{{ $ext['url'] }}" target="_blank" rel="noopener noreferrer" style="color:var(--theme-color-primary,#119A48);">
                            {{ $ext['title'] }}
                        </a>
                        @if(!empty($ext['note']))
                            <span class="text-muted"> — {{ $ext['note'] }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <p class="mb-0 mt-2" style="font-size:0.72rem;color:#94a3b8;">AI-generated summary. Verify important details against the resources below.</p>
</div>
@endif
