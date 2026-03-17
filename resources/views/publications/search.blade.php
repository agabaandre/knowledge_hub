@php
    $pageTitle = $pageTitle ?? ('Search Resources & Discussions - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub'));
    $pageDescription = $pageDescription ?? 'Search publications, resources and discussion forums. Find public health content and join discussions across Africa.';
    $canonicalUrl = $canonicalUrl ?? url('records/search');
@endphp
@extends('layouts.app')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SearchResultsPage",
    "name": "{{ addslashes($pageTitle) }}",
    "description": "{{ addslashes(strip_tags($pageDescription)) }}",
    "url": "{{ $canonicalUrl }}",
    "mainEntity": {
        "@type": "ItemList",
        "numberOfItems": {{ $results_count ?? 0 }},
        "itemListElement": [
            @if(isset($publications) && $publications->count() > 0)
                @foreach($publications->take(10) as $index => $pub)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "item": {
                        "@type": "Article",
                        "name": "{{ addslashes(Str::limit(strip_tags($pub->title ?? ''), 100)) }}",
                        "url": "{{ url('records/resource?id=' . $pub->id) }}",
                        "description": "{{ addslashes(Str::limit(strip_tags($pub->description ?? ''), 200)) }}"
                    }
                }@if(!$loop->last || (isset($searchForums) && $searchForums->count() > 0) || (isset($searchCommunities) && $searchCommunities->count() > 0)),@endif
                @endforeach
            @endif
            @if(isset($searchForums) && $searchForums->count() > 0)
                @foreach($searchForums as $index => $forum)
                {
                    "@type": "ListItem",
                    "position": {{ (isset($publications) && $publications->count() > 0 ? min(10, $publications->count()) : 0) + $index + 1 }},
                    "item": {
                        "@type": "DiscussionForumPosting",
                        "name": "{{ addslashes(Str::limit(strip_tags($forum->forum_title ?? ''), 100)) }}",
                        "url": "{{ url('forums/thread?id=' . $forum->id) }}",
                        "description": "{{ addslashes(Str::limit(strip_tags($forum->forum_description ?? ''), 200)) }}",
                        "author": {
                            "@type": "Person",
                            "name": "{{ addslashes($forum->user->name ?? 'Anonymous') }}"
                        }
                    }
                }@if(!$loop->last || (isset($searchCommunities) && $searchCommunities->count() > 0)),@endif
                @endforeach
            @endif
            @if(isset($searchCommunities) && $searchCommunities->count() > 0)
                @foreach($searchCommunities as $index => $community)
                {
                    "@type": "ListItem",
                    "position": {{ (isset($publications) && $publications->count() > 0 ? min(10, $publications->count()) : 0) + (isset($searchForums) ? $searchForums->count() : 0) + $index + 1 }},
                    "item": {
                        "@type": "Organization",
                        "name": "{{ addslashes(Str::limit($community->community_name ?? '', 100)) }}",
                        "url": "{{ url('communities/detail/' . $community->id) }}",
                        "description": "{{ addslashes(Str::limit(strip_tags($community->description ?? ''), 200)) }}"
                    }
                }@if(!$loop->last),@endif
                @endforeach
            @endif
        ]
    },
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}" },
            { "@type": "ListItem", "position": 2, "name": "Search", "item": "{{ $canonicalUrl }}" }
        ]
    }
}
</script>
@endsection

@section('styles')
@endsection

@section('content')
    <div class="gray py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
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
                            
                            @if(isset($_GET['tag']) && !empty($_GET['tag']))
                                @php
                                    $tagId = $_GET['tag'];
                                    $tag = \App\Models\Tag::find($tagId);
                                    $tagName = $tag ? $tag->tag_text : 'Tag #' . $tagId;
                                    $auGold = settings()->au_gold ?? '#B4A269';
                                    $auPlum = settings()->au_plum ?? '#522B39'; // Agenda 2063 Plum (PANTONE 3415 C)
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

                    @if (count($sub_themes) > 0)
                        @include('publications.partials.subthemes')
                    @endif

                    @include('publications.partials.publications')

                </div>

                <div class="col-lg-4">
                    <style>
                        .sidebar-content{background:#fff;border:1px solid #e2e8f0;border-radius:0.5rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                        .sidebar-content h5.popular-tags-title{margin-bottom:10px;font-size:15px;font-weight:500;text-transform:capitalize;color:#2d3748}
                        .sidebar-content .btn-primary{background-color:var(--theme-color-primary, #119A48);border-color:var(--theme-color-primary, #119A48);font-weight:500;border-radius:0.375rem}
                        .sidebar-content .btn-primary:hover{background-color:var(--theme-color-primary, #0d7a38);border-color:var(--theme-color-primary, #0d7a38);filter:brightness(1.05)}
                        .sidebar-content .btn-secondary{font-weight:500;border-radius:0.375rem}
                        .sidebar-tags{display:flex;flex-wrap:wrap;gap:0.5rem}
                        .sidebar-tag-pill{display:inline-block;padding:0.3rem 0.7rem;font-size:0.8rem;font-weight:500;color:#ffffff !important;text-decoration:none;border-radius:0.25rem;transition:all 0.2s ease;white-space:nowrap;background-color:var(--theme-color-primary, #119A48) !important;border:1px solid rgba(17,154,72,0.3)}
                        .sidebar-tag-pill:hover{transform:translateY(-2px);box-shadow:0 2px 6px rgba(17,154,72,0.3);color:#ffffff !important;text-decoration:none;background-color:var(--theme-color-primary, #119A48) !important}
                    </style>

                    {{-- Popular Tags --}}
                    @if(isset($tags) && count($tags) > 0)
                    <div class="sidebar-content">
                      <h5 class="popular-tags-title">Popular Tags</h5>
                      <div class="sidebar-tags">
                        @foreach($tags->take(10) as $tag)
                        <a href="{{ url('records')}}?tag={{$tag->id}}" 
                           class="sidebar-tag-pill" 
                           title="{{$tag->tag_text}}">
                          {{ truncate($tag->tag_text,15) }}
                        </a>
                        @endforeach
                      </div>
                    </div>
                    @else
                    {{-- Fallback: Show empty state or get tags from view composer --}}
                    @php
                        $allTags = \App\Models\Tag::orderBy('id', 'desc')->limit(10)->get();
                    @endphp
                    @if($allTags->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="popular-tags-title">Popular Tags</h5>
                      <div class="sidebar-tags">
                        @foreach($allTags as $tag)
                        <a href="{{ url('records')}}?tag={{$tag->id}}" 
                           class="sidebar-tag-pill" 
                           title="{{$tag->tag_text}}">
                          {{ truncate($tag->tag_text,15) }}
                        </a>
                        @endforeach
                      </div>
                    </div>
                    @endif
                    @endif

                    {{-- Related Resources --}}
                    @if(isset($relatedPublications) && $relatedPublications->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="mb-3">Related Resources</h5>
                      @foreach($relatedPublications->take(5) as $pub)
                      <div class="mb-3 pb-3 border-bottom">
                        <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
                          <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
                        </a>
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>
                            @if(!empty($pub->author->orcid))
                                <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                                    {{ $pub->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                </a>
                            @else
                                {{ $pub->author->name }}
                            @endif
                          </span>
                          @endif
                          <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
                        </div>
                      </div>
                      @endforeach
                      <a href="{{ url('records') }}" class="btn btn-sm btn-primary w-100 mt-2">View All Resources</a>
                    </div>
                    @endif

                    {{-- Latest Publications --}}
                    @if(isset($latestPublications) && $latestPublications->count() > 0)
                    <div class="sidebar-content">
                      <h5 class="mb-3">Latest Publications</h5>
                      @foreach($latestPublications->take(5) as $pub)
                      <div class="mb-3 pb-3 border-bottom">
                        <a href="{{ url('records/resource?id=' . $pub->id) }}" class="text-decoration-none">
                          <h6 class="mb-1" style="font-size:0.9rem;color:#0f172a;line-height:1.4;">{{ Str::limit(strip_tags($pub->title), 80) }}</h6>
                        </a>
                        <p class="mb-1" style="font-size:0.8rem;color:#64748b;">{{ Str::limit(strip_tags(clean_unicode(publication_description_for_list($pub->description ?? ''))), 100) }}</p>
                        <div class="d-flex align-items-center" style="font-size:0.75rem;color:#94a3b8;">
                          @if($pub->author)
                          <span class="mr-2"><i class="fa fa-user mr-1"></i>
                            @if(!empty($pub->author->orcid))
                                <a href="https://orcid.org/{{ $pub->author->orcid }}" target="_blank" rel="noopener noreferrer" title="View {{ $pub->author->name }}'s ORCID profile" style="color: inherit; text-decoration: none;">
                                    {{ $pub->author->name }}
                                    <i class="fa fa-external-link-alt" style="font-size: 0.65rem; margin-left: 2px;"></i>
                                </a>
                            @else
                                {{ $pub->author->name }}
                            @endif
                          </span>
                          @endif
                          <span><i class="fa fa-calendar mr-1"></i>{{ $pub->created_at->format('M Y') }}</span>
                        </div>
                      </div>
                      @endforeach
                      <a href="{{ url('records') }}" class="btn btn-sm btn-primary w-100 mt-2">View All Publications</a>
                    </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        @include('common.select2')
    @endsection
