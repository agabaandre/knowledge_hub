@php
    $hide_search = false;
@endphp

@extends('layouts.app')

@section('structured_data')
@if(!empty($authorsIndexJsonLd))
<script type="application/ld+json">{!! json_encode($authorsIndexJsonLd, $jsonLdFlags ?? (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !!}</script>
@endif
@endsection

@section('styles')
<style>
    .authors-wrapper {
        padding: 2rem 0;
    }

    .authors-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .authors-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .authors-header .authors-lead {
        color: #64748b;
        font-size: 1rem;
        margin: 0 auto;
        max-width: 42rem;
        line-height: 1.6;
    }

    .author-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .author-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--theme-color-primary, #119A48);
        transform: translateY(-2px);
    }

    .author-header {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .author-avatar {
        width: 70px;
        height: 70px;
        border-radius: 4px;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .author-avatar-icon {
        font-size: 2rem;
        color: var(--theme-color-primary, #119A48);
    }

    .author-info {
        flex: 1;
        min-width: 0;
    }

    .author-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        word-wrap: break-word;
    }

    .author-name a {
        color: inherit;
        text-decoration: none;
    }

    .author-name a:hover {
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
    }

    .author-title {
        font-size: 0.875rem;
        color: #4a5568;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .author-organization {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .author-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .author-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
        border-radius: 4px;
        white-space: nowrap;
    }

    .author-stats {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .author-resources {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .author-resources i {
        color: var(--theme-color-primary, #119A48);
        margin-right: 0.25rem;
    }

    .view-link {
        color: var(--theme-color-primary, #119A48);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
    }

    .view-link:hover {
        text-decoration: underline;
    }

    .no-results {
        text-align: center;
        padding: 3rem 2rem;
        color: #718096;
    }

    .no-results i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e0;
    }

    @media (max-width: 768px) {
        .author-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .author-info {
            text-align: center;
        }

        .author-badges {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="authors-wrapper">
<div class="container">
        <header class="authors-header">
            <h1>
                <i class="fa fa-users me-2" aria-hidden="true"></i>Contributors &amp; Authors
                @if(isset($authorsTotal) && $authorsTotal > 0)
                    <small class="text-muted">({{ number_format($authorsTotal) }} total)</small>
                @elseif(isset($authors) && method_exists($authors, 'total'))
                    <small class="text-muted">({{ number_format($authors->total()) }} total)</small>
                @endif
            </h1>
            <p class="authors-lead">
                @if(!empty($authorsSearchTerm))
                    Showing contributors matching <strong>{{ $authorsSearchTerm }}</strong>.
                @else
                    Discover researchers, clinicians, ministries, and institutions sharing verified public health publications, resources, and forum expertise across Africa.
                @endif
            </p>
        </header>
					
        @if(isset($authors) && $authors->count() > 0)
        <div class="row">
@foreach($authors as $author)
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-4">
                @php
                    $isOrg = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
                    $avatarUrl = null;
                    if (!empty($author->logo) && $author->logo !== 'author.png') {
                        $avatarUrl = filter_var($author->logo, FILTER_VALIDATE_URL) ? $author->logo : asset(ltrim($author->logo, '/'));
                    } elseif ($author->user && !empty($author->user->photo)) {
                        $avatarUrl = $author->user->photo;
                    }
                    $cardId = 'contributor-card-'.$author->id;
                @endphp
                <article class="author-card" aria-labelledby="{{ $cardId }}">
                    <div class="author-header">
                        <div class="author-avatar">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $author->name }} — {{ $isOrg ? 'organisation' : 'contributor' }} profile" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa {{ $isOrg ? 'fa-building' : 'fa-user' }} author-avatar-icon\'></i>';">
                            @else
                                <i class="fa {{ $isOrg ? 'fa-building' : 'fa-user' }} author-avatar-icon" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div class="author-info">
                            <h2 class="author-name" id="{{ $cardId }}">
                                <a href="{{ author_publications_url($author) }}" title="View profile and publications for {{ $author->name }}">
                                    @if(!empty($author->orcid))
                                        <span title="View {{ $author->name }}'s ORCID profile">
                                            {{ truncate($author->name, 25) }}
                                            <i class="fa fa-external-link-alt" style="font-size: 0.7em; margin-left: 3px;"></i>
                                        </span>
                                    @else
                                        {{ truncate($author->name, 25) }}
                                    @endif
                                </a>
                            </h2>
                            @if($author->user && $author->user->job_title)
                            <div class="author-title">
                                <i class="fa fa-briefcase me-1" style="font-size: 0.8em;"></i>
                                {{ truncate($author->user->job_title, 30) }}
                            </div>
                            @endif
                            @if($author->user && $author->user->organization_name)
                            <div class="author-organization">
                                <i class="fa fa-building me-1" style="font-size: 0.8em;"></i>
                                {{ truncate($author->user->organization_name, 30) }}
                            </div>
                            @endif
                            @if($author->user && $author->user->country && $author->user->country->name)
                            <div class="author-organization">
                                <i class="fa fa-map-marker-alt me-1" style="font-size: 0.8em;"></i>
                                {{ $author->user->country->name }}
                            </div>
                            @endif
                            @if($author->user && $author->user->lifetimeBadge && $author->user->lifetimeBadge->badgeType)
                            @php
                                $lb = $author->user->lifetimeBadge;
                                $bt = $lb->badgeType;
                                $acquiredAt = $lb->last_upgraded_at ?? $lb->created_at;
                            @endphp
                            <div class="author-badges">
                                <span class="author-badge"
                                      style="background-color: {{ $bt->badge_color ?? '#C0C0C0' }};"
                                      title="{{ $bt->name }} — {{ number_format((int) $lb->lifetime_contributions) }} lifetime contributions@if($acquiredAt) · Acquired {{ $acquiredAt->format('M j, Y') }}@endif">
                                    {{ participant_badge_emoji($bt->slug ?? null) }}
                                    {{ $bt->name }}
                                </span>
                            </div>
                            @endif
				</div>
				</div>
                    <div class="author-stats">
                        <span class="author-resources">
                            <i class="fa fa-book"></i>
                            @php
                                $publicationCount = (int) ($author->publications_count ?? 0);
                                $forumEngagementCount = (int) ($author->forum_engagement_total ?? 0);
                                $totalContributions = (int) ($author->total_contributions ?? ($publicationCount + $forumEngagementCount));
                            @endphp
                            {{ $totalContributions }} {{ $totalContributions == 1 ? 'Contribution' : 'Contributions' }}
                        </span>
                        <a href="{{ author_publications_url($author) }}" class="view-link" title="View all resources by {{ $author->name }}">
                            View profile <i class="fa fa-arrow-right ms-1" aria-hidden="true"></i>
			</a>
		</div>
	</article>
            </div>
@endforeach
</div>

        @if($authors->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    {{ $authors->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="no-results">
            <i class="fa fa-users"></i>
            <h3>No Contributors Found</h3>
            <p>No contributors match your search criteria.</p>
        </div>
        @endif
</div>
</div>
@endsection