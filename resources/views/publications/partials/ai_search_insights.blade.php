@if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null))
@php
    $insights = $aiSearchInsights;
    $searchTerm = trim((string) ($insights['query'] ?? request('term', '')));
    $hubMatches = (int) ($insights['hub_matches'] ?? 0);
    $hasHubPicks = !empty($insights['publications']) || !empty($insights['forums']) || !empty($insights['communities']) || !empty($insights['health_topics']);
    $hasScholarly = !empty($insights['internet_results']) || !empty($insights['external_resources']);
    $hasTaxonomy = !empty($insights['thematic_areas']) || !empty($insights['sub_thematic_areas']) || !empty($insights['contributors']);
@endphp
<style>
    .ai-search-insights {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .ai-insights-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .ai-insights-header__brand {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        min-width: 0;
    }
    .ai-insights-header__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48), color-mix(in srgb, var(--theme-color-primary, #119A48) 70%, #0ea5e9));
        color: #fff;
        flex-shrink: 0;
    }
    .ai-insights-header__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }
    .ai-insights-header__subtitle {
        margin: 0.15rem 0 0;
        font-size: 0.78rem;
        color: #64748b;
    }
    .ai-insights-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        justify-content: flex-end;
        flex-shrink: 0;
    }
    .ai-insights-meta__chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.55rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        white-space: nowrap;
    }
    .ai-insights-meta__chip--primary {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 25%, #e2e8f0);
        color: var(--theme-color-primary, #119A48);
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 6%, #fff);
    }
    .ai-insights-body {
        padding: 1rem 1.15rem 0.25rem;
    }
    .ai-insights-summary {
        margin: 0 0 1rem;
        padding: 0.85rem 0.95rem;
        border-left: 3px solid var(--theme-color-primary, #119A48);
        background: #f8fafc;
        color: #334155;
        font-size: 0.92rem;
        line-height: 1.65;
    }
    .ai-insights-section {
        margin-bottom: 1rem;
    }
    .ai-insights-section__title {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin: 0 0 0.65rem;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }
    .ai-insights-section__title i {
        color: var(--theme-color-primary, #119A48);
        font-size: 0.82rem;
    }
    .ai-insights-takeaways {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.45rem;
    }
    .ai-insights-takeaways li {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        padding: 0.55rem 0.7rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        background: #fff;
        color: #334155;
        font-size: 0.84rem;
        line-height: 1.5;
    }
    .ai-insights-takeaways li i {
        color: var(--theme-color-primary, #119A48);
        margin-top: 0.15rem;
        flex-shrink: 0;
    }
    .ai-insights-card {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        padding: 0.7rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        background: #fff;
        text-decoration: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .ai-insights-card:hover {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 35%, #e2e8f0);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
    }
    .ai-insights-card__icon {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 10%, #fff);
        color: var(--theme-color-primary, #119A48);
        flex-shrink: 0;
        font-size: 0.82rem;
    }
    .ai-insights-card__title {
        display: block;
        font-weight: 600;
        color: #0f172a;
        font-size: 0.86rem;
        line-height: 1.4;
    }
    .ai-insights-card__label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--theme-color-primary, #119A48);
        margin-top: 0.1rem;
    }
    .ai-insights-card__excerpt {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.45;
        margin-top: 0.2rem;
    }
    .ai-insights-pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .ai-insights-pill {
        display: inline-block;
        padding: 0.22rem 0.55rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        line-height: 1.4;
    }
    .ai-insights-pill a {
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
        font-weight: 600;
    }
    .ai-insights-pill a:hover {
        text-decoration: underline;
    }
    .ai-insights-footer {
        padding: 0.65rem 1.15rem 0.85rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.72rem;
        color: #94a3b8;
        line-height: 1.45;
    }
    @media (max-width: 767.98px) {
        .ai-insights-header {
            flex-direction: column;
        }
        .ai-insights-meta {
            justify-content: flex-start;
        }
    }
</style>

<div class="ai-search-insights mb-4">
    <header class="ai-insights-header">
        <div class="ai-insights-header__brand">
            <span class="ai-insights-header__icon" aria-hidden="true">
                <i class="fa-solid fa-microchip"></i>
            </span>
            <div>
                <h2 class="ai-insights-header__title">{{ __('publications.search.ai_overview') }}</h2>
                <p class="ai-insights-header__subtitle">{{ __('publications.search.ai_overview_subtitle') }}</p>
            </div>
        </div>
        <div class="ai-insights-meta">
            @if($searchTerm !== '')
                <span class="ai-insights-meta__chip">
                    <i class="fa fa-magnifying-glass" aria-hidden="true"></i>
                    {{ __('publications.search.ai_search_query') }}: “{{ $searchTerm }}”
                </span>
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
                <ul class="ai-insights-takeaways">
                    @foreach($insights['key_points'] as $point)
                        <li>
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <div class="row g-3">
            @if($hasHubPicks)
                <div class="{{ $hasScholarly ? 'col-lg-7' : 'col-12' }}">
                    <section class="ai-insights-section" aria-label="{{ __('publications.search.on_this_hub') }}">
                        <h3 class="ai-insights-section__title">
                            <i class="fa fa-database" aria-hidden="true"></i>
                            {{ __('publications.search.on_this_hub') }}
                        </h3>
                        <div class="d-flex flex-column gap-2">
                            @foreach($insights['health_topics'] ?? [] as $topic)
                                @if(!empty($topic['url']))
                                    <a href="{{ $topic['url'] }}" class="ai-insights-card">
                                        <span class="ai-insights-card__icon"><i class="fa fa-heart-pulse" aria-hidden="true"></i></span>
                                        <span>
                                            <span class="ai-insights-card__title">{{ $topic['name'] ?? __('publications.search.health_topics') }}</span>
                                            <span class="ai-insights-card__label">{{ __('publications.search.health_topics') }}</span>
                                            @if(!empty($topic['overview']))
                                                <span class="ai-insights-card__excerpt">{{ plain_text_excerpt_from_html($topic['overview'], 140) }}</span>
                                            @endif
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
                                            @if(!empty($pick['excerpt']))
                                                <span class="ai-insights-card__excerpt">{{ $pick['excerpt'] }}</span>
                                            @elseif(!empty($pick['category']))
                                                <span class="ai-insights-card__excerpt">{{ $pick['category'] }}</span>
                                            @endif
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
                                            @if(!empty($pick['excerpt']))
                                                <span class="ai-insights-card__excerpt">{{ $pick['excerpt'] }}</span>
                                            @endif
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
                                            @if(!empty($pick['excerpt']))
                                                <span class="ai-insights-card__excerpt">{{ $pick['excerpt'] }}</span>
                                            @endif
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </section>
                </div>
            @endif

            @if($hasScholarly)
                <div class="{{ $hasHubPicks ? 'col-lg-5' : 'col-12' }}">
                    <section class="ai-insights-section" aria-label="{{ __('publications.search.scholarly_sources') }}">
                        <h3 class="ai-insights-section__title">
                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                            {{ __('publications.search.scholarly_sources') }}
                        </h3>
                        <div class="d-flex flex-column gap-2">
                            @foreach($insights['internet_results'] ?? [] as $web)
                                @if(!empty($web['url']))
                                    @php
                                        $webLabel = $web['label'] ?? (($web['source'] ?? '') === 'pubmed' ? __('publications.search.pubmed') : (($web['source'] ?? '') === 'google_scholar' ? __('publications.search.google_scholar') : (($web['source'] ?? '') === 'jphia' ? __('publications.search.jphia') : __('publications.search.suggested_external_resources'))));
                                        $webIcon = $web['icon'] ?? (($web['source'] ?? '') === 'pubmed' ? 'fa-book-medical' : 'fa-graduation-cap');
                                    @endphp
                                    <a href="{{ $web['url'] }}" target="_blank" rel="noopener noreferrer" class="ai-insights-card">
                                        <span class="ai-insights-card__icon"><i class="fa {{ $webIcon }}" aria-hidden="true"></i></span>
                                        <span>
                                            <span class="ai-insights-card__title">{{ $web['title'] ?? $webLabel }}</span>
                                            <span class="ai-insights-card__label">{{ $webLabel }}</span>
                                            @if(!empty($web['snippet']))
                                                <span class="ai-insights-card__excerpt">{{ $web['snippet'] }}</span>
                                            @endif
                                        </span>
                                    </a>
                                @endif
                            @endforeach

                            @foreach($insights['external_resources'] ?? [] as $ext)
                                @if(!empty($ext['url']))
                                    <a href="{{ $ext['url'] }}" target="_blank" rel="noopener noreferrer" class="ai-insights-card">
                                        <span class="ai-insights-card__icon"><i class="fa fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                                        <span>
                                            <span class="ai-insights-card__title">{{ $ext['title'] ?? __('publications.search.suggested_external_resources') }}</span>
                                            <span class="ai-insights-card__label">{{ __('publications.search.suggested_external_resources') }}</span>
                                            @if(!empty($ext['note']))
                                                <span class="ai-insights-card__excerpt">{{ $ext['note'] }}</span>
                                            @endif
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </section>
                </div>
            @endif
        </div>

        @if($hasTaxonomy)
            <section class="ai-insights-section mt-1" aria-label="{{ __('publications.search.thematic_areas') }}">
                @if(!empty($insights['thematic_areas']))
                    <h3 class="ai-insights-section__title">
                        <i class="fa fa-layer-group" aria-hidden="true"></i>
                        {{ __('publications.search.thematic_areas') }}
                    </h3>
                    <div class="ai-insights-pill-row mb-2">
                        @foreach($insights['thematic_areas'] as $theme)
                            @if(!empty($theme['name']))
                                <span class="ai-insights-pill">{{ $theme['name'] }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(!empty($insights['sub_thematic_areas']))
                    <h3 class="ai-insights-section__title">
                        <i class="fa fa-sitemap" aria-hidden="true"></i>
                        {{ __('publications.search.sub_thematic_areas') }}
                    </h3>
                    <div class="ai-insights-pill-row mb-2">
                        @foreach(collect($insights['sub_thematic_areas'])->take(6) as $subTheme)
                            @if(!empty($subTheme['name']))
                                <span class="ai-insights-pill">{{ $subTheme['name'] }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if(!empty($insights['contributors']))
                    <h3 class="ai-insights-section__title">
                        <i class="fa fa-user-pen" aria-hidden="true"></i>
                        {{ __('publications.search.contributors') }}
                    </h3>
                    <div class="ai-insights-pill-row">
                        @foreach($insights['contributors'] as $contributor)
                            @if(!empty($contributor['url']))
                                <span class="ai-insights-pill">
                                    <a href="{{ $contributor['url'] }}">{{ $contributor['name'] }}</a>
                                </span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>

    <footer class="ai-insights-footer">
        {{ __('publications.search.ai_disclaimer') }}
    </footer>
</div>
@endif
