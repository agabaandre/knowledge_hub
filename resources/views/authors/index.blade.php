@php
    $hide_search = false;
@endphp

@extends('layouts.app')

@section('styles')
<style>
    .authors-wrapper {
        padding: 2rem 0;
    }

    .authors-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .authors-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .authors-header p {
        color: #64748b;
        font-size: 1rem;
        margin: 0;
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
        <div class="authors-header">
            <h2><i class="fa fa-users me-2"></i>Contributors</h2>
            <p>Browse our community of knowledge contributors</p>
</div>
					
        @if(isset($authors) && $authors->count() > 0)
        <div class="row">
@foreach($authors as $author)
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="author-card">
                    <div class="author-header">
                        <div class="author-avatar">
                            @if($author->user && $author->user->photo)
                                <img src="{{ $author->user->photo }}" alt="{{ $author->name }}" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa fa-user author-avatar-icon\'></i>';">
                            @else
                                <i class="fa fa-user author-avatar-icon"></i>
                            @endif
                        </div>
                        <div class="author-info">
                            <div class="author-name">
                                <a href="{{ url('authors/publications')}}?author={{$author->id}}">
                                    @if(!empty($author->orcid))
                                        <span title="View {{ $author->name }}'s ORCID profile">
                                            {{ truncate($author->name, 25) }}
                                            <i class="fa fa-external-link-alt" style="font-size: 0.7em; margin-left: 3px;"></i>
                                        </span>
                                    @else
                                        {{ truncate($author->name, 25) }}
                                    @endif
                                </a>
                            </div>
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
                            @if($author->user && $author->user->badges && $author->user->badges->count() > 0)
                            <div class="author-badges">
                                @foreach($author->user->badges->take(3) as $userBadge)
                                    <span class="author-badge" 
                                          style="background-color: {{ $userBadge->badgeType->badge_color ?? '#C0C0C0' }};"
                                          title="{{ $userBadge->badgeType->name }} - {{ Carbon\Carbon::create($userBadge->year, $userBadge->month, 1)->format('M Y') }}">
                                        @if($userBadge->badgeType->slug === 'silver')🥈
                                        @elseif($userBadge->badgeType->slug === 'gold')🥇
                                        @elseif($userBadge->badgeType->slug === 'platinum')💎
                                        @elseif($userBadge->badgeType->slug === 'diamond')💠
                                        @else🏅
                                        @endif
                                        {{ $userBadge->badgeType->name }}
                                    </span>
                                @endforeach
                                @if($author->user->badges->count() > 3)
                                    <span class="author-badge" style="background-color: #64748b;">
                                        +{{ $author->user->badges->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                            @endif
				</div>
				</div>
                    <div class="author-stats">
                        <span class="author-resources">
                            <i class="fa fa-book"></i>
                            {{ count($author->publications) }} {{ count($author->publications) == 1 ? 'Resource' : 'Resources' }}
                        </span>
                        <a href="{{ url('authors/publications')}}?author={{$author->id}}" class="view-link">
                            View Resources <i class="fa fa-arrow-right ms-1"></i>
			</a>
		</div>
	</div>
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