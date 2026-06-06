@php
    $hide_search = true;
    // SEO Meta Tags for Forums Listing Page
    $pageTitle = 'Discussion Forums - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = 'Join public health discussion forums, share insights, ask questions, and collaborate with experts across Africa. Participate in health-related discussions and knowledge exchange.';
    $pageKeywords = 'discussion forums, public health forums, health discussions, Africa CDC forums, health experts, ' . (settings()->seo_keywords ?? '');
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = url('forums');
    $ogType = 'website';
@endphp

@extends('layouts.app')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "{{ $pageTitle }}",
    "description": "{{ strip_tags($pageDescription) }}",
    "url": "{{ $canonicalUrl }}",
    "mainEntity": {
        "@type": "ItemList",
        "itemListElement": [
            @if(isset($forums) && $forums->count() > 0)
                @php
                    $schemaForums = $forums instanceof \Illuminate\Pagination\AbstractPaginator
                        ? $forums->getCollection()->take(10)
                        : collect($forums)->take(10);
                @endphp
                @foreach($schemaForums as $index => $forum)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "item": {
                        "@type": "DiscussionForumPosting",
                        "headline": "{{ addslashes($forum->forum_title ?? 'Forum') }}",
                        "url": "{{ forum_thread_url($forum) }}",
                        "description": "{{ addslashes(Str::limit(strip_tags($forum->forum_description ?? ''), 200)) }}",
                        "author": {
                            "@type": "Person",
                            "name": "{{ addslashes($forum->user->name ?? 'Anonymous') }}"
                        },
                        "datePublished": "{{ $forum->created_at ? (is_string($forum->created_at) ? \Carbon\Carbon::parse($forum->created_at)->toIso8601String() : $forum->created_at->toIso8601String()) : '' }}",
                        "interactionStatistic": {
                            "@type": "InteractionCounter",
                            "interactionType": "https://schema.org/CommentAction",
                            "userInteractionCount": {{ $forum->total_comments ?? count($forum->comments ?? []) }}
                        }
                    }
                }@if(!$loop->last),@endif
                @endforeach
            @endif
        ]
    },
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
                "name": "Forums",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
}
</script>
@endsection

@section('styles')
<style>
.forums-wrapper {
    background: #f4f5f7;
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}


.forums-filters {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.search-bar {
    position: relative;
    margin-bottom: 1rem;
}

.search-bar input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 3rem;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-bar input:focus {
    border-color: var(--theme-color-primary, #119A48);
    outline: none;
}

.search-bar .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
}

.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 0.875rem;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
    color: white;
}

.forum-card {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
    position: relative;
}

.forum-card:hover {
    border-color: var(--theme-color-primary, #119A48);
}

.forum-header {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.forum-image {
    width: 180px;
    height: 180px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
}

.forum-content {
    flex: 1;
}

/* Author column: tall enough to read beside meta + actions */
.forum-meta-actions-wrap {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.forum-meta-actions-body {
    flex: 1;
    min-width: 0;
}

.forum-meta-actions-wrap .forum-meta {
    border-top: none;
    padding-top: 0.125rem;
}

.forum-author-avatar-large {
    width: 4.5rem;
    height: 4.5rem;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #e2e8f0;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.forum-author-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.forum-author-avatar-large > i {
    font-size: 2rem;
    color: var(--theme-color-primary, #119A48);
    opacity: 0.85;
}

.forum-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.forum-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.forum-title a:hover {
    color: var(--theme-color-primary, #119A48);
    text-decoration: none;
}

.forum-description {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.forum-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--theme-color-primary, #119A48);
}

.forum-meta .meta-item.forum-meta-author-text {
    align-items: flex-start;
    max-width: min(100%, 22rem);
}

.forum-meta-author-name {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.95rem;
    line-height: 1.25;
}

.forum-meta-author-title {
    display: block;
    font-size: 0.8125rem;
    font-weight: 400;
    color: #64748b;
    line-height: 1.35;
    margin-top: 0.15rem;
}

.forum-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin: 1rem 0;
}

.tag {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    background: #f1f5f9;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.forum-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

/* Comments Panel Styles */
.comments-panel {
    margin-top: 1rem;
    border-top: 1px solid #e2e8f0;
    padding-top: 1rem;
}

.comments-toggle-inline {
    transition: opacity 0.2s ease;
}

.comments-toggle-inline:hover {
    opacity: 0.8;
}

.comments-toggle-inline i {
    transition: transform 0.3s ease;
}

.comments-toggle-inline.collapsed i.fa-comments {
    /* Icon stays normal when collapsed */
}

.like-forum-btn {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.like-forum-btn:hover {
    opacity: 0.8;
    transform: scale(1.1);
}

.like-forum-btn i {
    transition: color 0.2s ease;
}

.comments-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.comment-item-mini {
    display: flex;
    gap: 0.75rem;
    padding: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.comment-item-mini:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.comment-avatar-mini {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    flex-shrink: 0;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 0.875rem;
    overflow: hidden;
}

.comment-avatar-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.comment-content-mini {
    flex: 1;
    min-width: 0;
}

.comment-author-mini {
    font-weight: 600;
    font-size: 0.875rem;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.comment-text-mini {
    font-size: 0.875rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0.25rem;
    word-wrap: break-word;
}

.comment-time-mini {
    font-size: 0.75rem;
    color: #94a3b8;
}

.inline-comment-form {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

.inline-comment-form textarea {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 0.875rem;
    resize: vertical;
    font-family: inherit;
}

.inline-comment-form textarea:focus {
    outline: none;
    border-color: var(--theme-color-primary, #119A48);
}

.inline-comment-field {
    width: 100%;
}

.inline-comment-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
    justify-content: flex-end;
}

.inline-forum-upload-widget {
    margin-top: 0.75rem;
}

.inline-comment-form .file-upload-area {
    margin-top: 0.5rem;
    padding: 0.65rem;
    border: 2px dashed #e2e8f0;
    border-radius: 4px;
    background: #fff;
    text-align: center;
    transition: all 0.2s ease;
}

.inline-comment-form .file-upload-area:hover {
    border-color: var(--theme-color-primary, #119A48);
    background: rgba(17, 154, 72, 0.03);
}

.inline-comment-form .file-upload-area.dragover {
    border-color: var(--theme-color-primary, #119A48);
    background: rgba(17, 154, 72, 0.06);
}

.inline-comment-form .file-upload-text {
    color: #64748b;
    font-size: 0.8125rem;
    margin-bottom: 0.35rem;
}

.inline-comment-form .file-upload-hint {
    color: #94a3b8;
    font-size: 0.7rem;
}

.inline-comment-form .file-preview {
    margin-top: 0.65rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.inline-comment-form .file-preview-item {
    position: relative;
    display: inline-block;
}

.inline-comment-form .file-preview-img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
}

.inline-comment-form .file-preview-remove {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ef4444;
    color: white;
    border: 2px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: bold;
    line-height: 1;
}

.no-comments {
    text-align: center;
    padding: 1.5rem;
    color: #94a3b8;
    font-size: 0.875rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border: 1px solid #e2e8f0;
}

.empty-icon {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    font-weight: 600;
    font-size: 0.875rem;
    color: #64748b;
    border: 1px solid #e2e8f0;
}

@media (max-width: 768px) {
    .custom-bg h1 {
        font-size: 1.75rem;
    }
    
    .custom-bg p {
        font-size: 0.9rem;
    }

    .forum-header {
        flex-direction: column;
    }

    .forum-image {
        width: 100%;
        height: 180px;
    }

    .forum-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
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
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        <i class="fa fa-comments me-2"></i>Discussions & Forums
                    </h1>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        Join conversations, share knowledge, and collaborate with the community
                    </p>
                </div>
                            </div>
                        </div>
                    </div>
                </div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="forums-wrapper">
    <div class="container" style="max-width: 1200px;">
                <div class="row">
            <!-- Main Content -->
            <div class="col-lg-12 col-md-12">
                <!-- Filters -->
                <div class="forums-filters">
                    <div class="search-bar">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="forum-search" placeholder="Search discussions by title, description, or tags...">
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Discussions</button>
                        <button class="filter-btn" data-filter="joined">My Discussions</button>
                        <button class="filter-btn" data-filter="recent">Most Recent</button>
                        <button class="filter-btn" data-filter="popular">Most Active</button>
                    </div>
                </div>

                <!-- Forums List -->
                <div id="forums-list">
            @forelse($forums as $forum)
                <div class="forum-card" 
                     data-forum-id="{{ $forum->id }}"
                     data-joined="{{ in_array($forum->id, $my_forums) ? 'true' : 'false' }}"
                     data-comments="{{ $forum->total_comments ?? count($forum->comments) }}"
                     data-date="{{ $forum->created_at }}">
                    <div class="forum-header">
                        @if($forum->forum_image)
                        <img src="{{ $forum->forum_image }}" alt="{{ $forum->forum_title }} - Forum Discussion" class="forum-image" loading="lazy">
                        @else
                        <div class="forum-image" style="background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 2rem; border: 1px solid #e2e8f0;">
                            <i class="fa fa-comments"></i>
                        </div>
                        @endif

                        <div class="forum-content">
                            @php
                                $authorPhotoUrl = null;
                                if ($forum->user && !empty($forum->user->photo)) {
                                    $authorPhotoUrl = $forum->user->photo;
                                    $baseUrl = url('/');
                                    if (strpos($authorPhotoUrl, 'http://') === 0 || strpos($authorPhotoUrl, 'https://') === 0) {
                                        // full URL
                                    } elseif (strpos($authorPhotoUrl, $baseUrl) !== false) {
                                        // already absolute
                                    } elseif (strpos($authorPhotoUrl, '/storage/') === 0) {
                                        $authorPhotoUrl = $baseUrl . $authorPhotoUrl;
                                    } elseif (strpos($authorPhotoUrl, 'storage/') === 0) {
                                        $authorPhotoUrl = $baseUrl . '/' . $authorPhotoUrl;
                                    }
                                }
                            @endphp
                            <h2 class="forum-title" itemprop="headline">
                                <a href="{{ forum_thread_url($forum)}}">{!! $forum->forum_title !!}</a>
                            </h2>
                            <p class="forum-description">
                                @php
                                    // Limit to 80 words first
                                    $limitedDescription = Str::words(strip_tags($forum->forum_description), 80, '...');
                                    // Then process for video links and URLs
                                    $processedDescription = detect_and_embed_video_links($limitedDescription, 180, 180);
                                @endphp
                                {!! $processedDescription !!}
                            </p>

                            @if(count($forum->tags) > 0)
                            <div class="forum-tags">
                                    @foreach($forum->tags as $tag)
                                <span class="tag">#{{ $tag->tag }}</span>
                                    @endforeach
                            </div>
                            @endif

                            @php
                                $totalComments = $forum->total_comments ?? count($forum->comments);
                            @endphp
                            <div class="forum-meta-actions-wrap">
                                <div class="forum-author-avatar-large">
                                    @if($authorPhotoUrl)
                                        <img src="{{ $authorPhotoUrl }}" alt="{{ $forum->user->name ?? 'User' }}"
                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa fa-user\' aria-hidden=\'true\'></i>';">
                                    @else
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <div class="forum-meta-actions-body">
                            <div class="forum-meta">
                                <div class="meta-item forum-meta-author-text">
                                    <div>
                                        <span class="forum-meta-author-name">{{ $forum->user->name ?? 'Unknown' }}</span>
                                        @if($forum->user && trim((string) ($forum->user->job_title ?? '')) !== '')
                                            <span class="forum-meta-author-title">{{ $forum->user->job_title }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <i class="fa fa-clock"></i>
                                    <span>{{ time_ago($forum->created_at) }}</span>
                                </div>
                                @if($totalComments > 0)
                                <div class="meta-item comments-toggle-inline collapsed" 
                                     data-forum-id="{{ $forum->id }}" 
                                     onclick="toggleComments({{ $forum->id }})"
                                     style="cursor: pointer;">
                                    <i class="fa fa-comments"></i>
                                    <span>{{ $totalComments }} {{ $totalComments === 1 ? 'Comment' : 'Comments' }}</span>
                                </div>
                                @endif
                                @php
                                    $totalLikes = $forum->total_likes ?? count($forum->likes);
                                    $isLiked = auth()->check() && $forum->isLikedBy(auth()->id());
                                    $totalViews = isset($forum->views) ? (int)$forum->views : 0;
                                @endphp
                                <div class="meta-item like-forum-btn" 
                                     data-forum-id="{{ $forum->id }}"
                                     onclick="likeForum({{ $forum->id }})"
                                     style="cursor: pointer; {{ $isLiked ? 'color: #ef4444;' : '' }}">
                                    <i class="fa {{ $isLiked ? 'fa-heart' : 'fa-heart-o' }}" style="color: {{ $isLiked ? '#ef4444' : 'inherit' }};"></i>
                                    <span class="like-count-{{ $forum->id }}">{{ $totalLikes }}</span>
                                    <span>{{ $totalLikes === 1 ? ' like' : ' likes' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fa fa-eye"></i>
                                    <span>{{ $totalViews }} {{ $totalViews === 1 ? 'view' : 'views' }}</span>
                                </div>
                            </div>

                            <div class="forum-actions">
                                <a href="{{ forum_thread_url($forum)}}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-info-circle"></i> Details
                                </a>
                                @auth
                                    @if(in_array($forum->id, $my_forums))
                                        <a href="{{ forum_thread_url($forum)}}" class="btn btn-sm theme-bg text-white">
                                            <i class="fa fa-comments"></i> View Discussion
                                        </a>
                                        <button type="button" class="btn btn-sm theme-bg text-white" 
                                                onclick="showInlineCommentForm({{ $forum->id }})"
                                                id="show-comment-btn-{{ $forum->id }}">
                                            <i class="fa fa-plus-circle me-1"></i> Add Comment
                                        </button>
                                    @else
                                        <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm btn-dark" id="join{{ $forum->id }}">
                                            <i class="fa fa-link"></i> Join Discussion
                                        </a>
                                        <button type="button" class="btn btn-sm theme-bg text-white" 
                                                onclick="showInlineCommentJoinPanel({{ $forum->id }})"
                                                id="show-comment-btn-join-{{ $forum->id }}">
                                            <i class="fa fa-plus-circle me-1"></i> Add Comment
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm btn-dark" id="join{{ $forum->id }}">
                                        <i class="fa fa-link"></i> Join Discussion
                                    </a>
                                @endauth
                            </div>
                                </div>
                            </div>

                            @php
                                $forumComments = $forum->comments->take(5);
                                $hasMoreComments = ($totalComments ?? 0) > 5;
                                $commentsToShow = max(2, min(5, min($totalComments ?? 0, 5)));
                            @endphp

                            @if($totalComments > 0 || auth()->check())
                            <div class="comments-panel">
                                <div class="comments-list" id="comments-list-{{ $forum->id }}" style="display: none;">
                                    @if($totalComments > 0)
                                        @foreach($forumComments->take($commentsToShow) as $comment)
                                            <div class="comment-item-mini">
                                                <div class="comment-avatar-mini">
                                                    @if($comment->user && $comment->user->photo)
                                                        @php
                                                            $photoUrl = $comment->user->photo;
                                                            $baseUrl = url('/');
                                                            if (strpos($photoUrl, 'http://') === 0 || strpos($photoUrl, 'https://') === 0) {
                                                                // Already a full URL
                                                            } elseif (strpos($photoUrl, $baseUrl) !== false) {
                                                                // Already contains base URL
                                                            } elseif (strpos($photoUrl, '/storage/') === 0) {
                                                                $photoUrl = $baseUrl . $photoUrl;
                                                            } elseif (strpos($photoUrl, 'storage/') === 0) {
                                                                $photoUrl = $baseUrl . '/' . $photoUrl;
                                                            }
                                                        @endphp
                                                        <img src="{{ $photoUrl }}" alt="Avatar for {{ $comment->user->name ?? 'User' }}" 
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa fa-user\'></i>';">
                                                    @else
                                                        <i class="fa fa-user"></i>
                                                    @endif
                                                </div>
                                                <div class="comment-content-mini">
                                                    <div class="comment-author-mini">{{ $comment->user->name ?? 'Unknown' }}</div>
                                                    <div class="comment-text-mini">{!! Str::limit(strip_tags($comment->comment ?? ''), 150) !!}</div>
                                                    <div class="comment-time-mini">
                                                        <i class="fa fa-clock me-1"></i>{{ time_ago($comment->created_at ?? now()) }}
                                                        @if($comment->likes && count($comment->likes) > 0)
                                                            <span class="ms-2">
                                                                <i class="fa fa-heart text-danger me-1"></i>{{ count($comment->likes) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="no-comments">No comments yet. Be the first to comment!</div>
                                    @endif
                                </div>

                                @auth
                                @if(in_array($forum->id, $my_forums))
                                <div class="inline-comment-form" id="comment-form-{{ $forum->id }}" style="display: none;">
                                    <form onsubmit="submitInlineComment(event, {{ $forum->id }})" enctype="multipart/form-data" method="post" action="{{ url('forums/comment') }}">
                                        <div class="inline-comment-field">
                                            <textarea name="comment" id="inline-comment-{{ $forum->id }}"
                                                      class="comment-textarea"
                                                      placeholder="Add a comment..." required maxlength="20000" rows="3"></textarea>
                                            <div class="comment-char-count" style="font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem;">
                                                <span class="char-count">0</span> / 300 words max
                                            </div>
                                            <div class="inline-forum-upload-widget" data-forum-id="{{ $forum->id }}">
                                                <div class="file-upload-area inline-file-upload-area" style="cursor: pointer;">
                                                    <div class="file-upload-text">
                                                        <i class="fa fa-paperclip me-1"></i>
                                                        <span>Attach images, PDF, office, audio, or video (max 2MB per file)</span>
                                                    </div>
                                                    <div class="file-upload-hint">Images · PDF · Word/Excel/PowerPoint (saved as PDF) · Audio · Video</div>
                                                    <input type="file" name="attachments[]" class="inline-forum-attachments-input" multiple
                                                           accept="image/jpeg,image/png,image/gif,image/webp,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.odp,.rtf,audio/*,video/*,.mp3,.m4a,.wav,.aac,.ogg,.oga,.opus,.flac,.wma,.mp4,.webm,.mov,.avi,.mkv,.wmv,.flv,.3gp,.mpeg,.mpg"
                                                           style="display: none;">
                                                </div>
                                                <div class="file-preview inline-forum-file-preview"></div>
                                            </div>
                                        </div>
                                        <div class="inline-comment-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="cancelInlineComment({{ $forum->id }})">Cancel</button>
                                            <button type="submit" class="btn btn-sm theme-bg text-white">
                                                <i class="fa fa-paper-plane me-1"></i>Post Comment
                                            </button>
                                        </div>
                                        @csrf
                                    </form>
                                </div>
                                @else
                                <div class="inline-comment-form" id="comment-form-join-{{ $forum->id }}" style="display: none;">
                                    <p class="mb-2 text-muted small">Join this discussion to post a comment from the listing.</p>
                                    <a href="{{ url('forums/join') }}?id={{ $forum->id }}" class="btn btn-sm theme-bg text-white">
                                        <i class="fa fa-link me-1"></i>Join discussion
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="cancelInlineCommentJoin({{ $forum->id }})">Cancel</button>
                                </div>
                                @endif
                                @endauth
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa fa-comments"></i>
                    </div>
                    <h3>No Discussions Yet</h3>
                    <p class="text-muted">Be the first to start a discussion!</p>
                    @auth
                    <a href="{{ url('forums/create') }}" class="btn btn-sm theme-bg text-white mt-3">
                        <i class="fa fa-plus-circle me-2"></i>Start First Discussion
                    </a>
                    @endauth
                </div>
            @endforelse
                </div>

                <!-- Pagination -->
                @if($forums->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $forums->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            {{-- <div class="col-lg-4 col-md-12">
                @php 
                    $primary = settings()->primary_color ?? '#119A48';
                    $textColor = settings()->links_active_color ?? settings()->primary_text_color ?? $primary;
                @endphp
                <style>
                    .sidebar-card{background:#fff;border:1px solid #e2e8f0;border-radius:0.25rem;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-bottom:20px}
                    .sidebar-card-title{margin-bottom:15px;font-size:15px;font-weight:600;color:#2d3748}
                    .sidebar-item{margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #e2e8f0}
                    .sidebar-item:last-child{margin-bottom:0;padding-bottom:0;border-bottom:none}
                    .sidebar-item-title{font-size:0.9rem;font-weight:600;color:#0f172a;line-height:1.4;margin-bottom:8px}
                    .sidebar-item-title a{color:{{ $textColor }};text-decoration:none;transition:color 0.2s}
                    .sidebar-item-title a:hover{color:var(--theme-color-primary, {{ $primary }})}
                    .sidebar-item-meta{font-size:0.75rem;color:#94a3b8;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
                    .sidebar-item-meta i{color:var(--theme-color-primary, {{ $primary }})}
                    .sidebar-card .btn-outline-primary{color:var(--theme-color-primary, {{ $primary }});border-color:var(--theme-color-primary, {{ $primary }});width:100%}
                    .sidebar-card .btn-outline-primary:hover{background:var(--theme-color-primary, {{ $primary }});color:#fff}
                </style>

                <!-- Related Forums -->
                @if(isset($relatedForums) && $relatedForums->count() > 0)
                <div class="sidebar-card">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-comments me-2" style="color: {{ $primary }};"></i>Related Discussions
                    </h5>
                    @foreach($relatedForums as $forum)
                    <div class="sidebar-item">
                        <div class="sidebar-item-title">
                            <a href="{{ forum_thread_url($forum) }}">
                                {{ Str::limit(strip_tags($forum->forum_title), 80) }}
                            </a>
                        </div>
                        <div class="sidebar-item-meta">
                            @if($forum->user)
                            <span><i class="fa fa-user"></i> {{ $forum->user->name ?? 'Anonymous' }}</span>
                            @endif
                            <span><i class="fa fa-comments"></i> {{ $forum->total_comments ?? 0 }} {{ ($forum->total_comments ?? 0) == 1 ? 'comment' : 'comments' }}</span>
                            @if($forum->created_at)
                            <span><i class="fa fa-clock"></i> {{ $forum->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ url('forums') }}" class="btn btn-sm btn-outline-primary mt-2">View All Forums</a>
                </div>
                @endif

                <!-- Related Publications -->
                @if(isset($relatedPublications) && $relatedPublications->count() > 0)
                <div class="sidebar-card">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-file-text me-2" style="color: {{ $primary }};"></i>Related Publications
                    </h5>
                    @foreach($relatedPublications as $publication)
                    <div class="sidebar-item">
                        <div class="sidebar-item-title">
                            <a href="{{ publication_url($publication)}}">
                                {{ Str::limit(strip_tags($publication->title), 80) }}
                            </a>
                        </div>
                        <div class="sidebar-item-meta">
                            @if($publication->author)
                            <span><i class="fa fa-user"></i> {{ $publication->author->name ?? 'Unknown' }}</span>
                            @endif
                            <span><i class="fa fa-eye"></i> {{ $publication->visits ?? 0 }} {{ ($publication->visits ?? 0) == 1 ? 'view' : 'views' }}</span>
                            @if($publication->updated_at)
                            <span><i class="fa fa-clock"></i> {{ $publication->updated_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ url('records') }}" class="btn btn-sm btn-outline-primary mt-2">View All Publications</a>
            </div>
                @endif

                <!-- Related Communities -->
                @if(isset($relatedCommunities) && $relatedCommunities->count() > 0)
                <div class="sidebar-card">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-users me-2" style="color: {{ $primary }};"></i>Related Communities
                    </h5>
                    @foreach($relatedCommunities as $community)
                    <div class="sidebar-item">
                        <div class="sidebar-item-title">
                            <a href="{{ community_detail_url($community) }}">
                                {{ Str::limit(strip_tags($community->community_name), 80) }}
                            </a>
                        </div>
                        <div class="sidebar-item-meta">
                            <span><i class="fa fa-users"></i> {{ $community->members_count ?? 0 }} {{ ($community->members_count ?? 0) == 1 ? 'member' : 'members' }}</span>
                            <span><i class="fa fa-comments"></i> {{ $community->forums_count ?? 0 }} {{ ($community->forums_count ?? 0) == 1 ? 'forum' : 'forums' }}</span>
                            @if($community->created_at)
                            <span><i class="fa fa-clock"></i> {{ $community->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    <a href="{{ route('community.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All Communities</a>
                </div>
                @endif
            </div> --}}
        </div>
    </div>
</div>

<script>
// Image Modal for full view (if not already defined)
if (typeof openImageModal === 'undefined') {
    function openImageModal(imageUrl, imageAlt) {
        let modal = document.getElementById('imageViewModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'imageViewModal';
            modal.className = 'image-view-modal';
            modal.innerHTML = `
                <div class="image-view-modal-overlay" onclick="closeImageModal()"></div>
                <div class="image-view-modal-content">
                    <button class="image-view-modal-close" onclick="closeImageModal()">&times;</button>
                    <img id="imageViewModalImg" src="" alt="" />
                </div>
            `;
            document.body.appendChild(modal);
            
            // Add styles if not already present
            if (!document.getElementById('imageModalStyles')) {
                const style = document.createElement('style');
                style.id = 'imageModalStyles';
                style.textContent = `
                    .image-view-modal {
                        display: none;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 9999;
                        align-items: center;
                        justify-content: center;
                        background: rgba(0, 0, 0, 0.9);
                    }
                    .image-view-modal-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        cursor: pointer;
                    }
                    .image-view-modal-content {
                        position: relative;
                        max-width: 90%;
                        max-height: 90%;
                        z-index: 10000;
                        text-align: center;
                    }
                    .image-view-modal-content img {
                        max-width: 100%;
                        max-height: 90vh;
                        border-radius: 8px;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
                    }
                    .image-view-modal-close {
                        position: absolute;
                        top: -40px;
                        right: 0;
                        background: rgba(255, 255, 255, 0.9);
                        border: none;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        font-size: 24px;
                        cursor: pointer;
                        color: #2d3748;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s ease;
                        z-index: 10001;
                    }
                    .image-view-modal-close:hover {
                        background: #ffffff;
                        transform: scale(1.1);
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        document.getElementById('imageViewModalImg').src = imageUrl;
        document.getElementById('imageViewModalImg').alt = imageAlt || 'Image';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageViewModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
    
    window.openImageModal = openImageModal;
    window.closeImageModal = closeImageModal;
}

// Word / character limits for inline “Add comment” on listing (same rules as thread page)
function updateForumListingCommentCharCount(textarea) {
    const maxWords = 300;
    const maxChars = 20000;
    const text = textarea.value.trim();
    const words = text.split(/\s+/).filter(function (word) { return word.length > 0; });
    const wordCount = words.length;
    const charCount = text.length;

    const parent = textarea.parentElement;
    const charCountSpan = parent ? parent.querySelector('.comment-char-count .char-count') : null;
    if (charCountSpan) {
        charCountSpan.textContent = wordCount;
        const charCountDiv = parent.querySelector('.comment-char-count');
        if (charCountDiv) {
            if (wordCount >= maxWords || charCount >= maxChars) {
                charCountDiv.style.color = '#ef4444';
            } else if (wordCount >= maxWords * 0.8 || charCount >= maxChars * 0.8) {
                charCountDiv.style.color = '#f59e0b';
            } else {
                charCountDiv.style.color = '#94a3b8';
            }
        }
    }

    if (wordCount > maxWords) {
        textarea.value = words.slice(0, maxWords).join(' ');
        updateForumListingCommentCharCount(textarea);
        return;
    }

    if (charCount > maxChars) {
        textarea.value = text.substring(0, maxChars);
        updateForumListingCommentCharCount(textarea);
    }
}

function initForumListingInlineCommentCounters() {
    document.querySelectorAll('.inline-comment-form textarea.comment-textarea').forEach(function (textarea) {
        if (textarea.dataset.forumListingCountBound) return;
        textarea.dataset.forumListingCountBound = '1';
        textarea.addEventListener('input', function () {
            updateForumListingCommentCharCount(this);
        });
        textarea.addEventListener('paste', function () {
            setTimeout(function () { updateForumListingCommentCharCount(textarea); }, 10);
        });
        updateForumListingCommentCharCount(textarea);
    });
}

(function () {
    var inlineForumUploadMaxBytes = 2 * 1024 * 1024;
    var inlineForumUploadAllowedTypes = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/pjpeg', 'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation',
        'application/rtf', 'text/rtf',
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm', 'video/x-ms-wmv', 'video/x-flv',
        'video/3gpp', 'video/mpeg', 'audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a', 'audio/m4a',
        'audio/wav', 'audio/x-wav', 'audio/aac', 'audio/ogg', 'audio/flac', 'audio/x-ms-wma', 'audio/webm'
    ];
    var inlineForumUploadDangerousTypes = [
        'application/x-msdownload', 'application/x-sh', 'application/x-executable',
        'application/x-msdos-program', 'application/javascript', 'application/x-php'
    ];

    window._inlineForumUploadState = window._inlineForumUploadState || {};

    function inlineForumAllowedExt(name) {
        var ext = (name.split('.').pop() || '').toLowerCase();
        return [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf',
            'mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv', 'wmv', 'flv', '3gp', '3gpp', 'mpeg', 'mpg',
            'mp3', 'm4a', 'wav', 'aac', 'ogg', 'oga', 'opus', 'flac', 'wma'
        ].indexOf(ext) !== -1;
    }

    function inlineForumTypeAllowed(file) {
        var t = file.type || '';
        if (inlineForumUploadAllowedTypes.indexOf(t) !== -1) {
            return true;
        }
        if (t.indexOf('video/') === 0 || t.indexOf('audio/') === 0) {
            return true;
        }
        if (t.indexOf('application/vnd') === 0) {
            return true;
        }
        if (t === 'application/msword' || t === 'application/rtf' || t === 'text/rtf') {
            return true;
        }
        return false;
    }

    function inlineForumDangerousExt(name) {
        var ext = (name.split('.').pop() || '').toLowerCase();
        return ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'apk', 'dll', 'sh', 'php',
            'asp', 'jsp', 'py', 'rb', 'pl', 'cgi', 'bin', 'msi', 'deb', 'rpm'].indexOf(ext) !== -1;
    }

    function inlineForumGetState(forumId) {
        var key = String(forumId);
        if (window._inlineForumUploadState[key]) {
            return window._inlineForumUploadState[key];
        }
        var widget = document.querySelector('.inline-forum-upload-widget[data-forum-id="' + key + '"]');
        if (!widget) {
            return null;
        }
        window._inlineForumUploadState[key] = {
            selectedFiles: [],
            input: widget.querySelector('.inline-forum-attachments-input'),
            preview: widget.querySelector('.inline-forum-file-preview'),
            area: widget.querySelector('.inline-file-upload-area')
        };
        return window._inlineForumUploadState[key];
    }

    function inlineForumSyncInput(forumId) {
        var st = inlineForumGetState(forumId);
        if (!st || !st.input) {
            return;
        }
        var dt = new DataTransfer();
        st.selectedFiles.forEach(function (f) {
            dt.items.add(f);
        });
        st.input.files = dt.files;
    }

    function inlineForumRemoveAt(forumId, index) {
        var st = inlineForumGetState(forumId);
        if (!st) {
            return;
        }
        st.selectedFiles.splice(index, 1);
        inlineForumRenderPreviews(forumId);
        inlineForumSyncInput(forumId);
    }

    function inlineForumRenderPreviews(forumId) {
        var st = inlineForumGetState(forumId);
        if (!st || !st.preview) {
            return;
        }
        st.preview.innerHTML = '';
        st.selectedFiles.forEach(function (file, idx) {
            var item = document.createElement('div');
            item.className = 'file-preview-item';

            if (file.type.indexOf('image/') === 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var dataUrl = e.target.result;
                    var img = document.createElement('img');
                    img.src = dataUrl;
                    img.className = 'file-preview-img';
                    img.alt = file.name;
                    img.addEventListener('click', function () {
                        if (typeof openImageModal === 'function') {
                            openImageModal(dataUrl, file.name);
                        }
                    });
                    var rm = document.createElement('span');
                    rm.className = 'file-preview-remove';
                    rm.textContent = '\u00d7';
                    rm.addEventListener('click', function (ev) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        inlineForumRemoveAt(forumId, idx);
                    });
                    item.appendChild(img);
                    item.appendChild(rm);
                };
                reader.readAsDataURL(file);
            } else {
                var icon = 'fa-file';
                if (file.type.indexOf('pdf') !== -1) {
                    icon = 'fa-file-pdf';
                } else if (file.type.indexOf('word') !== -1 || /\.(doc|docx)$/i.test(file.name)) {
                    icon = 'fa-file-word';
                } else if (file.type.indexOf('excel') !== -1 || file.type.indexOf('spreadsheet') !== -1 || /\.(xls|xlsx)$/i.test(file.name)) {
                    icon = 'fa-file-excel';
                } else if (file.type.indexOf('powerpoint') !== -1 || file.type.indexOf('presentation') !== -1 || /\.(ppt|pptx)$/i.test(file.name)) {
                    icon = 'fa-file-powerpoint';
                } else if (/\.(odt|ods|odp|rtf)$/i.test(file.name)) {
                    icon = 'fa-file-o';
                } else if (file.type.indexOf('audio') !== -1 || /\.(mp3|m4a|wav|aac|ogg|oga|opus|flac|wma)$/i.test(file.name)) {
                    icon = 'fa-file-audio';
                } else if (file.type.indexOf('video') !== -1 || /\.(mp4|m4v|mov|avi|webm|mkv|wmv|flv|3gp|3gpp|mpeg|mpg)$/i.test(file.name)) {
                    icon = 'fa-file-video';
                }
                var box = document.createElement('div');
                box.style.cssText = 'width:72px;height:72px;background:#f8f9fa;border:1px solid #e2e8f0;border-radius:4px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0.35rem;';
                var ic = document.createElement('i');
                ic.className = 'fa ' + icon;
                ic.style.cssText = 'font-size:1.35rem;color:#64748b;margin-bottom:0.2rem;';
                var lbl = document.createElement('span');
                lbl.style.cssText = 'font-size:0.6rem;color:#64748b;text-align:center;word-break:break-all;max-width:100%;';
                lbl.textContent = file.name.length > 12 ? file.name.substring(0, 12) + '\u2026' : file.name;
                box.appendChild(ic);
                box.appendChild(lbl);
                var rm = document.createElement('span');
                rm.className = 'file-preview-remove';
                rm.textContent = '\u00d7';
                rm.addEventListener('click', function (ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    inlineForumRemoveAt(forumId, idx);
                });
                item.appendChild(box);
                item.appendChild(rm);
            }

            st.preview.appendChild(item);
        });
    }

    function inlineForumHandleFiles(forumId, files) {
        var st = inlineForumGetState(forumId);
        if (!st) {
            return;
        }
        files.forEach(function (file) {
            if (file.size > inlineForumUploadMaxBytes) {
                alert(file.name + ' is too large. Maximum file size is 2MB.');
                return;
            }
            if (!inlineForumTypeAllowed(file) && !inlineForumAllowedExt(file.name)) {
                alert(file.name + ' is not allowed. Use images, PDF, Word/Excel/PowerPoint, or common audio/video formats.');
                return;
            }
            if (inlineForumUploadDangerousTypes.indexOf(file.type) !== -1 || inlineForumDangerousExt(file.name)) {
                alert(file.name + ' is not allowed for security reasons.');
                return;
            }
            st.selectedFiles.push(file);
        });
        inlineForumRenderPreviews(forumId);
        inlineForumSyncInput(forumId);
    }

    window.initForumListingInlineFileUploads = function () {
        document.querySelectorAll('.inline-forum-upload-widget').forEach(function (widget) {
            if (widget.dataset.inlineUploadBound) {
                return;
            }
            widget.dataset.inlineUploadBound = '1';
            var forumId = widget.dataset.forumId;
            var st = inlineForumGetState(forumId);
            if (!st || !st.area || !st.input || !st.preview) {
                return;
            }

            st.area.addEventListener('click', function (e) {
                if (e.target.closest('.file-preview-remove')) {
                    return;
                }
                if (!e.target.closest('input') && !e.target.closest('textarea')) {
                    e.preventDefault();
                    st.input.click();
                }
            });

            st.input.addEventListener('change', function () {
                if (this.files && this.files.length) {
                    inlineForumHandleFiles(forumId, Array.from(this.files));
                }
            });

            st.area.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                st.area.classList.add('dragover');
            });
            st.area.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                st.area.classList.remove('dragover');
            });
            st.area.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                st.area.classList.remove('dragover');
                var fl = e.dataTransfer && e.dataTransfer.files;
                if (fl && fl.length) {
                    inlineForumHandleFiles(forumId, Array.from(fl));
                }
            });
        });
    };

    window.clearInlineForumAttachments = function (forumId) {
        var key = String(forumId);
        var st = window._inlineForumUploadState[key];
        if (st) {
            st.selectedFiles = [];
            if (st.preview) {
                st.preview.innerHTML = '';
            }
            if (st.input) {
                st.input.value = '';
                try {
                    st.input.files = new DataTransfer().files;
                } catch (err) { /* ignore */ }
            }
        }
    };

    window.inlineForumValidateAttachmentsBeforeSubmit = function (forumId) {
        inlineForumSyncInput(forumId);
        var st = inlineForumGetState(forumId);
        if (!st || !st.selectedFiles.length) {
            return true;
        }
        for (var i = 0; i < st.selectedFiles.length; i++) {
            var f = st.selectedFiles[i];
            if (f.size > inlineForumUploadMaxBytes) {
                alert(f.name + ' exceeds the 2MB limit.');
                return false;
            }
            if (!inlineForumTypeAllowed(f) && !inlineForumAllowedExt(f.name)) {
                alert(f.name + ' is not allowed. Use images, PDF, Word/Excel/PowerPoint, or common audio/video formats.');
                return false;
            }
            if (inlineForumUploadDangerousTypes.indexOf(f.type) !== -1 || inlineForumDangerousExt(f.name)) {
                alert(f.name + ' is not allowed for security reasons.');
                return false;
            }
        }
        return true;
    };
})();

function toggleComments(forumId) {
    const inlineToggle = document.querySelector(`.comments-toggle-inline[data-forum-id="${forumId}"]`);
    const commentsList = document.getElementById(`comments-list-${forumId}`);
    
    if (commentsList) {
        const isCollapsed = commentsList.style.display === 'none' || !commentsList.style.display;
        if (isCollapsed) {
            commentsList.style.display = 'block';
            if (inlineToggle) inlineToggle.classList.remove('collapsed');
        } else {
            commentsList.style.display = 'none';
            if (inlineToggle) inlineToggle.classList.add('collapsed');
        }
    }
}

function likeForum(forumId) {
    @auth
    const likeBtn = document.querySelector(`.like-forum-btn[data-forum-id="${forumId}"]`);
    const icon = likeBtn ? likeBtn.querySelector('i') : null;
    const countSpan = likeBtn ? likeBtn.querySelector('.like-count-' + forumId) : null;
    
    if (!likeBtn || !icon || !countSpan) return;
    
    // Disable button during request
    likeBtn.style.pointerEvents = 'none';
    
    fetch('{{ url("forums/like") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ forum_id: forumId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        
        // Update icon
        if (data.liked) {
            icon.classList.remove('fa-heart-o');
            icon.classList.add('fa-heart');
            icon.style.color = '#ef4444';
            likeBtn.style.color = '#ef4444';
        } else {
            icon.classList.remove('fa-heart');
            icon.classList.add('fa-heart-o');
            icon.style.color = 'inherit';
            likeBtn.style.color = 'inherit';
        }
        
        // Update count
        countSpan.textContent = data.count || 0;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        likeBtn.style.pointerEvents = 'auto';
    });
    @else
    alert('Please login to like forums');
    @endauth
}

function showInlineCommentForm(forumId) {
    const form = document.getElementById(`comment-form-${forumId}`);
    const btn = document.getElementById(`show-comment-btn-${forumId}`);
    const joinPanel = document.getElementById(`comment-form-join-${forumId}`);
    const joinBtn = document.getElementById(`show-comment-btn-join-${forumId}`);
    if (joinPanel) joinPanel.style.display = 'none';
    if (joinBtn) joinBtn.style.display = '';

    if (form && btn) {
        form.style.display = 'block';
        btn.style.display = 'none';
        const ta = form.querySelector('textarea');
        if (ta) {
            ta.focus();
            updateForumListingCommentCharCount(ta);
        }
    }
}

function showInlineCommentJoinPanel(forumId) {
    const panel = document.getElementById(`comment-form-join-${forumId}`);
    const btn = document.getElementById(`show-comment-btn-join-${forumId}`);
    if (panel && btn) {
        panel.style.display = 'block';
        btn.style.display = 'none';
    }
}

function cancelInlineCommentJoin(forumId) {
    const panel = document.getElementById(`comment-form-join-${forumId}`);
    const btn = document.getElementById(`show-comment-btn-join-${forumId}`);
    if (panel && btn) {
        panel.style.display = 'none';
        btn.style.display = '';
    }
}

function cancelInlineComment(forumId) {
    const form = document.getElementById(`comment-form-${forumId}`);
    const btn = document.getElementById(`show-comment-btn-${forumId}`);
    const textarea = document.getElementById(`inline-comment-${forumId}`);
    
    if (form && btn) {
        form.style.display = 'none';
        btn.style.display = '';
        if (textarea) {
            textarea.value = '';
            updateForumListingCommentCharCount(textarea);
        }
        if (typeof clearInlineForumAttachments === 'function') {
            clearInlineForumAttachments(forumId);
        }
    }
}

function submitInlineComment(event, forumId) {
    event.preventDefault();
    
    const form = event.target;
    const textarea = form.querySelector('textarea');
    const commentText = textarea ? textarea.value.trim() : '';
    
    if (!commentText) {
        alert('Please enter a comment');
        return;
    }

    const wordCount = commentText.split(/\s+/).filter(function(w) { return w.length > 0; }).length;
    if (wordCount > 300) {
        alert('Comments are limited to 300 words.');
        return;
    }
    if (commentText.length > 20000) {
        alert('Comment is too long.');
        return;
    }

    if (typeof inlineForumValidateAttachmentsBeforeSubmit === 'function' &&
        !inlineForumValidateAttachmentsBeforeSubmit(forumId)) {
        return;
    }
    
    const formData = new FormData(form);
    formData.set('id', String(forumId));
    formData.set('comment', commentText);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Posting...';
    
    fetch('{{ url("forums/comment") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(function (response) {
        if (!response.ok) {
            return response.json().then(
                function (j) {
                    j._httpStatus = response.status;
                    return Promise.reject(j);
                },
                function () {
                    return Promise.reject({ error: 'Request failed.', _httpStatus: response.status });
                }
            );
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 200 || data.success) {
            window.location.reload();
        } else {
            alert(data.message || data.error || 'Error posting comment. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error('Error:', err);
        var msg = 'An error occurred. Please try again.';
        if (err && err.error) {
            msg = err.error;
        } else if (err && err.errors && err.errors.comment) {
            var c = err.errors.comment;
            msg = Array.isArray(c) ? c[0] : c;
        } else if (err && err.message) {
            msg = err.message;
        }
        alert(msg);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initForumListingInlineCommentCounters();
    if (typeof initForumListingInlineFileUploads === 'function') {
        initForumListingInlineFileUploads();
    }

    const searchInput = document.getElementById('forum-search');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const forumsList = document.getElementById('forums-list');
    const forumCards = forumsList ? Array.from(forumsList.querySelectorAll('.forum-card')) : [];

    function restoreDefaultOrder() {
        if (!forumsList || !window._forumsListInitialOrder) return;
        window._forumsListInitialOrder.forEach(function (node) {
            forumsList.appendChild(node);
        });
    }

    function sortCardsInDom(compareFn) {
        if (!forumsList || forumCards.length === 0) return;
        const sorted = forumCards.slice().sort(compareFn);
        sorted.forEach(function (card) {
            forumsList.appendChild(card);
        });
    }

    if (forumsList && forumCards.length) {
        window._forumsListInitialOrder = forumCards.slice();
    }

    function getActiveFilter() {
        const active = document.querySelector('.filter-btn.active');
        return active && active.dataset.filter ? active.dataset.filter : 'all';
    }

    // Search functionality (respects current filter: joined / recent / popular / all)
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            applyFilter(getActiveFilter());
        });
    }

    // Filter buttons
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });

    function cardMatchesSearch(card, searchTerm) {
        if (!searchTerm) return true;
        const titleEl = card.querySelector('.forum-title');
        const descEl = card.querySelector('.forum-description');
        const title = titleEl ? titleEl.textContent.toLowerCase() : '';
        const description = descEl ? descEl.textContent.toLowerCase() : '';
        const tags = Array.from(card.querySelectorAll('.tag')).map(t => t.textContent.toLowerCase()).join(' ');
        return title.includes(searchTerm) || description.includes(searchTerm) || tags.includes(searchTerm);
    }

    function applyFilter(filter) {
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

        restoreDefaultOrder();

        if (filter === 'recent') {
            sortCardsInDom(function (a, b) {
                const da = new Date(a.dataset.date || 0).getTime();
                const db = new Date(b.dataset.date || 0).getTime();
                return db - da;
            });
        } else if (filter === 'popular') {
            sortCardsInDom(function (a, b) {
                const ca = parseInt(a.dataset.comments, 10) || 0;
                const cb = parseInt(b.dataset.comments, 10) || 0;
                if (cb !== ca) return cb - ca;
                const da = new Date(a.dataset.date || 0).getTime();
                const db = new Date(b.dataset.date || 0).getTime();
                return db - da;
            });
        }

        forumCards.forEach(card => {
            let shouldShow = true;

            if (filter === 'joined') {
                shouldShow = card.dataset.joined === 'true';
            }

            if (shouldShow && searchTerm) {
                shouldShow = cardMatchesSearch(card, searchTerm);
            }

            card.style.display = shouldShow ? 'block' : 'none';
        });
    }
});
</script>
@endsection
