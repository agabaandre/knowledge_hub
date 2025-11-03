@php
    $hide_search = true;
    // SEO Meta Tags for Community Detail Page
    $pageTitle = ($community->community_name ?? 'Community') . ' - Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = Str::limit(strip_tags($community->description ?? ''), 160) ?: ($community->community_name . ' - A professional community of practice focused on public health topics.');
    $pageKeywords = 'community of practice, ' . ($community->community_name ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = url('communities/detail/' . $community->id);
    $ogType = 'profile';
    
    // Get community stats
    $memberCount = $community->approved_members_count ?? $community->members_count ?? 0;
    $forumCount = $community->community_forums_count ?? $community->forums_count ?? 0;
    $publicationCount = $community->community_publications_count ?? $community->publications_count ?? 0;
@endphp

@extends('layouts.app')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "{{ addslashes($community->community_name ?? 'Community') }}",
    "description": "{{ addslashes(Str::limit(strip_tags($community->description ?? ''), 300)) }}",
    "url": "{{ $canonicalUrl }}",
    @if($community->region)
    "areaServed": {
        "@type": "Place",
        "name": "{{ addslashes($community->region->name ?? '') }}"
    },
    @endif
    "memberOf": {
        "@type": "Organization",
        "name": "{{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}"
    },
    "numberOfMembers": {{ $memberCount }},
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Communities",
                "item": "{{ url('communities') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes($community->community_name ?? 'Community') }}",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
}
</script>
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
</style>
@endsection

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
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs community-tabs" id="communityTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="publications-tab" data-toggle="tab" href="#publications" role="tab">
                        <i class="fa fa-book mr-1"></i>Publications
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="communityTabsContent">
                <!-- Publications Tab -->
                <div class="tab-pane fade show active" id="publications" role="tabpanel">
                    @if($publications->count() > 0)
                        @foreach($publications as $publication)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ url('records/resource') }}?id={{ $publication->id }}" class="text-dark">
                                            {{ $publication->title }}
                                        </a>
                                    </h5>
                                    @if($publication->author)
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fa fa-user mr-1"></i>Author: {{ $publication->author->name ?? 'Unknown' }}
                                        </p>
                                    @endif
                                    @if($publication->description)
                                        <p class="card-text">
                                            {!! \Illuminate\Support\Str::words(strip_tags($publication->description), 40, '...') !!}
                                        </p>
                                    @endif
                                    <div class="mt-2">
                                        <span class="badge badge-secondary">
                                            <i class="fa fa-calendar mr-1"></i>{{ $publication->created_at->format('M d, Y') }}
                                        </span>
                                        @if($publication->visits)
                                            <span class="badge badge-info ml-2">
                                                <i class="fa fa-eye mr-1"></i>{{ $publication->visits }} Views
                                            </span>
                                        @endif
                                    </div>
                                </div>
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
            </div>
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
                @if($members->count() > 0)
                    @foreach($members as $member)
                        <div class="member-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="member-name">{{ $member['name'] }}</div>
                                    <div class="member-title">
                                        <i class="fa fa-briefcase mr-1"></i>{{ $member['job_title'] }}
                                    </div>
                                    @if(isset($member['badges']) && $member['badges']->count() > 0)
                                        <div class="mt-2">
                                            @foreach($member['badges']->take(3) as $userBadge)
                                                <span class="badge mr-1" style="background-color: {{ $userBadge->badgeType->badge_color ?? '#C0C0C0' }}; color: white; font-size: 0.75rem; padding: 4px 8px;" title="{{ $userBadge->badgeType->name }} - {{ Carbon\Carbon::create($userBadge->year, $userBadge->month, 1)->format('M Y') }}">
                                                    @if($userBadge->badgeType->slug === 'silver')🥈
                                                    @elseif($userBadge->badgeType->slug === 'gold')🥇
                                                    @elseif($userBadge->badgeType->slug === 'platinum')💎
                                                    @elseif($userBadge->badgeType->slug === 'diamond')💠
                                                    @else🏅
                                                    @endif
                                                    {{ $userBadge->badgeType->name }}
                                                </span>
                                            @endforeach
                                            @if($member['badges']->count() > 3)
                                                <span class="badge badge-secondary" style="font-size: 0.75rem;">+{{ $member['badges']->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No members found.</p>
                @endif
            </div>

            <!-- Badge Requirements Info -->
            <div class="sidebar-card">
                <h5><i class="fa fa-trophy theme-text mr-2"></i>Contribution Badges</h5>
                <p class="small text-muted mb-3">Earn badges based on your monthly contributions (publications, forum posts, and comments):</p>
                @if(isset($badgeTypes) && $badgeTypes->count() > 0)
                    @foreach($badgeTypes as $badgeType)
                        <div class="mb-3 p-2" style="border-left: 3px solid {{ $badgeType->badge_color }}; background: {{ $badgeType->badge_color }}10; border-radius: 4px;">
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
                    <div class="mt-3 p-2" style="background: #f8f9fa; border-radius: 4px; font-size: 0.85rem;">
                        <strong>Note:</strong> Badges are awarded monthly based on your contributions. The system automatically calculates and awards badges at the end of each month.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab functionality is handled by Bootstrap
</script>
@endsection

