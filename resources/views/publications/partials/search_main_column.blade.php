<div class="row mb-3">
    <div class="col-12">
        {{-- Search Results Info Card --}}
        @if(isset($results_count) || isset($search_time))
        <div class="sidebar-content mb-3" style="background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <h5 class="mb-0 me-4" style="color:var(--theme-color-primary, #119A48);">Search Results</h5>
                    <span class="fw-bold" style="color:#1e293b;font-size:1rem;">
                        {{ $results_count ?? ($publications->total() ?? 0) }} {{ ($results_count ?? ($publications->total() ?? 0)) == 1 ? 'result' : 'results' }} found
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

        {{-- Related Discussions (forums matching search term) --}}
        @if(isset($searchForums) && $searchForums->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3" style="color:var(--theme-color-primary, #119A48);">
                <i class="fa fa-comments mr-2"></i>Related Discussions
            </h5>
            <div class="row">
                @foreach($searchForums as $forum)
                <div class="col-12 mb-3">
                    <div class="card border rounded" style="border-color:#e2e8f0;">
                        <div class="card-body py-3">
                            <a href="{{ url('forums/thread') }}?id={{ $forum->id }}" class="text-decoration-none">
                                <h6 class="mb-1" style="color:#0f172a;font-size:1rem;">{!! Str::limit(strip_tags($forum->forum_title ?? ''), 120) !!}</h6>
                            </a>
                            @if(!empty($forum->forum_description))
                            <p class="mb-2 text-muted" style="font-size:0.875rem;">{{ Str::limit(strip_tags($forum->forum_description), 140) }}</p>
                            @endif
                            <div class="d-flex align-items-center flex-wrap" style="font-size:0.8rem;color:#64748b;">
                                @if($forum->user)
                                <span class="mr-3"><i class="fa fa-user mr-1"></i>{{ $forum->user->name ?? 'Unknown' }}</span>
                                @endif
                                <span class="mr-3"><i class="fa fa-clock mr-1"></i>{{ time_ago($forum->created_at) }}</span>
                                <span class="mr-3"><i class="fa fa-comments mr-1"></i>{{ $forum->total_comments ?? 0 }} Comments</span>
                                <span><i class="fa fa-eye mr-1"></i>{{ $forum->views ?? 0 }} Views</span>
                            </div>
                            <a href="{{ url('forums/thread') }}?id={{ $forum->id }}" class="btn btn-sm btn-primary mt-2">
                                <i class="fa fa-arrow-right mr-1"></i>View Discussion
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="mb-0 mt-2">
                <a href="{{ url('forums') }}{{ request()->filled('term') ? '?term=' . urlencode(request('term')) : '' }}" class="btn btn-sm btn-secondary">View all discussions</a>
            </p>
        </div>
        @endif

        {{-- Related Communities (matching search term) --}}
        @if(isset($searchCommunities) && $searchCommunities->count() > 0)
        <div class="mb-4">
            <h5 class="mb-3" style="color:var(--theme-color-primary, #119A48);">
                <i class="fa fa-users mr-2"></i>Related Communities
            </h5>
            <div class="row">
                @foreach($searchCommunities as $community)
                <div class="col-12 mb-3">
                    <div class="card border rounded" style="border-color:#e2e8f0;">
                        <div class="card-body py-3">
                            <a href="{{ url('communities/detail/' . $community->id) }}" class="text-decoration-none">
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
                            <a href="{{ url('communities/detail/' . $community->id) }}" class="btn btn-sm btn-primary mt-2">
                                <i class="fa fa-arrow-right mr-1"></i>View Community
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="mb-0 mt-2">
                <a href="{{ url('communities') }}{{ request()->filled('term') ? '?term=' . urlencode(request('term')) : '' }}" class="btn btn-sm btn-secondary">View all communities</a>
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
