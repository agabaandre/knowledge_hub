@if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null) && !empty($aiSearchInsights['overview']))
<div class="ai-search-insights mb-4" style="background:#fff;border-radius:0.5rem;padding:1rem 1.15rem;box-shadow:0 2px 10px rgba(15,23,42,.04);">
    <div class="d-flex align-items-start gap-2 mb-2">
        <span style="width:2rem;height:2rem;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--theme-color-primary,#119A48),color-mix(in srgb,var(--theme-color-primary,#119A48) 70%,#0ea5e9));color:#fff;flex-shrink:0;">
            <i class="fa-solid fa-microchip" aria-hidden="true"></i>
        </span>
        <div>
            <h2 class="h6 mb-1 fw-bold" style="color:#0f172a;">{{ __('publications.search.ai_overview') }}</h2>
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

    @if(!empty($aiSearchInsights['internet_results']))
        <div class="ai-insights-web mb-3">
            <p class="small fw-semibold mb-2" style="color:#475569;">{{ __('publications.search.internet_results') }}</p>
            <div class="d-flex flex-column gap-2">
                @foreach($aiSearchInsights['internet_results'] as $web)
                    @if(!empty($web['url']))
                        <a href="{{ $web['url'] }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;">
                            <i class="fa fa-globe mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $web['title'] ?? 'Web result' }}</span>
                                @if(!empty($web['snippet']))
                                    <span class="d-block small text-muted" style="line-height:1.45;">{{ $web['snippet'] }}</span>
                                @endif
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($aiSearchInsights['health_topics']))
        <div class="ai-insights-health-topics mb-3">
            <p class="small fw-semibold mb-2" style="color:var(--theme-color-primary,#119A48);">{{ __('publications.search.health_topics') }}</p>
            <div class="d-flex flex-column gap-2">
                @foreach($aiSearchInsights['health_topics'] as $topic)
                    @if(!empty($topic['url']))
                        <a href="{{ $topic['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;">
                            <i class="fa fa-heart-pulse mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $topic['name'] ?? __('publications.search.health_topics') }}</span>
                                @if(!empty($topic['overview']))
                                    <span class="d-block small text-muted" style="line-height:1.45;">{{ $topic['overview'] }}</span>
                                @endif
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @php
        $hasHubPicks = !empty($aiSearchInsights['publications']) || !empty($aiSearchInsights['forums']) || !empty($aiSearchInsights['communities']);
    @endphp
    @if($hasHubPicks)
        <div class="ai-insights-picks" style="padding-top:0.75rem;">
            <p class="small fw-semibold mb-2" style="color:var(--theme-color-primary,#119A48);">{{ __('publications.search.highlighted_on_khub') }}</p>
            <div class="d-flex flex-column gap-2">
                @foreach($aiSearchInsights['publications'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;">
                            <i class="fa fa-file-lines mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['title'] ?? __('publications.publication') }}</span>
                                <span class="d-block small text-muted">{{ __('publications.publication') }}</span>
                            </span>
                        </a>
                    @endif
                @endforeach
                @foreach($aiSearchInsights['forums'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;">
                            <i class="fa fa-comments mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['title'] ?? __('publications.search.forum_discussion') }}</span>
                                <span class="d-block small text-muted">{{ __('publications.search.forum_discussion') }}</span>
                            </span>
                        </a>
                    @endif
                @endforeach
                @foreach($aiSearchInsights['communities'] ?? [] as $pick)
                    @if(!empty($pick['url']))
                        <a href="{{ $pick['url'] }}" class="text-decoration-none d-flex align-items-start gap-2 p-2 rounded" style="background:#f8fafc;">
                            <i class="fa fa-users mt-1" style="color:var(--theme-color-primary,#119A48);"></i>
                            <span>
                                <span class="d-block fw-semibold" style="color:#0f172a;font-size:0.88rem;">{{ $pick['name'] ?? __('publications.search.community') }}</span>
                                <span class="d-block small text-muted">{{ __('publications.search.community') }}</span>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($aiSearchInsights['thematic_areas']) || !empty($aiSearchInsights['sub_thematic_areas']) || !empty($aiSearchInsights['contributors']))
        <div class="ai-insights-taxonomy mt-3 pt-2 small" style="color:#64748b;">
            @if(!empty($aiSearchInsights['thematic_areas']))
                <p class="mb-1 fw-semibold" style="color:#475569;">{{ __('publications.search.thematic_areas') }}</p>
                <p class="mb-2">{{ collect($aiSearchInsights['thematic_areas'])->pluck('name')->filter()->implode(' · ') }}</p>
            @endif
            @if(!empty($aiSearchInsights['sub_thematic_areas']))
                <p class="mb-1 fw-semibold" style="color:#475569;">{{ __('publications.search.sub_thematic_areas') }}</p>
                <p class="mb-2">{{ collect($aiSearchInsights['sub_thematic_areas'])->pluck('name')->filter()->take(6)->implode(' · ') }}</p>
            @endif
            @if(!empty($aiSearchInsights['contributors']))
                <p class="mb-1 fw-semibold" style="color:#475569;">{{ __('publications.search.contributors') }}</p>
                <p class="mb-0">
                    @foreach($aiSearchInsights['contributors'] as $contributor)
                        @if(!empty($contributor['url']))
                            <a href="{{ $contributor['url'] }}" class="text-decoration-none me-2" style="color:var(--theme-color-primary,#119A48);">{{ $contributor['name'] }}</a>
                        @endif
                    @endforeach
                </p>
            @endif
        </div>
    @endif

    @if(!empty($aiSearchInsights['external_resources']))
        <div class="mt-3 pt-2">
            <p class="small fw-semibold mb-2 text-muted">{{ __('publications.search.suggested_external_resources') }}</p>
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

    <p class="mb-0 mt-2" style="font-size:0.72rem;color:#94a3b8;">{{ __('publications.search.ai_disclaimer') }}</p>
</div>
@endif
