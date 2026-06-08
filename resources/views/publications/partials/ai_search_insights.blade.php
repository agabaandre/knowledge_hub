@if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null))
@php
    $insights = $aiSearchInsights;
    $searchTerm = trim((string) ($insights['query'] ?? request('term', '')));
    $hubMatches = (int) ($insights['hub_matches'] ?? 0);
    $scholarlySources = $insights['scholarly_sources'] ?? $insights['internet_results'] ?? [];
    $hasTaxonomy = !empty($insights['thematic_areas']) || !empty($insights['sub_thematic_areas']) || !empty($insights['contributors']);
@endphp
<style>
    .ai-brief {
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        background: #fff;
        margin-bottom: 0.5rem !important;
        font-size: 0.84rem;
        color: #334155;
    }
    .ai-brief__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #eef2f6;
    }
    .ai-brief__title-wrap {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        min-width: 0;
    }
    .ai-brief__icon {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 0.3rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--theme-color-primary, #119A48);
        color: #fff;
        font-size: 0.72rem;
        flex-shrink: 0;
    }
    .ai-brief__title {
        margin: 0;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .ai-brief__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        justify-content: flex-end;
    }
    .ai-brief__chip {
        font-size: 0.65rem;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.12rem 0.4rem;
        white-space: nowrap;
    }
    .ai-brief__chip--accent {
        color: var(--theme-color-primary, #119A48);
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 22%, #e2e8f0);
    }
    .ai-brief__body { padding: 0.55rem 0.75rem 0.35rem; }
    .ai-brief__summary {
        margin: 0 0 0.45rem;
        color: #475569;
        line-height: 1.5;
        font-size: 0.84rem;
    }
    .ai-brief__label {
        margin: 0 0 0.25rem;
        font-size: 0.64rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .ai-brief__highlights {
        list-style: none;
        margin: 0 0 0.45rem;
        padding: 0;
        border: 1px solid #eef2f6;
        border-radius: 0.2rem;
        overflow: hidden;
    }
    .ai-brief__highlight,
    .ai-brief__highlight-link {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.38rem 0.5rem;
        border-top: 1px solid #eef2f6;
        color: inherit;
        text-decoration: none;
    }
    .ai-brief__highlights > li:first-child .ai-brief__highlight,
    .ai-brief__highlights > li:first-child .ai-brief__highlight-link { border-top: 0; }
    .ai-brief__highlight-link:hover { background: #f8fafc; color: var(--theme-color-primary, #119A48); }
    .ai-brief__highlight-icon {
        width: 1.25rem;
        text-align: center;
        color: var(--theme-color-primary, #119A48);
        font-size: 0.72rem;
        flex-shrink: 0;
    }
    .ai-brief__highlight-text {
        flex: 1;
        min-width: 0;
        line-height: 1.35;
        color: #1e293b;
        font-weight: 500;
    }
    .ai-brief__highlight-type {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        white-space: nowrap;
    }
    .ai-brief__scholarly {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        margin: 0 0 0.35rem;
        padding: 0;
        list-style: none;
    }
    .ai-brief__scholarly a {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.22rem 0.45rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.2rem;
        background: #fff;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 500;
        text-decoration: none;
        max-width: 100%;
    }
    .ai-brief__scholarly a:hover {
        border-color: #cbd5e1;
        color: var(--theme-color-primary, #119A48);
    }
    .ai-brief__scholarly-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 16rem;
    }
    .ai-brief__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.2rem;
        margin-bottom: 0.15rem;
    }
    .ai-brief__tag {
        font-size: 0.68rem;
        color: #64748b;
        background: #f1f5f9;
        border-radius: 999px;
        padding: 0.1rem 0.4rem;
    }
    .ai-brief__tag a {
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
        font-weight: 600;
    }
    .ai-brief__foot {
        padding: 0.35rem 0.75rem 0.45rem;
        border-top: 1px solid #eef2f6;
        font-size: 0.64rem;
        color: #94a3b8;
        line-height: 1.3;
    }
    @media (max-width: 575.98px) {
        .ai-brief__head { flex-direction: column; align-items: flex-start; }
        .ai-brief__highlight-type { display: none; }
    }
</style>

<div class="ai-brief">
    <header class="ai-brief__head">
        <div class="ai-brief__title-wrap">
            <span class="ai-brief__icon" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span>
            <h2 class="ai-brief__title">{{ __('publications.search.ai_overview') }}</h2>
        </div>
        <div class="ai-brief__meta">
            @if($searchTerm !== '')
                <span class="ai-brief__chip">{{ $searchTerm }}</span>
            @endif
            @if($hubMatches > 0)
                <span class="ai-brief__chip ai-brief__chip--accent">
                    {{ $hubMatches === 1 ? __('publications.search.ai_hub_matches_one') : __('publications.search.ai_hub_matches', ['count' => number_format($hubMatches)]) }}
                </span>
            @endif
        </div>
    </header>

    <div class="ai-brief__body">
        @if(!empty($insights['overview']))
            <p class="ai-brief__summary">{{ $insights['overview'] }}</p>
        @endif

        @if(!empty($insights['key_points']))
            <p class="ai-brief__label">{{ __('publications.search.key_takeaways') }}</p>
            <ul class="ai-brief__highlights">
                @foreach($insights['key_points'] as $point)
                    @php
                        $pointText = is_array($point) ? ($point['text'] ?? '') : (string) $point;
                        $pointUrl = is_array($point) ? ($point['url'] ?? null) : null;
                        $pointLabel = is_array($point) ? ($point['label'] ?? __('publications.search.on_this_hub')) : __('publications.search.on_this_hub');
                        $pointIcon = is_array($point) ? ($point['icon'] ?? 'fa-arrow-right') : 'fa-arrow-right';
                    @endphp
                    @if($pointText !== '')
                        <li>
                            @if(!empty($pointUrl))
                                <a href="{{ $pointUrl }}" class="ai-brief__highlight-link">
                                    <span class="ai-brief__highlight-icon"><i class="fa {{ $pointIcon }}" aria-hidden="true"></i></span>
                                    <span class="ai-brief__highlight-text">{{ $pointText }}</span>
                                    <span class="ai-brief__highlight-type">{{ $pointLabel }}</span>
                                </a>
                            @else
                                <div class="ai-brief__highlight">
                                    <span class="ai-brief__highlight-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    <span class="ai-brief__highlight-text">{{ $pointText }}</span>
                                </div>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        @if(!empty($scholarlySources))
            <p class="ai-brief__label">{{ __('publications.search.scholarly_sources') }}</p>
            <ul class="ai-brief__scholarly">
                @foreach($scholarlySources as $web)
                    @if(!empty($web['url']))
                        <li>
                            <a href="{{ $web['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $web['label'] ?? '' }}">
                                <i class="fa {{ $web['icon'] ?? 'fa-graduation-cap' }}" aria-hidden="true"></i>
                                <span class="ai-brief__scholarly-title">{{ $web['title'] ?? ($web['label'] ?? __('publications.search.scholarly_sources')) }}</span>
                                <i class="fa fa-arrow-up-right-from-square" aria-hidden="true" style="font-size:0.6rem;opacity:0.65;"></i>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        @if($hasTaxonomy)
            <div class="ai-brief__tags">
                @foreach($insights['thematic_areas'] ?? [] as $theme)
                    @if(!empty($theme['name']))
                        <span class="ai-brief__tag">{{ $theme['name'] }}</span>
                    @endif
                @endforeach
                @foreach(collect($insights['sub_thematic_areas'] ?? [])->take(3) as $subTheme)
                    @if(!empty($subTheme['name']))
                        <span class="ai-brief__tag">{{ $subTheme['name'] }}</span>
                    @endif
                @endforeach
                @foreach($insights['contributors'] ?? [] as $contributor)
                    @if(!empty($contributor['url']))
                        <span class="ai-brief__tag"><a href="{{ $contributor['url'] }}">{{ $contributor['name'] }}</a></span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <footer class="ai-brief__foot">{{ __('publications.search.ai_disclaimer') }}</footer>
</div>
@endif
