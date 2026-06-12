<div class="row mb-3">
    <div class="col-12">
        <h1 class="mb-3" style="font-size:1.35rem;font-weight:700;color:#0f172a;line-height:1.35;">
            {{ $searchHeading ?? 'Browse public health resources' }}
        </h1>

        {{-- Search Results Info Card --}}
        @if(isset($results_count) || isset($search_time))
        <div class="sidebar-content mb-3" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <p class="mb-0 me-4 fw-semibold" style="color:var(--theme-color-primary, #119A48);">{{ __('publications.search.results_summary') }}</p>
                    <span class="fw-bold" style="color:#1e293b;font-size:1rem;">
                        {{ $results_count ?? ($publications->total() ?? 0) }} {{ ($results_count ?? ($publications->total() ?? 0)) == 1 ? __('publications.search.result_found') : __('publications.search.results_found') }}
                    </span>
                </div>
                @if(isset($search_time))
                <div class="d-flex align-items-center">
                    <i class="fa fa-clock me-2" style="color:#64748b;font-size:0.9rem;"></i>
                    <span style="color:#64748b;font-size:0.9rem;">
                        {{ $search_time }} ms
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if(\App\Services\AiSearchInsightsService::isDisplayable($aiSearchInsights ?? null))
            @include('publications.partials.ai_search_insights')
        @endif

        @if(($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
            @include('publications.partials.ai_search_chat')
        @endif

        @if(($federationBrowseEnabled ?? false) && !request()->filled('term'))
            <div class="alert alert-light border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span><i class="fa fa-globe-africa me-2" style="color:var(--theme-color-primary,#119A48);"></i>Browse resources synced from partner country knowledge hubs.</span>
                <a href="{{ route('federation.browse') }}" class="btn btn-sm theme-bg text-white">
                    <i class="fa fa-globe-africa me-1"></i>{{ __('frontend_nav.partner_hubs') }}
                </a>
            </div>
        @endif

        {{-- Federated forums (partner country hubs) --}}
        @if(isset($federatedForums) && $federatedForums->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3" style="color:var(--theme-color-primary, #119A48);">
                <i class="fa fa-globe-africa mr-2"></i>Discussions from partner hubs
            </h5>
            @foreach($federatedForums as $forum)
                @include('partials.federation.forum_card', ['forum' => $forum])
            @endforeach
            <p class="mb-0 mt-2">
                <a href="{{ route('federation.browse', ['type' => 'forums', 'term' => request('term')]) }}" class="btn btn-sm btn-secondary">View all partner hub forums</a>
            </p>
        </div>
        @endif

        {{-- Related Communities (matching search term) --}}
        @if(isset($searchCommunities) && $searchCommunities->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3" style="color:var(--theme-color-primary, #119A48);">
                <i class="fa fa-users mr-2"></i>{{ __('publications.search.related_communities') }}
            </h5>
            <div class="row">
                @foreach($searchCommunities as $community)
                <div class="col-12 mb-3">
                    <div class="card border rounded" style="border-color:#e2e8f0;">
                        <div class="card-body py-3">
                            <a href="{{ community_detail_url($community) }}" class="text-decoration-none">
                                <h6 class="mb-1" style="color:#0f172a;font-size:1rem;">{{ Str::limit($community->community_name ?? '', 120) }}</h6>
                            </a>
                            @if(!empty($community->description))
                            <p class="mb-2 text-muted" style="font-size:0.875rem;">{{ Str::limit(strip_tags($community->description), 140) }}</p>
                            @endif
                            <div class="d-flex align-items-center flex-wrap" style="font-size:0.8rem;color:#64748b;">
                                @if($community->organisation)
                                <span class="mr-3"><i class="fa fa-building mr-1"></i>{{ Str::limit($community->organisation, 40) }}</span>
                                @endif
                                @if($community->region)
                                <span class="mr-3"><i class="fa fa-globe mr-1"></i>{{ $community->region->name ?? '' }}</span>
                                @endif
                            </div>
                            <a href="{{ community_detail_url($community) }}" class="btn btn-sm btn-outline-primary mt-2" style="border-color:var(--theme-color-primary,#119A48);color:var(--theme-color-primary,#119A48);">
                                <i class="fa fa-arrow-right mr-1"></i>{{ __('publications.search.view_community') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="mb-0 mt-2">
                <a href="{{ url('communities') }}{{ request()->filled('term') ? '?term=' . urlencode(request('term')) : '' }}" class="btn btn-sm btn-outline-primary" style="border-color:var(--theme-color-primary,#119A48);color:var(--theme-color-primary,#119A48);">{{ __('publications.search.view_all_communities') }}</a>
            </p>
        </div>
        @endif

        @if(request()->filled('tag'))
            @php
                $tagId = request('tag');
                $activeTag = \App\Models\Tag::find($tagId);
                $tagName = $activeTag ? $activeTag->tag_text : 'Tag #' . $tagId;
                $auGold = settings()->au_gold ?? '#B4A269';
                $auPlum = settings()->au_plum ?? '#522B39';
            @endphp
            <div class="mb-3" style="background-color: {{ $auGold }}; border: 1px solid {{ $auGold }}; border-radius: 0.25rem; padding: 0.75rem 1rem;">
                <div style="color: {{ $auPlum }};">
                    <strong>Tag:</strong> {{ $tagName }}
                </div>
            </div>
        @endif
    </div>
</div>

@include('partials.quiz.quiz')

@if (isset($sub_themes) && count($sub_themes) > 0)
    @include('publications.partials.subthemes')
@endif

@include('publications.partials.publications')
