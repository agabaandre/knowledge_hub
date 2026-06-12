@php
    $primary = settings()->primary_color ?? '#119A48';
@endphp
<section class="py-5" style="background-color: #f8fafc;">
    <style>
        .theme1-resource-card.forum-post-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: box-shadow 0.2s ease;
            position: relative;
        }
        .theme1-resource-card.forum-post-card:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .theme1-resource-card .forum-header {
            margin-bottom: 0.75rem;
        }
        .theme1-resource-card .forum-author-name-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .theme1-resource-card .forum-author-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9375rem;
        }
        .theme1-resource-card .forum-post-time {
            color: #64748b;
            font-size: 0.875rem;
        }
        .theme1-resource-card .forum-content {
            color: #4a5568;
            line-height: 1.6;
            overflow: hidden;
        }
        .theme1-resource-card .forum-thread-image {
            float: left;
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .theme1-resource-card .forum-thread-image:hover {
            transform: scale(1.03);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .theme1-resource-card .forum-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .theme1-resource-card .forum-title a {
            color: inherit;
            text-decoration: none;
        }
        .theme1-resource-card .forum-title a:hover {
            color: {{ $primary }};
        }
        .theme1-resource-card .forum-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .theme1-resource-card .forum-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: {{ $primary }};
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .theme1-resource-card .forum-action-btn:hover {
            color: #0d5034;
            text-decoration: underline;
        }
        .records-search-infinite-sentinel { height: 1px; width: 100%; }
        .records-search-infinite-loader { color: #64748b; font-size: 0.875rem; padding: 0.5rem 0; }
        @media (max-width: 575.98px) {
            .theme1-resource-card .forum-thread-image {
                width: 100px;
                height: 100px;
                margin-right: 0.75rem;
            }
        }
    </style>
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="sec_title position-relative text-center">
                    <h2 class="ft-bold mb-0 notranslate" style="color: #1e293b;" data-khub-i18n="home_sections.top_searches">{{ \App\Support\UiLocaleLabels::homeSection('top_searches') }}</h2>
                </div>
            </div>
        </div>
        @php
            $topSearchesTotal = (int) ($topSearchesTotal ?? count($recent));
            $topSearchesLoaded = count($recent);
        @endphp
        <div id="home-top-searches"
             data-initial="{{ (int) ($topSearchesInitial ?? 6) }}"
             data-page-size="{{ (int) ($topSearchesPageSize ?? 10) }}"
             data-loaded="{{ $topSearchesLoaded }}"
             data-total="{{ $topSearchesTotal }}">
            <div class="row g-0" id="home-top-searches-list">
                @include('home.partials.theme1.top_searches_list_items', ['listOffset' => 0])
            </div>
            @if($topSearchesLoaded < $topSearchesTotal)
                <div class="records-search-infinite-footer py-3 text-center" id="home-top-searches-footer">
                    <p class="text-muted small mb-2" id="home-top-searches-status">
                        Showing {{ number_format($topSearchesLoaded) }} of {{ number_format($topSearchesTotal) }} resources
                    </p>
                    <div id="home-top-searches-sentinel" class="records-search-infinite-sentinel" aria-hidden="true"></div>
                    <div id="home-top-searches-loader" class="records-search-infinite-loader d-none" aria-live="polite">
                        <i class="fa fa-spinner fa-spin me-1"></i>{{ __('publications.search.loading_more') }}
                    </div>
                </div>
            @else
                <p class="text-muted small text-center py-2 mb-0" id="home-top-searches-complete">{{ __('publications.search.all_results_loaded') }}</p>
            @endif
        </div>
    </div>
</section>
