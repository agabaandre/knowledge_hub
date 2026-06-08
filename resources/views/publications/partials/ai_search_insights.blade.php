@if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null))
@php
    $insights = $aiSearchInsights;
    $searchTerm = trim((string) ($insights['query'] ?? request('term', '')));
    $hubMatches = (int) ($insights['hub_matches'] ?? 0);
    $keyTakeaways = $insights['key_takeaways'] ?? array_map(
        fn ($point) => ['text' => $point, 'url' => '', 'label' => ''],
        (array) ($insights['key_points'] ?? [])
    );
    $scholarlySources = $insights['scholarly_sources'] ?? $insights['internet_results'] ?? [];
    $hasHubPicks = !empty($insights['publications']) || !empty($insights['forums']) || !empty($insights['communities']) || !empty($insights['health_topics']);
    $hasEvidence = !empty($keyTakeaways) || !empty($scholarlySources);
    $hasTaxonomy = !empty($insights['thematic_areas']) || !empty($insights['sub_thematic_areas']) || !empty($insights['contributors']);
    $showSplit = $hasHubPicks && $hasEvidence;
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
        gap: 0.75rem;
        padding: 0.7rem 0.95rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .ai-insights-header__brand {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        min-width: 0;
    }
    .ai-insights-header__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.45rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48), color-mix(in srgb, var(--theme-color-primary, #119A48) 70%, #0ea5e9));
        color: #fff;
        flex-shrink: 0;
    }
    .ai-insights-header__title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }
    .ai-insights-header__subtitle {
        margin: 0.1rem 0 0;
        font-size: 0.76rem;
        color: #64748b;
    }
    .ai-insights-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: flex-end;
        flex-shrink: 0;
    }
    .ai-insights-meta__chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.24rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
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
        padding: 0.7rem 0.95rem 0.25rem;
    }
    .ai-insights-summary {
        margin: 0 0 0.55rem;
        padding: 0.6rem 0.75rem;
        border-left: 3px solid var(--theme-color-primary, #119A48);
        background: #f8fafc;
        color: #334155;
        font-size: 0.88rem;
        line-height: 1.5;
    }
    .ai-insights-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.55rem;
        align-items: start;
    }
    .ai-insights-layout--split {
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    }
    .ai-insights-section {
        margin: 0;
    }
    .ai-insights-section__title {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0 0 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #475569;
    }
    .ai-insights-section__title i {
        color: var(--theme-color-primary, #119A48);
        font-size: 0.78rem;
    }
    .ai-insights-card-list {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .ai-insights-card {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.5rem 0.6rem;
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
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 0.3rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in srgb, var(--theme-color-primary, #119A48) 10%, #fff);
        color: var(--theme-color-primary, #119A48);
        flex-shrink: 0;
        font-size: 0.78rem;
    }
    .ai-insights-card__title {
        display: block;
        font-weight: 600;
        color: #0f172a;
        font-size: 0.84rem;
        line-height: 1.35;
    }
    .ai-insights-card__label {
        display: block;
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--theme-color-primary, #119A48);
        margin-top: 0.05rem;
    }
    .ai-insights-card__excerpt {
        display: block;
        font-size: 0.76rem;
        color: #64748b;
        line-height: 1.4;
        margin-top: 0.15rem;
    }
    .ai-insights-takeaways {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    .ai-insights-takeaways li {
        margin: 0;
    }
    .ai-insights-takeaway {
        display: flex;
        align-items: flex-start;
        gap: 0.45rem;
        padding: 0.45rem 0.55rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        background: #fff;
        color: #334155;
        font-size: 0.82rem;
        line-height: 1.4;
        text-decoration: none;
        transition: border-color 0.15s ease, background 0.15s ease;
    }
    .ai-insights-takeaway:hover {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 35%, #e2e8f0);
        background: #f8fafc;
        color: #0f172a;
    }
    .ai-insights-takeaway--static {
        cursor: default;
    }
    .ai-insights-takeaway--static:hover {
        background: #fff;
        border-color: #e2e8f0;
        color: #334155;
    }
    .ai-insights-takeaway i {
        color: var(--theme-color-primary, #119A48);
        margin-top: 0.12rem;
        flex-shrink: 0;
        font-size: 0.76rem;
    }
    .ai-insights-takeaway__text {
        flex: 1;
        min-width: 0;
    }
    .ai-insights-takeaway__source {
        display: block;
        margin-top: 0.12rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .ai-insights-source-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        margin-top: 0.4rem;
    }
    .ai-insights-source-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.18rem 0.45rem;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: var(--theme-color-primary, #119A48);
        font-size: 0.68rem;
        font-weight: 600;
        text-decoration: none;
    }
    .ai-insights-source-chip:hover {
        border-color: color-mix(in srgb, var(--theme-color-primary, #119A48) 30%, #e2e8f0);
        background: #fff;
    }
    .ai-insights-taxonomy {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }
    .ai-insights-pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.3rem;
        margin-bottom: 0.35rem;
    }
    .ai-insights-pill-row:last-child {
        margin-bottom: 0;
    }
    .ai-insights-pill {
        display: inline-block;
        padding: 0.18rem 0.5rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 0.72rem;
        line-height: 1.35;
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
        padding: 0.45rem 0.95rem 0.55rem;
        border-top: 1px solid #e2e8f0;
        font-size: 0.7rem;
        color: #94a3b8;
        line-height: 1.35;
    }
    @media (max-width: 991.98px) {
        .ai-insights-layout--split {
            grid-template-columns: 1fr;
        }
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

        <div class="ai-insights-layout{{ $showSplit ? ' ai-insights-layout--split' : '' }}">
            @if($hasHubPicks)
                <section class="ai-insights-section" aria-label="{{ __('publications.search.on_this_hub') }}">
                    <h3 class="ai-insights-section__title">
                        <i class="fa fa-database" aria-hidden="true"></i>
                        {{ __('publications.search.on_this_hub') }}
                    </h3>
                    <div class="ai-insights-card-list">
                        @foreach($insights['health_topics'] ?? [] as $topic)
                            @if(!empty($topic['url']))
                                <a href="{{ $topic['url'] }}" class="ai-insights-card">
                                    <span class="ai-insights-card__icon"><i class="fa fa-heart-pulse" aria-hidden="true"></i></span>
                                    <span>
                                        <span class="ai-insights-card__title">{{ $topic['name'] ?? __('publications.search.health_topics') }}</span>
                                        <span class="ai-insights-card__label">{{ __('publications.search.health_topics') }}</span>
                                        @if(!empty($topic['overview']))
                                            <span class="ai-insights-card__excerpt">{{ plain_text_excerpt_from_html($topic['overview'], 120) }}</span>
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
            @endif

            @if($hasEvidence)
                <section class="ai-insights-section" aria-label="{{ __('publications.search.key_takeaways') }}">
                    @if(!empty($keyTakeaways))
                        <h3 class="ai-insights-section__title">
                            <i class="fa fa-list-check" aria-hidden="true"></i>
                            {{ __('publications.search.key_takeaways') }}
                        </h3>
                        <ul class="ai-insights-takeaways">
                            @foreach($keyTakeaways as $takeaway)
                                @php
                                    $takeawayText = $takeaway['text'] ?? '';
                                    $takeawayUrl = trim((string) ($takeaway['url'] ?? ''));
                                    $takeawayLabel = trim((string) ($takeaway['label'] ?? ''));
                                @endphp
                                @if($takeawayText !== '')
                                    <li>
                                        @if($takeawayUrl !== '')
                                            <a href="{{ $takeawayUrl }}" target="_blank" rel="noopener noreferrer" class="ai-insights-takeaway">
                                                <i class="fa fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                                <span class="ai-insights-takeaway__text">
                                                    {{ $takeawayText }}
                                                    @if($takeawayLabel !== '')
                                                        <span class="ai-insights-takeaway__source">{{ $takeawayLabel }}</span>
                                                    @endif
                                                </span>
                                            </a>
                                        @else
                                            <div class="ai-insights-takeaway ai-insights-takeaway--static">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                <span class="ai-insights-takeaway__text">{{ $takeawayText }}</span>
                                            </div>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($scholarlySources))
                        <div class="ai-insights-source-row" aria-label="{{ __('publications.search.scholarly_sources') }}">
                            @foreach($scholarlySources as $source)
                                @if(!empty($source['url']))
                                    <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer" class="ai-insights-source-chip">
                                        <i class="fa {{ $source['icon'] ?? 'fa-graduation-cap' }}" aria-hidden="true"></i>
                                        {{ $source['label'] ?? __('publications.search.scholarly_sources') }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif
        </div>

        @if($hasTaxonomy)
            <div class="ai-insights-taxonomy">
                @if(!empty($insights['thematic_areas']))
                    <h3 class="ai-insights-section__title">
                        <i class="fa fa-layer-group" aria-hidden="true"></i>
                        {{ __('publications.search.thematic_areas') }}
                    </h3>
                    <div class="ai-insights-pill-row">
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
                    <div class="ai-insights-pill-row">
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
            </div>
        @endif
    </div>

    <footer class="ai-insights-footer">
        {{ __('publications.search.ai_disclaimer') }}
    </footer>
</div>
@endif
