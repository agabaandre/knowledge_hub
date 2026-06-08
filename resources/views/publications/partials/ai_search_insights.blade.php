@if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null))
@php
    $insights = $aiSearchInsights;
    $searchTerm = trim((string) ($insights['query'] ?? request('term', '')));
    $hubMatches = (int) ($insights['hub_matches'] ?? 0);
    $extraScholarly = $insights['scholarly_sources'] ?? $insights['internet_results'] ?? [];
    $hasHubPicks = !empty($insights['publications']) || !empty($insights['forums']) || !empty($insights['communities']) || !empty($insights['health_topics']);
    $hasTaxonomy = !empty($insights['thematic_areas']) || !empty($insights['sub_thematic_areas']) || !empty($insights['contributors']);
@endphp
<style>
    .ai-search-insights {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: hidden;
        margin-bottom: 0.75rem !important;
    }
    .ai-insights-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.6rem 0.85rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .ai-insights-header__brand {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: 0;
    }
    .ai-insights-header__icon {
        width: 1.85rem;
        height: 1.85rem;
        border-radius: 0.4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--theme-color-primary, #119A48);
        color: #fff;
        flex-shrink: 0;
        font-size: 0.8rem;
    }
    .ai-insights-header__title {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .ai-insights-header__subtitle {
        margin: 0;
        font-size: 0.7rem;
        color: #64748b;
    }
    .ai-insights-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        justify-content: flex-end;
    }
    .ai-insights-meta__chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.18rem 0.45rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        white-space: nowrap;
    }
    .ai-insights-meta__chip--primary {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 25%, #e2e8f0);
        color: var(--theme-color-primary, #119A48);
    }
    .ai-insights-body { padding: 0.65rem 0.85rem 0.35rem; }
    .ai-insights-summary {
        margin: 0 0 0.55rem;
        padding: 0.55rem 0.65rem;
        border-left: 3px solid var(--theme-color-primary, #119A48);
        background: #f8fafc;
        color: #334155;
        font-size: 0.86rem;
        line-height: 1.55;
    }
    .ai-insights-section { margin-bottom: 0.55rem; }
    .ai-insights-section__title {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0 0 0.35rem;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
    }
    .ai-insights-section__title i { color: var(--theme-color-primary, #119A48); }
    .ai-insights-evidence { list-style: none; margin: 0; padding: 0; display: grid; gap: 0.3rem; }
    .ai-insights-evidence__item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.45rem;
        align-items: start;
        padding: 0.45rem 0.55rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.2rem;
        background: #fff;
    }
    .ai-insights-evidence__text {
        display: flex;
        align-items: flex-start;
        gap: 0.4rem;
        color: #334155;
        font-size: 0.82rem;
        line-height: 1.45;
        min-width: 0;
    }
    .ai-insights-evidence__text i {
        color: var(--theme-color-primary, #119A48);
        margin-top: 0.12rem;
        flex-shrink: 0;
        font-size: 0.72rem;
    }
    .ai-insights-evidence__link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.45rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--theme-color-primary, #119A48) 20%, #e2e8f0);
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 5%, #fff);
        color: var(--theme-color-primary, #119A48);
        font-size: 0.66rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        max-width: 9rem;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ai-insights-evidence__link:hover {
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 12%, #fff);
        color: var(--theme-color-primary, #119A48);
    }
    .ai-insights-hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.35rem;
    }
    .ai-insights-card {
        display: flex;
        align-items: flex-start;
        gap: 0.45rem;
        padding: 0.45rem 0.55rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.2rem;
        background: #fff;
        text-decoration: none;
        min-height: 0;
    }
    .ai-insights-card:hover {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 30%, #e2e8f0);
    }
    .ai-insights-card__icon {
        width: 1.4rem;
        height: 1.4rem;
        border-radius: 0.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 10%, #fff);
        color: var(--theme-color-primary, #119A48);
        flex-shrink: 0;
        font-size: 0.72rem;
    }
    .ai-insights-card__title {
        display: block;
        font-weight: 600;
        color: #0f172a;
        font-size: 0.78rem;
        line-height: 1.35;
    }
    .ai-insights-card__label {
        display: block;
        font-size: 0.62rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #94a3b8;
        margin-top: 0.05rem;
    }
    .ai-insights-card__excerpt {
        display: block;
        font-size: 0.72rem;
        color: #64748b;
        line-height: 1.35;
        margin-top: 0.1rem;
    }
    .ai-insights-pill-row { display: flex; flex-wrap: wrap; gap: 0.25rem; }
    .ai-insights-pill {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.7rem;
    }
    .ai-insights-pill a {
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
        font-weight: 600;
    }
    .ai-insights-footer {
        padding: 0.45rem 0.85rem 0.55rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.68rem;
        color: #94a3b8;
        line-height: 1.35;
    }
    @media (max-width: 575.98px) {
        .ai-insights-evidence__item { grid-template-columns: 1fr; }
        .ai-insights-evidence__link { justify-self: start; }
        .ai-insights-header { flex-direction: column; align-items: flex-start; }
    }
</style>

<div class="ai-search-insights">
    <header class="ai-insights-header">
        <div class="ai-insights-header__brand">
            <span class="ai-insights-header__icon" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span>
            <div>
                <h2 class="ai-insights-header__title">{{ __('publications.search.ai_overview') }}</h2>
                <p class="ai-insights-header__subtitle">{{ __('publications.search.ai_overview_subtitle') }}</p>
            </div>
        </div>
        <div class="ai-insights-meta">
            @if($searchTerm !== '')
                <span class="ai-insights-meta__chip"><i class="fa fa-magnifying-glass" aria-hidden="true"></i> {{ $searchTerm }}</span>
            @endif
            @if($hubMatches > 0)
                <span class="ai-insights-meta__chip ai-insights-meta__chip--primary">
                    <i class="fa fa-file-lines" aria-hidden="true"></i>
                    {{ $hubMatches === 1 ? __('publications.search.ai_hub_matches_one') : __('publications.search.ai_hub_matches', ['count' => number_format($hubMatches)]) }}
                </span>
            @endif
        </div>
    </header>

    <div class="ai-insights-body">
        @if(!empty($insights['overview']))
            <p class="ai-insights-summary">{{ $insights['overview'] }}</p>
        @endif

        @if(!empty($insights['key_points']))
            <section class="ai-insights-section" aria-label="{{ __('publications.search.key_takeaways') }}">
                <h3 class="ai-insights-section__title">
                    <i class="fa fa-list-check" aria-hidden="true"></i>
                    {{ __('publications.search.key_takeaways') }}
                </h3>
                <ul class="ai-insights-evidence">
                    @foreach($insights['key_points'] as $point)
                        @php
                            $pointText = is_array($point) ? ($point['text'] ?? '') : (string) $point;
                            $pointUrl = is_array($point) ? ($point['url'] ?? null) : null;
                            $pointLabel = is_array($point) ? ($point['label'] ?? __('publications.search.scholarly_sources')) : __('publications.search.scholarly_sources');
                            $pointIcon = is_array($point) ? ($point['icon'] ?? 'fa-graduation-cap') : 'fa-graduation-cap';
                        @endphp
                        @if($pointText !== '')
                            <li class="ai-insights-evidence__item">
                                <div class="ai-insights-evidence__text">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span>{{ $pointText }}</span>
                                </div>
                                @if(!empty($pointUrl))
                                    <a href="{{ $pointUrl }}" target="_blank" rel="noopener noreferrer" class="ai-insights-evidence__link" title="{{ $pointLabel }}">
                                        <i class="fa {{ $pointIcon }}" aria-hidden="true"></i>
                                        {{ $pointLabel }}
                                    </a>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </section>
        @endif

        @if($hasHubPicks)
            <section class="ai-insights-section" aria-label="{{ __('publications.search.on_this_hub') }}">
                <h3 class="ai-insights-section__title">
                    <i class="fa fa-database" aria-hidden="true"></i>
                    {{ __('publications.search.on_this_hub') }}
                </h3>
                <div class="ai-insights-hub-grid">
                    @foreach($insights['health_topics'] ?? [] as $topic)
                        @if(!empty($topic['url']))
                            <a href="{{ $topic['url'] }}" class="ai-insights-card">
                                <span class="ai-insights-card__icon"><i class="fa fa-heart-pulse" aria-hidden="true"></i></span>
                                <span>
                                    <span class="ai-insights-card__title">{{ $topic['name'] ?? __('publications.search.health_topics') }}</span>
                                    <span class="ai-insights-card__label">{{ __('publications.search.health_topics') }}</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                    @foreach($insights['publications'] ?? [] as $pick)
                        @if(!empty($pick['url']))
                            <a href="{{ $pick['url'] }}" class="ai-insights-card">
                                <span class="ai-insights-card__icon"><i class="fa fa-file-lines" aria-hidden="true"></i></span>
                                <span>
                                    <span class="ai-insights-card__title">{{ $pick['title'] ?? __('publications.publication') }}</span>
                                    <span class="ai-insights-card__label">{{ __('publications.publication') }}</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                    @foreach($insights['forums'] ?? [] as $pick)
                        @if(!empty($pick['url']))
                            <a href="{{ $pick['url'] }}" class="ai-insights-card">
                                <span class="ai-insights-card__icon"><i class="fa fa-comments" aria-hidden="true"></i></span>
                                <span>
                                    <span class="ai-insights-card__title">{{ $pick['title'] ?? __('publications.search.forum_discussion') }}</span>
                                    <span class="ai-insights-card__label">{{ __('publications.search.forum_discussion') }}</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                    @foreach($insights['communities'] ?? [] as $pick)
                        @if(!empty($pick['url']))
                            <a href="{{ $pick['url'] }}" class="ai-insights-card">
                                <span class="ai-insights-card__icon"><i class="fa fa-users" aria-hidden="true"></i></span>
                                <span>
                                    <span class="ai-insights-card__title">{{ $pick['name'] ?? __('publications.search.community') }}</span>
                                    <span class="ai-insights-card__label">{{ __('publications.search.community') }}</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($extraScholarly))
            <section class="ai-insights-section" aria-label="{{ __('publications.search.more_scholarly_sources') }}">
                <h3 class="ai-insights-section__title">
                    <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                    {{ __('publications.search.more_scholarly_sources') }}
                </h3>
                <div class="ai-insights-hub-grid">
                    @foreach($extraScholarly as $web)
                        @if(!empty($web['url']))
                            <a href="{{ $web['url'] }}" target="_blank" rel="noopener noreferrer" class="ai-insights-card">
                                <span class="ai-insights-card__icon"><i class="fa {{ $web['icon'] ?? 'fa-graduation-cap' }}" aria-hidden="true"></i></span>
                                <span>
                                    <span class="ai-insights-card__title">{{ $web['title'] ?? ($web['label'] ?? __('publications.search.scholarly_sources')) }}</span>
                                    <span class="ai-insights-card__label">{{ $web['label'] ?? __('publications.search.scholarly_sources') }}</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @if($hasTaxonomy)
            <section class="ai-insights-section" aria-label="{{ __('publications.search.thematic_areas') }}">
                <div class="ai-insights-pill-row">
                    @foreach($insights['thematic_areas'] ?? [] as $theme)
                        @if(!empty($theme['name']))
                            <span class="ai-insights-pill">{{ $theme['name'] }}</span>
                        @endif
                    @endforeach
                    @foreach(collect($insights['sub_thematic_areas'] ?? [])->take(4) as $subTheme)
                        @if(!empty($subTheme['name']))
                            <span class="ai-insights-pill">{{ $subTheme['name'] }}</span>
                        @endif
                    @endforeach
                    @foreach($insights['contributors'] ?? [] as $contributor)
                        @if(!empty($contributor['url']))
                            <span class="ai-insights-pill"><a href="{{ $contributor['url'] }}">{{ $contributor['name'] }}</a></span>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <footer class="ai-insights-footer">{{ __('publications.search.ai_disclaimer') }}</footer>
</div>
@endif
