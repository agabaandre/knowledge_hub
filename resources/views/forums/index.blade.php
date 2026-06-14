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

.forum-popularity-rank {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.45rem;
    border-radius: 999px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 0.8125rem;
    font-weight: 800;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.35);
}

.forum-title {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 0.75rem;
    padding-right: 2.5rem;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.forum-engagement-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    background: #ecfdf5;
    border: 1px solid rgba(17, 154, 72, 0.2);
    color: #166534;
    font-size: 0.75rem;
    font-weight: 700;
    white-space: nowrap;
}

.forum-card__contributors {
    margin: 0.75rem 0 0.25rem;
}

.forums-layout-main {
    min-width: 0;
}

.forums-sidebar {
    position: sticky;
    top: 5.5rem;
    align-self: flex-start;
}

.forums-sidebar-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem 1.1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.forums-sidebar-card__title {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.35rem;
}

.forums-sidebar-card__hint {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0 0 0.75rem;
    line-height: 1.45;
}

.forums-sidebar-table-wrap {
    overflow-x: auto;
}

.forums-sidebar-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.forums-sidebar-table thead th {
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    font-weight: 700;
    padding: 0.35rem 0.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.forums-sidebar-table tbody td {
    padding: 0.45rem 0.25rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.forums-sidebar-table tbody tr:last-child td {
    border-bottom: none;
}

.forums-sidebar-table tbody tr.is-active {
    background: #f0fdf4;
}

.forums-sidebar-table__link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
}

.forums-sidebar-table__link:hover {
    color: var(--theme-color-primary, #119A48);
    text-decoration: underline;
}

.forums-sidebar-table__count {
    color: #64748b;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.forums-sidebar-clear {
    display: inline-block;
    margin-top: 0.65rem;
    color: var(--theme-color-primary, #119A48);
}

.forums-sidebar-ranked__item {
    display: flex;
    gap: 0.65rem;
    padding: 0.55rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.forums-sidebar-ranked__item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.forums-sidebar-ranked__num {
    flex-shrink: 0;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 0.75rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.forums-sidebar-ranked__body {
    min-width: 0;
}

.forums-sidebar-ranked__title {
    display: block;
    color: #0f172a;
    font-size: 0.8125rem;
    font-weight: 600;
    line-height: 1.35;
    text-decoration: none;
    margin-bottom: 0.15rem;
}

.forums-sidebar-ranked__title:hover {
    color: var(--theme-color-primary, #119A48);
}

.forums-sidebar-ranked__meta {
    display: block;
    font-size: 0.6875rem;
    color: #64748b;
}

.forums-sidebar-links li {
    margin-bottom: 0.45rem;
}

.forums-sidebar-links li:last-child {
    margin-bottom: 0;
}

.forums-sidebar-links a {
    color: #334155;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.forums-sidebar-links a:hover {
    color: var(--theme-color-primary, #119A48);
}

@media (max-width: 991.98px) {
    .forums-sidebar {
        position: static;
        margin-top: 1.5rem;
    }
}

/* Contributor carousel (shared with communities listing) */
.community-room-card__avatar-carousel {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    min-height: 2.5rem;
}
.community-room-card__avatar-nav {
    flex-shrink: 0;
    width: 1.65rem;
    height: 1.65rem;
    padding: 0;
    border: 1px solid #e1e4e8;
    border-radius: 50%;
    background: #fff;
    color: #6a737c;
    font-size: 0.65rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.community-room-card__avatar-nav:hover {
    color: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
}
.community-room-card__avatar-track {
    flex: 1;
    min-width: 0;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
}
.community-room-card__avatar-track::-webkit-scrollbar { display: none; }
.community-room-card__avatar-slides {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: max-content;
}
.community-room-card__avatar-wrap {
    position: relative;
    width: 2rem;
    height: 2rem;
    min-width: 2rem;
    border-radius: 50%;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
}
.community-room-card__avatar-wrap--linked { text-decoration: none; color: inherit; }
.community-room-card__avatar-wrap--online::after {
    content: '';
    position: absolute;
    right: 0;
    bottom: 0;
    width: 0.45rem;
    height: 0.45rem;
    border-radius: 50%;
    background: #22c55e;
    border: 2px solid #fff;
}
.community-room-card__avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.community-room-card__avatar-initials {
    font-size: 0.65rem;
    font-weight: 700;
    color: #475569;
}
.community-room-card__more-members {
    font-size: 0.75rem;
    color: #64748b;
    white-space: nowrap;
    padding: 0 0.25rem;
}

.forum-card:hover {
    border-color: var(--theme-color-primary, #119A48);
}

.forums-infinite-sentinel {
    height: 1px;
    width: 100%;
}

.forums-infinite-loader {
    color: #64748b;
    font-size: 0.875rem;
    padding: 0.5rem 0;
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

.forum-share-actions--inline {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    margin-left: auto;
    flex-wrap: wrap;
}

.forum-share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    color: #64748b;
    background: #fff;
    text-decoration: none;
    font-size: 0.875rem;
    padding: 0;
    cursor: pointer;
    transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease;
}

.forum-share-btn:hover {
    color: var(--theme-color-primary, #119A48);
    border-color: var(--theme-color-primary, #119A48);
    background: rgba(17, 154, 72, 0.05);
    text-decoration: none;
}

@media (max-width: 768px) {
    .forum-share-actions--inline {
        margin-left: 0;
        width: 100%;
        margin-top: 0.35rem;
    }
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

.khub-forums-ai-banner {
    background: #fff;
    border: 1px solid #dbe3ec;
    border-left: 4px solid var(--theme-color-primary, #119A48);
    margin-bottom: 1.25rem;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
}
.khub-forums-ai-banner-inner {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.15rem 1.25rem 0.9rem;
    flex-wrap: wrap;
}
.khub-forums-ai-banner-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--theme-color-primary, #119A48), color-mix(in srgb, var(--theme-color-primary, #119A48) 70%, #0ea5e9));
    color: #fff;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.khub-forums-ai-banner-copy {
    flex: 1;
    min-width: 220px;
}
.khub-forums-ai-banner-title {
    margin: 0 0 0.35rem;
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
}
.khub-forums-ai-banner-text {
    margin: 0;
    color: #475569;
    font-size: 0.9rem;
    line-height: 1.55;
    max-width: 760px;
}
.khub-forums-ai-banner-meta {
    margin: 0.55rem 0 0;
    font-size: 0.8rem;
    color: #64748b;
}
.khub-forums-ai-banner-actions {
    display: flex;
    align-items: center;
    margin-left: auto;
}
.khub-forums-ai-suggestions-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    padding: 0 1.25rem 1rem;
    border-top: 1px solid #eef2f6;
    padding-top: 0.75rem;
}
.khub-forums-ai-chip {
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    font-size: 0.78rem;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}
.khub-forums-ai-chip:hover {
    background: #fff;
    border-color: var(--theme-color-primary, #119A48);
    color: var(--theme-color-primary, #119A48);
}

.forums-nav-react {
    margin-bottom: 2rem;
}
.forums-nav-jumps {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    margin-bottom: 0.75rem;
}
.forums-nav-jump-btn {
    border: 1px solid #dbe3ec;
    background: #fff;
    color: #334155;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    cursor: pointer;
    transition: border-color 0.15s ease, color 0.15s ease, background 0.15s ease;
}
.forums-nav-jump-btn:hover {
    border-color: var(--theme-color-primary, #119A48);
    color: var(--theme-color-primary, #119A48);
    background: #f0fdf4;
}
.forums-nav-status {
    margin: 0.85rem 0 0;
    font-size: 0.8125rem;
    color: #64748b;
}
#khub-forums-ai-banner,
#forums-list {
    scroll-margin-top: 6rem;
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

    .khub-forums-ai-banner-inner {
        flex-direction: column;
    }
    .khub-forums-ai-banner-actions {
        margin-left: 0;
        width: 100%;
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
            <div class="col-lg-8 col-md-12 forums-layout-main">
                <!-- React navigation: search, filters, section jumps -->
                <div id="forums-nav-root"></div>
                @php
                    $forumsInfiniteScroll = (bool) ($forumsInfiniteScroll ?? false);
                    $loadedForumCount = ($forums instanceof \Illuminate\Pagination\AbstractPaginator)
                        ? (($forums->currentPage() - 1) * $forums->perPage()) + $forums->count()
                        : count($forums ?? []);
                    $pageForumIds = $forums instanceof \Illuminate\Pagination\AbstractPaginator
                        ? $forums->getCollection()->pluck('id')->values()->all()
                        : collect($forums ?? [])->pluck('id')->values()->all();
                @endphp
                <script type="application/json" id="forums-nav-config">{!! json_encode([
                    'totalForums' => $forums instanceof \Illuminate\Pagination\AbstractPaginator
                        ? $forums->count()
                        : count($forums ?? []),
                    'searchPlaceholder' => 'Search discussions by title, description, or tags...',
                    'searchDebounceMs' => 220,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

                @include('forums.partials.khub_ai_listing_banner')

                <!-- Forums List -->
                <div id="forums-list-wrap"
                     @if($forumsInfiniteScroll && $forums instanceof \Illuminate\Pagination\AbstractPaginator)
                     data-infinite-scroll="1"
                     data-current-page="{{ $forums->currentPage() }}"
                     data-last-page="{{ $forums->lastPage() }}"
                     data-total="{{ $forums->total() }}"
                     data-loaded="{{ $loadedForumCount }}"
                     @endif>
                <div id="forums-list">
            @if($forums->count() > 0)
                @include('forums.partials.forum_list_items', ['forums' => $forums, 'my_forums' => $my_forums ?? []])
            @else
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
            @endif
                </div>

                @if($forumsInfiniteScroll && $forums instanceof \Illuminate\Pagination\AbstractPaginator && $forums->total() > 0)
                <div class="forums-infinite-footer py-3 text-center" id="forums-infinite-footer">
                    <p class="text-muted small mb-2" id="forums-infinite-status">
                        Showing {{ number_format($loadedForumCount) }} of {{ number_format($forums->total()) }} discussions
                    </p>
                    @if($forums->hasMorePages())
                        <div id="forums-infinite-sentinel" class="forums-infinite-sentinel" aria-hidden="true"></div>
                        <div id="forums-infinite-loader" class="forums-infinite-loader d-none" aria-live="polite">
                            <i class="fa fa-spinner fa-spin me-1"></i>Loading more discussions…
                        </div>
                    @else
                        <p class="text-muted small mb-0" id="forums-infinite-complete">All discussions loaded</p>
                    @endif
                </div>
                @elseif($forums instanceof \Illuminate\Pagination\AbstractPaginator && $forums->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $forums->links('pagination::bootstrap-4') }}
                </div>
                @endif
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                @include('forums.partials.sidebar', [
                    'forumSidebarCategories' => $forumSidebarCategories ?? collect(),
                    'forumTopByEngagement' => $forumTopByEngagement ?? collect(),
                ])
            </div>
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

    function copyForumShareLink(url) {
        if (!url) return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function () {
                if (typeof showLobiboxNotification === 'function') {
                    showLobiboxNotification('success', 'Link copied to clipboard!');
                } else if (typeof Lobibox !== 'undefined') {
                    Lobibox.notify('success', { size: 'mini', msg: 'Link copied to clipboard!' });
                }
            }).catch(function () {
                fallbackCopyForumLink(url);
            });
        } else {
            fallbackCopyForumLink(url);
        }
    }

    function fallbackCopyForumLink(url) {
        const el = document.createElement('textarea');
        el.value = url;
        el.style.position = 'fixed';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        try {
            document.execCommand('copy');
            if (typeof showLobiboxNotification === 'function') {
                showLobiboxNotification('success', 'Link copied to clipboard!');
            } else if (typeof Lobibox !== 'undefined') {
                Lobibox.notify('success', { size: 'mini', msg: 'Link copied to clipboard!' });
            }
        } catch (err) {
            window.prompt('Copy this link:', url);
        } finally {
            document.body.removeChild(el);
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.copy-link-btn');
        if (!btn) return;
        e.preventDefault();
        const url = btn.getAttribute('data-share-url');
        if (typeof window.copyForumLink === 'function') {
            window.copyForumLink(url);
        } else {
            copyForumShareLink(url);
        }
    });
});
</script>

<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="{{ asset('js/forums-index-filters.js') }}?v={{ @filemtime(public_path('js/forums-index-filters.js')) }}"></script>
<script src="{{ asset('js/forums-index-nav.js') }}?v={{ @filemtime(public_path('js/forums-index-nav.js')) }}"></script>
<script>
    window.forumsInfiniteScrollConfig = {
        enabled: @json((bool) ($forumsInfiniteScroll ?? false)),
        pageUrl: @json(route('forums.page'))
    };
    window.FORUMS_INFINITE_STATUS_COMPLETE = 'All discussions loaded';
    window.FORUMS_INFINITE_STATUS_ERROR = 'Could not load more discussions. Tap to retry.';
</script>
<script src="{{ asset('js/forums-index-infinite.js') }}?v={{ @filemtime(public_path('js/forums-index-infinite.js')) }}"></script>
<script src="{{ asset('js/forums-index-contributors.js') }}?v={{ @filemtime(public_path('js/forums-index-contributors.js')) }}"></script>

@auth
@include('common.pdf-chat-modal')
<script>
    window.khubAiChat = { type: 'forums_index', forum_ids: @json($pageForumIds ?? []) };
    window.forumsIndexForumIds = @json($pageForumIds ?? []);
    var pdfChatDocumentTitle = 'Discussion forums';
</script>
@include('common.pdf-chat-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var openBtn = document.getElementById('btn-open-forums-khub-ai');
    if (openBtn && typeof window.openForumsListingAssistant === 'function') {
        openBtn.addEventListener('click', function () {
            window.openForumsListingAssistant(window.forumsIndexForumIds || [], 'Discussion forums');
        });
    }
    document.querySelectorAll('.js-forums-ai-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var prompt = chip.getAttribute('data-prompt') || '';
            if (!prompt) return;
            if (typeof window.openForumsListingAssistant === 'function') {
                window.openForumsListingAssistant(window.forumsIndexForumIds || [], 'Discussion forums', prompt);
            }
        });
    });
});
</script>
@endauth
@endsection
