@php
    $hide_search = true;
    // SEO Meta Tags for Community Detail Page
    $pageTitle = ($community->community_name ?? 'Community') . ' - Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = Str::limit(strip_tags($community->description ?? ''), 160) ?: ($community->community_name . ' - A professional community of practice focused on public health topics.');
    $pageKeywords = 'community of practice, ' . ($community->community_name ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = url('communities/detail/' . $community->id);
    $ogType = 'profile';

    $highlightParticipantNames = collect($community->listing_contributor_faces ?? [])
        ->take(8)
        ->map(fn ($f) => $f['user']->name ?? '')
        ->filter()
        ->unique()
        ->values();
    if ($highlightParticipantNames->isNotEmpty()) {
        $pageKeywords .= ', ' . $highlightParticipantNames->implode(', ');
        $pageDescription = \Illuminate\Support\Str::limit(
            trim($pageDescription) . ' Featured participants: ' . $highlightParticipantNames->implode(', ') . '.',
            320
        );
    }

    // Get community stats
    $memberCount = $community->approved_members_count ?? $community->members_count ?? 0;
    $forumCount = $community->community_forums_count ?? $community->forums_count ?? 0;
    $publicationCount = $community->community_publications_count ?? $community->publications_count ?? 0;
@endphp

@extends('layouts.app')

@section('structured_data')
@if(!empty($communityOrganizationLd))
<script type="application/ld+json">{!! json_encode($communityOrganizationLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endif
@endsection

@section('styles')
<style>
    .theme-text {
        color: {{ settings()->primary_color ?? '#119A48' }};
    }
    .community-tabs {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    .nav-tabs .nav-link {
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: {{ settings()->primary_color ?? '#119A48' }};
        border-bottom-color: {{ settings()->primary_color ?? '#119A48' }};
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        color: {{ settings()->primary_color ?? '#119A48' }};
        border-bottom-color: #e2e8f0;
    }
    .sidebar-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .sidebar-card h5 {
        color: #2d3748;
        margin-bottom: 1rem;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .publication-item, .forum-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .publication-item:last-child, .forum-item:last-child {
        border-bottom: none;
    }
    .publication-item h6, .forum-item h6 {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    .publication-item a, .forum-item a {
        color: #2d3748;
        text-decoration: none;
    }
    .publication-item a:hover, .forum-item a:hover {
        color: {{ settings()->primary_color ?? '#119A48' }};
    }
    .member-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .member-item:last-child {
        border-bottom: none;
    }
    .member-name {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    .member-title {
        font-size: 0.875rem;
        color: #64748b;
    }
    .community-summary {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .community-summary:last-child {
        border-bottom: none;
    }
    .community-summary h6 {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    .community-summary a {
        color: {{ settings()->primary_color ?? '#119A48' }};
        text-decoration: none;
    }
    .community-summary a:hover {
        text-decoration: underline;
    }
    .community-meta {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 0.5rem;
    }
    #memberSearchForm .input-group {
        align-items: stretch;
    }
    #memberSearchForm .form-control {
        height: 40px;
        border-right: 0;
    }
    #memberSearchForm .btn {
        height: 40px;
        min-width: 44px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-left: 1px solid #ced4da;
    }
    #memberSearchForm .btn i {
        font-size: 20px;
        line-height: 1;
    }
    .community-featured-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.2s ease;
    }
    .community-featured-card:hover {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .community-featured-image {
        float: left;
        width: 120px;
        height: 120px;
        object-fit: contain;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }
    .community-featured-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    .community-featured-title a {
        color: inherit;
        text-decoration: none;
    }
    .community-featured-title a:hover {
        color: {{ settings()->primary_color ?? '#119A48' }};
    }
    .community-featured-meta {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .community-featured-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }
    .community-featured-actions a {
        color: {{ settings()->primary_color ?? '#119A48' }};
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .community-featured-actions a:hover {
        text-decoration: underline;
    }

    /* Dark mode: community detail page */
    html[data-bs-theme="dark"] .community-tabs { border-bottom-color: #3e4348; }
    html[data-bs-theme="dark"] .nav-tabs .nav-link { color: #9ca3af; }
    html[data-bs-theme="dark"] .nav-tabs .nav-link.active { color: var(--theme-color-primary, #119A48); border-bottom-color: var(--theme-color-primary, #119A48); }
    html[data-bs-theme="dark"] .nav-tabs .nav-link:hover { color: var(--theme-color-primary, #119A48); border-bottom-color: #3e4348; }
    html[data-bs-theme="dark"] .sidebar-card {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .sidebar-card h5 { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .sidebar-card .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .publication-item,
    html[data-bs-theme="dark"] .forum-item {
        border-bottom-color: #3e4348 !important;
    }
    html[data-bs-theme="dark"] .publication-item a,
    html[data-bs-theme="dark"] .forum-item a { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .publication-item a:hover,
    html[data-bs-theme="dark"] .forum-item a:hover { color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .member-item { border-bottom-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .member-name { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .member-title { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .community-summary { border-bottom-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .community-summary h6 a { color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .community-meta { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .community-detail-page .card { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb; }
    html[data-bs-theme="dark"] .community-detail-page .card-title a.text-dark { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .community-detail-page .card-title a.text-dark:hover { color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .community-detail-page .card .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .community-detail-page .alert-info { background: #2d3136 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .community-detail-page .badge-note-block { background: #2d3136 !important; border-radius: 4px; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .community-detail-page .badge-note-block .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .community-detail-page .badge-type-block { background: #2d3136 !important; }
    html[data-bs-theme="dark"] .community-detail-page .badge-type-block .small.text-muted { color: #9ca3af !important; }
    .community-content-request-card {
        border-left: 4px solid {{ settings()->primary_color ?? '#119A48' }};
    }
    html[data-bs-theme="dark"] .community-content-request-card {
        background: #2d3136 !important;
        border-color: #3e4348 !important;
        border-left-color: var(--theme-color-primary, #119A48) !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .community-detail-page .card.border-warning .border-top { border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .community-detail-page .open-cr-row { background: #2d3136 !important; }
</style>
@endsection

@php
    $processedContentRequestsTabActive = request()->filled('pcr_page');
@endphp

@section('content')
{{-- Custom Header Section (replaces search bar) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;" itemprop="name">
                        {{ $community->community_name }}
                    </h1>
                    <p style="margin: 0 0 1rem 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        {!! \Illuminate\Support\Str::words(strip_tags($community->description ?? ''), 50, '...') !!}
                    </p>
                    <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 0.5rem;">
                        <span class="badge badge-light" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                            <i class="fa fa-users mr-1"></i>{{ $community->approved_members_count ?? $community->members_count ?? 0 }} Members
                        </span>
                        <span class="badge badge-light" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                            <i class="fa fa-comments mr-1"></i>{{ $community->community_forums_count ?? $community->forums_count ?? 0 }} Forums
                        </span>
                        <span class="badge badge-light" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                            <i class="fa fa-book mr-1"></i>{{ $community->community_publications_count ?? $community->publications_count ?? 0 }} Publications
                        </span>
                    </div>
                    <div class="mt-3 d-flex justify-content-center flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#inviteColleaguesModal">
                            <i class="fa fa-envelope mr-1"></i>Invite colleagues (max 5)
                        </button>
                        @if(!empty($isCommunityAdmin))
                        <button type="button" class="btn btn-sm text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }}; border-color: {{ settings()->primary_color ?? '#119A48' }};" data-toggle="modal" data-target="#createCommunityEventModal">
                            <i class="fa fa-calendar-plus-o mr-1"></i>Create community event
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container community-detail-page">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            @if(isset($pendingCommunityContentRequests) && $pendingCommunityContentRequests->isNotEmpty())
            <div class="card mb-4 border-warning" style="border-width: 2px;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">
                        <i class="fa fa-inbox theme-text mr-2"></i>Open content requests
                    </h5>
                    <span class="badge badge-warning text-dark">{{ $pendingCommunityContentRequests->count() }} pending</span>
                </div>
                <div class="card-body p-0">
                    <p class="text-muted small px-3 pt-3 mb-2">These requests were referred to this community for discussion. They are not yet marked as fully processed in the hub.</p>
                    @foreach($pendingCommunityContentRequests as $cr)
                        <div class="border-top px-3 py-3 community-content-request-card open-cr-row" style="background: #fffbeb;">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1 font-weight-bold">{{ $cr->subject }}</h6>
                                    <div class="small text-muted">
                                        @if($cr->country)<span class="mr-2"><i class="fa fa-globe mr-1"></i>{{ $cr->country->name }}</span>@endif
                                        @if($cr->referred_at)<span><i class="fa fa-share mr-1"></i>Referred {{ $cr->referred_at->format('M j, Y') }}</span>@endif
                                        @if($cr->referredByUser)<span class="ml-2">by {{ $cr->referredByUser->name }}</span>@endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap shrink-0" style="gap: 6px;">
                                    <a href="{{ $cr->discussionUrlForCommunity($community->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-comments mr-1"></i>Forum thread
                                    </a>
                                    @if($cr->userMayMarkReferralAsProcessed(auth()->user()))
                                        <a href="{{ $cr->discussionUrlForCommunity($community->id) }}#mark-processed" class="btn btn-sm btn-success">
                                            <i class="fa fa-check mr-1"></i>Mark processed
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if($cr->referral_notes)
                                <p class="small mb-0 mt-2 text-muted"><strong>Note:</strong> {{ Str::limit(strip_tags($cr->referral_notes), 200) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tabs -->
            <ul class="nav nav-tabs community-tabs" id="communityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $processedContentRequestsTabActive ? '' : 'active' }}" id="publications-tab" data-toggle="tab" href="#publications" role="tab">
                        <i class="fa fa-book mr-1"></i>Publications
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $processedContentRequestsTabActive ? 'active' : '' }}" id="processed-requests-tab" data-toggle="tab" href="#processed-content-requests" role="tab">
                        <i class="fa fa-check-circle mr-1"></i>Processed requests
                        @if(isset($processedCommunityContentRequests) && $processedCommunityContentRequests->total() > 0)
                            <span class="badge badge-secondary ml-1">{{ $processedCommunityContentRequests->total() }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="communityTabsContent">
                <!-- Publications Tab -->
                <div class="tab-pane fade {{ $processedContentRequestsTabActive ? '' : 'show active' }}" id="publications" role="tabpanel">
                    @if($publications->count() > 0)
                        @foreach($publications as $publication)
                            @php
                                $defaultImage = asset('assets/images/cover.png');
                                $imageLink = resolve_publication_card_cover($publication);
                            @endphp
                            <div class="community-featured-card community-detail-card">
                                <a href="{{ url('records/resource') }}?id={{ $publication->id }}">
                                    <img src="{{ $imageLink }}" alt="{{ $publication->title }}" class="community-featured-image" onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
                                </a>
                                <h5 class="community-featured-title">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->id }}">
                                            {{ $publication->title }}
                                        </a>
                                    </h5>
                                <div class="community-featured-meta">
                                    @if($publication->author)
                                        <span><i class="fa fa-user mr-1"></i>{{ $publication->author->name ?? 'Unknown' }}</span>
                                    @endif
                                    <span class="ml-2"><i class="fa fa-clock-o mr-1"></i>{{ time_ago($publication->updated_at ?? $publication->created_at) }}</span>
                                </div>
                                    @if($publication->description)
                                    <p class="mb-0" style="text-align: justify;">
                                            {!! \Illuminate\Support\Str::words(strip_tags($publication->description), 40, '...') !!}
                                        </p>
                                    @endif
                                <div class="community-featured-actions">
                                    <a href="{{ url('records/resource') }}?id={{ $publication->id }}">Read More <i class="fa fa-arrow-right"></i></a>
                                    <span class="text-muted small"><i class="fa fa-eye mr-1"></i>{{ $publication->visits ?? 0 }} Visits</span>
                                </div>
                                <div style="clear: both;"></div>
                            </div>
                        @endforeach
                        <div class="mt-4">
                            {{ $publications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>No publications found in this community.
                        </div>
                    @endif
                </div>

                <!-- Processed content requests (referred to this community, hub workflow complete) -->
                <div class="tab-pane fade {{ $processedContentRequestsTabActive ? 'show active' : '' }}" id="processed-content-requests" role="tabpanel">
                    @if(isset($processedCommunityContentRequests) && $processedCommunityContentRequests->total() > 0)
                        <p class="text-muted small mb-3">Requests that were referred to this community and later completed through the Knowledge Hub (resources shared with the requester).</p>
                        @foreach($processedCommunityContentRequests as $cr)
                            <div class="community-featured-card community-content-request-card mb-3">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h5 class="community-featured-title mb-1">{{ $cr->subject }}</h5>
                                        <div class="community-featured-meta">
                                            @if($cr->country)<span class="mr-2"><i class="fa fa-globe mr-1"></i>{{ $cr->country->name }}</span>@endif
                                            <span><i class="fa fa-check mr-1"></i>Processed {{ $cr->processed_at ? $cr->processed_at->format('M j, Y') : '—' }}</span>
                                            @if($cr->processedBy)<span class="ml-2"><i class="fa fa-user mr-1"></i>{{ $cr->processedBy->name }}</span>@endif
                                        </div>
                                    </div>
                                    <a href="{{ $cr->discussionUrlForCommunity($community->id) }}" class="btn btn-sm btn-outline-primary shrink-0">
                                        <i class="fa fa-comments mr-1"></i>Thread
                                    </a>
                                </div>
                                @if($cr->content_links)
                                    <div class="small mt-2 p-2 bg-light rounded" style="white-space: pre-wrap; max-height: 120px; overflow-y: auto;">{{ Str::limit(strip_tags($cr->content_links), 400) }}</div>
                                @endif
                                @if($cr->admin_comments)
                                    <p class="small text-muted mb-0 mt-2"><strong>Admin comments:</strong> {{ Str::limit(strip_tags($cr->admin_comments), 200) }}</p>
                                @endif
                            </div>
                        @endforeach
                        <div class="mt-3">
                            {{ $processedCommunityContentRequests->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle mr-2"></i>No processed content requests yet for this community. When referred requests are completed in the hub, they will appear here.
                        </div>
                    @endif
                </div>
            </div>
            @if(isset($communityEvents) && $communityEvents->count() > 0)
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fa fa-calendar theme-text mr-2"></i>Community Events</h5>
                    @foreach($communityEvents as $event)
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>{{ $event->title }}</strong>
                                <span class="badge badge-info">{{ $event->event_category ?? 'Event' }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="fa fa-clock-o mr-1"></i>{{ \Carbon\Carbon::parse($event->startdate)->format('M d, Y H:i') }}
                                @if($event->enddate)
                                    - {{ \Carbon\Carbon::parse($event->enddate)->format('M d, Y H:i') }}
                                @endif
                            </div>
                            @if($event->venue)<div class="small"><i class="fa fa-map-marker mr-1"></i>{{ $event->venue }}</div>@endif
                            @if($event->event_link)<div class="small"><a href="{{ $event->event_link }}" target="_blank" rel="noopener">Open event link</a></div>@endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Forum Engagements -->
            <div class="sidebar-card">
                <h5><i class="fa fa-comments theme-text mr-2"></i>Recent Forum Discussions</h5>
                @if($forums->count() > 0)
                    @foreach($forums as $forum)
                        <div class="forum-item">
                            <h6>
                                <a href="{{ url('forums/thread') }}?id={{ $forum->id }}">
                                    {{ \Illuminate\Support\Str::limit($forum->forum_title ?? 'Untitled', 60) }}
                                </a>
                            </h6>
                            <p class="text-muted small mb-0">
                                <i class="fa fa-user mr-1"></i>{{ $forum->user->name ?? 'Unknown' }}
                            </p>
                        </div>
                    @endforeach
                    <div class="mt-3">
                        <a href="{{ url('forums') }}?community_id={{ $community->id }}" class="btn btn-sm btn-outline-primary">
                            View All Forums <i class="fa fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <p class="text-muted">No forum discussions yet.</p>
                @endif
            </div>

            <!-- Other Communities -->
            <div class="sidebar-card">
                <h5><i class="fa fa-users theme-text mr-2"></i>My Other Communities</h5>
                @if($otherCommunities->count() > 0)
                    @foreach($otherCommunities as $otherCommunity)
                        <div class="community-summary">
                            <h6>
                                <a href="{{ route('community.detail', $otherCommunity->id) }}">
                                    {{ $otherCommunity->community_name }}
                                </a>
                            </h6>
                            <p class="small text-muted mb-2">
                                {!! \Illuminate\Support\Str::words(strip_tags($otherCommunity->description ?? ''), 20, '...') !!}
                            </p>
                            <div class="community-meta">
                                <span><i class="fa fa-book mr-1"></i>{{ $otherCommunity->community_publications_count ?? 0 }} Publications</span>
                                <span class="ml-3"><i class="fa fa-comments mr-1"></i>{{ $otherCommunity->community_forums_count ?? 0 }} Forums</span>
                                <span class="ml-3"><i class="fa fa-users mr-1"></i>{{ $otherCommunity->approved_members_count ?? 0 }} Members</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">You are not a member of any other communities.</p>
                @endif
            </div>

            <!-- Community Members -->
            <div class="sidebar-card">
                <h5><i class="fa fa-users theme-text mr-2"></i>Community Members</h5>
                <form id="memberSearchForm" class="mb-2" onsubmit="return false;">
                    <div class="input-group input-group-sm">
                        <input type="text" id="memberSearchInput" class="form-control" placeholder="Search members...">
                        <button type="button" class="btn btn-outline-secondary"><i class="fa fa-search"></i></button>
                                    </div>
                </form>
                <ul id="communityMembersList" class="list-unstyled mb-2"></ul>
                <div id="communityMembersEmpty" class="text-muted mb-0" style="display:none;">No members found.</div>
                <div id="communityMembersLoader" class="text-center text-muted small py-2" style="display:none;">Loading members...</div>
                <div id="communityMembersEnd" class="text-center text-muted small py-2" style="display:none;">End of members list.</div>
                <div id="communityMembersSentinel"></div>
            </div>

            <!-- Badge Requirements Info -->
            <div class="sidebar-card">
                <h5><i class="fa fa-trophy theme-text mr-2"></i>Contribution Badges</h5>
                <p class="small text-muted mb-3">Earn badges based on your monthly contributions (publications, forum posts, and comments):</p>
                        @if(isset($badgeTypes) && $badgeTypes->count() > 0)
                        @foreach($badgeTypes as $badgeType)
                        <div class="mb-3 p-2 badge-type-block" style="border-left: 3px solid {{ $badgeType->badge_color }}; background: {{ $badgeType->badge_color }}10; border-radius: 4px;">
                            <div class="d-flex align-items-center mb-1">
                                <span style="font-size: 1.2em; margin-right: 8px;">
                                    @if($badgeType->slug === 'silver')🥈
                                    @elseif($badgeType->slug === 'gold')🥇
                                    @elseif($badgeType->slug === 'platinum')💎
                                    @elseif($badgeType->slug === 'diamond')💠
                                    @else🏅
                                    @endif
                                </span>
                                <strong style="color: {{ $badgeType->badge_color }};">{{ $badgeType->name }}</strong>
                            </div>
                            <div class="small text-muted">
                                {{ $badgeType->contribution_threshold }}+ contributions/month
                            </div>
                            @if($badgeType->slug === 'diamond')
                                <div class="small mt-1" style="font-weight: 600; color: {{ $badgeType->badge_color }};">
                                    🏆 Hall of Honor
                                </div>
                            @endif
                        </div>
                    @endforeach
                    <div class="mt-3 p-2 badge-note-block" style="background: #f8f9fa; border-radius: 4px; font-size: 0.85rem;">
                        <strong>Note:</strong> Badges are awarded monthly based on your contributions. The system automatically calculates and awards badges at the end of each month.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inviteColleaguesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Invite colleagues</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form id="inviteColleaguesForm">
                @csrf
                <div class="modal-body">
                    <label>Emails (max 5)</label>
                    <textarea class="form-control" name="emails" rows="3" placeholder="name1@example.com, name2@example.com" required></textarea>
                    <small class="text-muted">Separate by comma, space, or new line.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send invitations</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($isCommunityAdmin))
<div class="modal fade" id="createCommunityEventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create community event</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form id="createCommunityEventForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group"><label>Title</label><input class="form-control" name="title" required></div>
                    <div class="form-group"><label>Type</label>
                        <select class="form-control" name="event_category" required>
                            <option value="Meeting">Meeting</option>
                            <option value="Webinar">Webinar</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Training">Training</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
                    <div class="form-group"><label>Start date/time</label><input type="datetime-local" class="form-control" name="startdate" required></div>
                    <div class="form-group"><label>End date/time</label><input type="datetime-local" class="form-control" name="enddate"></div>
                    <div class="form-group"><label>Venue</label><input class="form-control" name="venue"></div>
                    <div class="form-group"><label>Event link (optional)</label><input type="url" class="form-control" name="event_link"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }}; border-color: {{ settings()->primary_color ?? '#119A48' }};">Create event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    (function () {
        var membersState = {
            page: 1,
            perPage: 20,
            hasMore: true,
            loading: false,
            query: '',
            isCommunityAdmin: {{ !empty($isCommunityAdmin) ? 'true' : 'false' }}
        };

        function escapeHtml(v) {
            return String(v || '').replace(/[&<>"']/g, function (m) {
                return ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'})[m];
            });
        }

        function memberItemHtml(item) {
            var adminBadge = item.is_admin
                ? '<span class="badge text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }};">Admin</span>'
                : '<span class="badge badge-secondary">Member</span>';
            var activeBadge = item.is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            var actionHtml = '';
            if (membersState.isCommunityAdmin && parseInt(item.user_id, 10) !== {{ (int)(auth()->id() ?? 0) }}) {
                if (item.is_active) {
                    actionHtml = '<div class="mt-2"><button class="btn btn-sm btn-outline-danger js-member-toggle" data-member-id="' + item.membership_id + '" data-action="deactivate" type="button">Mark inactive</button></div>';
                } else {
                    actionHtml = '<div class="mt-2"><button class="btn btn-sm btn-outline-success js-member-toggle" data-member-id="' + item.membership_id + '" data-action="activate" type="button">Mark active</button></div>';
                }
            }
            return '' +
                '<li class="member-item">' +
                '  <div class="d-flex justify-content-between align-items-start">' +
                '    <div class="flex-grow-1">' +
                '      <div class="member-name"><span class="badge badge-secondary mr-1">' + item.rank + '</span>' + escapeHtml(item.name) + '</div>' +
                '      <div class="member-title"><i class="fa fa-briefcase mr-1"></i>' + escapeHtml(item.job_title || 'Not specified') + '</div>' +
                '      <div class="small text-muted mt-1"><i class="fa fa-envelope mr-1"></i>' + escapeHtml(item.email) + '</div>' +
                '      <div class="mt-1">' + adminBadge + ' ' + activeBadge + ' <span class="badge badge-info">' + parseInt(item.publication_count, 10) + ' Publications</span></div>' +
                actionHtml +
                '    </div>' +
                '  </div>' +
                '</li>';
        }

        function setMembersUiState() {
            var listCount = jQuery('#communityMembersList').children().length;
            jQuery('#communityMembersEmpty').toggle(!membersState.loading && listCount === 0);
            jQuery('#communityMembersLoader').toggle(membersState.loading);
            jQuery('#communityMembersEnd').toggle(!membersState.loading && !membersState.hasMore && listCount > 0);
        }

        function fetchMembers(reset) {
            if (membersState.loading) return;
            if (!membersState.hasMore && !reset) return;

            if (reset) {
                membersState.page = 1;
                membersState.hasMore = true;
                jQuery('#communityMembersList').empty();
            }

            membersState.loading = true;
            setMembersUiState();

            jQuery.get('{{ route('community.members-data', $community->id) }}', {
                page: membersState.page,
                per_page: membersState.perPage,
                q: membersState.query
            }).done(function (res) {
                var items = Array.isArray(res.items) ? res.items : [];
                membersState.hasMore = !!res.has_more;
                if (typeof res.is_community_admin !== 'undefined') {
                    membersState.isCommunityAdmin = !!res.is_community_admin;
                }
                if (items.length > 0) {
                    var html = items.map(memberItemHtml).join('');
                    jQuery('#communityMembersList').append(html);
                }
                if (membersState.hasMore) {
                    membersState.page += 1;
                }
            }).fail(function () {
                // keep current items on failure
            }).always(function () {
                membersState.loading = false;
                setMembersUiState();
            });
        }

        var inviteForm = document.getElementById('inviteColleaguesForm');
        if (inviteForm) {
            inviteForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(inviteForm);
                fetch('{{ route('community.invite', $community->id) }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: fd
                }).then(r => r.json()).then(function (res) {
                    alert(res.message || 'Done');
                    if (res.status === 'success') location.reload();
                }).catch(function () { alert('Failed to send invitations.'); });
            });
        }

        jQuery(document).on('click', '.js-member-toggle', function () {
            var fd = new FormData();
            fd.append('member_id', this.dataset.memberId);
            fd.append('action', this.dataset.action);
            fetch('{{ route('community.member-status', $community->id) }}', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                body: fd
            }).then(r => r.json()).then(function (res) {
                alert(res.message || 'Done');
                if (res.status === 'success') location.reload();
            }).catch(function () { alert('Failed to update member status.'); });
        });

        var eventForm = document.getElementById('createCommunityEventForm');
        if (eventForm) {
            eventForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(eventForm);
                fetch('{{ route('community.events.create', $community->id) }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: fd
                }).then(r => r.json()).then(function (res) {
                    alert(res.message || 'Done');
                    if (res.status === 'success') location.reload();
                }).catch(function () { alert('Failed to create event.'); });
            });
        }

        var searchTimer = null;
        jQuery('#memberSearchInput').on('input', function () {
            var val = jQuery(this).val() || '';
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                membersState.query = val.trim();
                fetchMembers(true);
            }, 300);
        });

        var sentinel = document.getElementById('communityMembersSentinel');
        if (sentinel && 'IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        fetchMembers(false);
                    }
                });
            }, { root: null, rootMargin: '120px 0px', threshold: 0.01 });
            observer.observe(sentinel);
        }

        fetchMembers(true);

    })();
</script>
@endsection

