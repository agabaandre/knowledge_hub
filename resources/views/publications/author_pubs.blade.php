@extends('layouts.app')

@section('structured_data')
@if(!empty($authorProfileJsonLd))
<script type="application/ld+json">{!! json_encode($authorProfileJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
@endif
@endsection

@section('styles')
<style>
    .author-profile-page {
        padding: 2rem 0 3rem;
        background: #f8fafc;
    }
    .forum-contrib-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem 1.1rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .forum-contrib-card:hover {
        border-color: rgba(17, 154, 72, 0.25);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
    }
    .forum-contrib-meta {
        font-size: 0.85rem;
        color: #64748b;
    }
    .author-profile-sidebar .card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    }
</style>
@endsection

@section('content')
<div class="author-profile-page">
    <div class="container">
        <nav class="contributor-breadcrumb mb-3" aria-label="Breadcrumb">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0; font-size: 0.875rem;">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('browse.authors') }}">Contributors</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $author->name }}</li>
            </ol>
        </nav>

        @include('publications.partials.contributor_profile', [
            'author' => $author,
            'contributionStats' => $contributionStats,
            'contributorOrganization' => $contributorOrganization ?? null,
            'lifetimeBadge' => $lifetimeBadge ?? null,
            'badgeDrilldownYear' => $badgeDrilldownYear ?? null,
            'badgeDrilldownMonth' => $badgeDrilldownMonth ?? null,
            'communityBadgeStarCount' => $communityBadgeStarCount ?? 0,
        ])

        <div class="row g-4">
            <div class="col-lg-8">
                <h2 class="contributor-section-title">Forum contributions</h2>
                @if(isset($forumContributions) && method_exists($forumContributions, 'count') && $forumContributions->count() > 0)
                    @foreach($forumContributions as $forum)
                        <div class="forum-contrib-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <a href="{{ forum_thread_url($forum) }}" class="fw-semibold text-dark text-decoration-none">
                                    {!! strip_tags($forum->forum_title ?? 'Untitled discussion') !!}
                                </a>
                                <div class="d-flex flex-wrap gap-1">
                                    @if(!empty($forum->is_authored_by_contributor))
                                        <span class="badge bg-success">Started by contributor</span>
                                    @endif
                                    @if((int) ($forum->my_comment_count ?? 0) > 0)
                                        <span class="badge bg-info text-dark">Comments: {{ (int) $forum->my_comment_count }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="forum-contrib-meta mt-2">
                                <i class="fa fa-comments me-1"></i>{{ (int) ($forum->total_comments ?? 0) }} comments
                                <span class="mx-2">|</span>
                                <i class="fa fa-thumbs-up me-1"></i>{{ (int) ($forum->total_likes ?? 0) }} likes
                            </div>
                        </div>
                    @endforeach

                    @if(method_exists($forumContributions, 'hasPages') && $forumContributions->hasPages())
                        <div class="mt-2">
                            {{ $forumContributions->appends(request()->except('forums_page'))->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-light border mb-4">No forum contributions found for this contributor yet.</div>
                @endif

                <h2 class="contributor-section-title mt-2">Published resources</h2>
                @include('publications.partials.publications')
            </div>

            <div class="col-lg-4 author-profile-sidebar">
                @include('publications.partials.contributor_sidebar_styles')
                @include('publications.partials.contributor_communities', ['authorCommunities' => $authorCommunities ?? collect()])
                @include('publications.partials.facts')
            </div>
        </div>
    </div>
</div>
@endsection
