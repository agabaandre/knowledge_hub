@php
    $hide_search = true;
    $siteName = settings()->site_name ?? 'Africa CDC Knowledge Hub';
    $forumAuthorName = $forum->user->name ?? 'Anonymous';
    $linkedCr = $linkedContentRequest ?? null;
    $isLinkedContentRequestForum = !empty($linkedCr);

    // SEO / hero: linked content-request threads must not expose requester details via meta or banner snippet
    if ($isLinkedContentRequestForum) {
        $pageDescription = 'Community discussion for a Knowledge Hub content request. Requester contact details are not shown on this page; the topic is described in the thread below.';
        $pageKeywords = 'community forum, content request, knowledge exchange, public health, ' . (settings()->seo_keywords ?? '');
        $schemaDescription = 'Community forum thread for a Knowledge Hub content request. The requester is not identified on this page.';
        $forumDescriptionShort = 'This thread is for your community to discuss a referred content request. Full topic details are in the first post below; the requester is not identified here.';
    } else {
        $pageDescription = Str::limit(strip_tags($forum->forum_description ?? ''), 160) ?: ($forum->forum_title . ' - Join the discussion on this public health forum topic.');
        $pageKeywords = 'forum discussion, ' . ($forum->forum_title ?? '') . ', ' . $forumAuthorName . ', public health, ' . (settings()->seo_keywords ?? '');
        $schemaDescription = Str::limit(strip_tags($forum->forum_description ?? ''), 300);
        $forumDescriptionPlain = strip_tags($forum->forum_description ?? '');
        $forumDescriptionShort = \Illuminate\Support\Str::words($forumDescriptionPlain, 40, '...');
    }

    $pageTitle = ($forum->forum_title ?? 'Forum Discussion') . ' - ' . $siteName;
    $pageAuthor = $forumAuthorName;

    // Use forum image if available, otherwise default
    $forumImage = null;
    if (!empty($forum->forum_image) && is_image($forum->forum_image)) {
        $forumImage = filter_var($forum->forum_image, FILTER_VALIDATE_URL) ? $forum->forum_image : asset($forum->forum_image);
    }
    $pageImage = $forumImage ?? settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = url('forums/thread?id=' . $forum->id);
    $ogType = 'article';

    // Article dates for OG
    $publishDate = $forum->created_at ? (is_string($forum->created_at) ? \Carbon\Carbon::parse($forum->created_at)->toIso8601String() : $forum->created_at->toIso8601String()) : now()->toIso8601String();
    $modifiedDate = $forum->updated_at ? (is_string($forum->updated_at) ? \Carbon\Carbon::parse($forum->updated_at)->toIso8601String() : $forum->updated_at->toIso8601String()) : $publishDate;
    $articlePublishedTime = $publishDate;
    $articleModifiedTime = $modifiedDate;

    // Get forum stats
    $commentCount = $forum->total_comments ?? count($forum->comments ?? []);
    $likeCount = $forum->total_likes ?? count($forum->likes ?? []);
    $authorProfileUrl = $forum->user ? url('account') : '';
@endphp

@extends('layouts.app')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "DiscussionForumPosting",
    "headline": "{{ addslashes($forum->forum_title ?? 'Forum Discussion') }}",
    "description": "{{ addslashes($schemaDescription) }}",
    "image": "{{ $pageImage }}",
    "datePublished": "{{ $publishDate }}",
    "dateModified": "{{ $modifiedDate }}",
    "author": {
        "@type": "Person",
        "name": "{{ addslashes($forum->user->name ?? 'Anonymous') }}"
        @if(!empty($authorProfileUrl))
        ,"url": "{{ $authorProfileUrl }}"
        @endif
    },
    "commentCount": {{ (int) ($forum->total_comments ?? count($forum->comments ?? [])) }},
    "publisher": {
        "@type": "Organization",
        "name": "{{ settings()->site_name ?? 'Africa CDC Knowledge Hub' }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ settings()->logo ?? asset('assets/images/logo.png') }}"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ $canonicalUrl }}"
    },
    "interactionStatistic": [
        {
            "@type": "InteractionCounter",
            "interactionType": "https://schema.org/CommentAction",
            "userInteractionCount": {{ $commentCount }}
        },
        {
            "@type": "InteractionCounter",
            "interactionType": "https://schema.org/LikeAction",
            "userInteractionCount": {{ $likeCount }}
        }
    ],
    @if($forum->tags && $forum->tags->count() > 0)
    "keywords": "{{ addslashes($forum->tags->pluck('tag')->filter()->implode(', ')) }}",
    @endif
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
                "item": "{{ url('forums') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ addslashes(Str::limit($forum->forum_title ?? 'Forum', 50)) }}",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
}
</script>
@endsection

@section('styles')
@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0e7a3a';
    $auGold = settings()->au_gold ?? '#B4A269';
    if (! isset($forumDescriptionShort)) {
        $forumDescriptionPlain = strip_tags($forum->forum_description ?? '');
        $forumDescriptionShort = \Illuminate\Support\Str::words($forumDescriptionPlain, 40, '...');
    }
    $bannerImage = is_image($forum->forum_image) ? $forum->forum_image : null;
    $gradientStart = settings()->gradient_start_color ?? '#119A48';
    $gradientEnd = settings()->gradient_end_color ?? '#16c653';
@endphp
<style>
    .forum-thread-wrapper {
        padding: 2rem 0;
        background: #f8f9fa;
    }

    /* Forum thread banner - use same background as forums index page */
    /* Removed forum-thread-custom-bg to use default custom-bg styling */

    @media (max-width: 768px) {
        .custom-bg h1 {
            font-size: 1.75rem;
        }
        
        .custom-bg p {
            font-size: 0.9rem;
        }
    }

    .forum-post-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .forum-header {
        margin-bottom: 1rem;
    }

    .forum-author-info {
        flex: 1;
    }

    .forum-author-name-container {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem 0.75rem;
        margin-bottom: 0.25rem;
    }

    /* Avatar size/layout: forums/partials/forum_details.blade.php (once block for author avatar) */
    .forum-thread-image {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .forum-thread-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .forum-author-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.05rem;
    }

    .forum-author-avatar-inline:hover {
        transform: scale(1.04);
        border-color: {{ $primaryColor }};
    }

    .forum-post-time {
        color: #64748b;
        font-size: 0.875rem;
    }

    .forum-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .forum-content {
        color: #4a5568;
        line-height: 1.7;
        margin-bottom: 1rem;
    }

    .forum-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }

    .forum-action-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        padding: 0.5rem;
        border-radius: 4px;
    }

    .forum-action-btn:hover {
        background: rgba(17, 154, 72, 0.05);
        color: {{ $primaryColor }};
        text-decoration: none;
    }

    .forum-action-btn i {
        font-size: 1rem;
    }

    /* Image View Modal */
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

    /* Twitter/Facebook Style Comments */
    .comments-section {
        margin-top: 2rem;
    }

    .comments-header {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .comment-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
    }

    .comment-item:last-child {
        margin-bottom: 0;
    }

    .comment-avatar {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .comment-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    .comment-avatar i {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #64748b;
        font-size: 1rem;
        z-index: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .comment-content {
        flex: 1;
        min-width: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .comment-content:hover {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e0;
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .comment-author {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.9375rem;
    }

    .comment-time {
        color: #94a3b8;
        font-size: 0.8125rem;
    }

    .comment-text {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 0.75rem;
        word-wrap: break-word;
    }

    .comment-attachments {
        margin-top: 0.75rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .comment-attachment {
        max-width: 100%;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .comment-attachments-row {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .comment-image-attachments {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .comment-attachment-img-wrapper {
        display: inline-block;
    }

    .comment-attachment-img {
        width: 88px;
        height: 88px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .comment-attachment-img:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .comment-file-attachments-summary {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
        min-width: 200px;
    }

    .comment-file-attachments-only {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .comment-attachment-file {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        color: #64748b;
        text-decoration: none;
        font-size: 0.8125rem;
        transition: all 0.2s ease;
        max-width: 250px;
    }

    .comment-attachment-file:hover {
        background: rgba(17, 154, 72, 0.05);
        border-color: {{ $primaryColor }};
        text-decoration: none;
        color: {{ $primaryColor }};
    }

    .comment-attachment-file span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .comment-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f1f5f9;
    }

    .comment-action-btn {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: #64748b;
        font-size: 0.8125rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        border: none;
        background: none;
    }

    .comment-action-btn:hover {
        background: rgba(17, 154, 72, 0.05);
        color: {{ $primaryColor }};
    }

    /* Comment Form - Twitter/Facebook Style */
    .comment-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .comment-form-header {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
    }

    .comment-input-wrapper {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .comment-input-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .comment-input-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        object-position: center center;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    .comment-input-avatar i {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #64748b;
        font-size: 1rem;
        z-index: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .comment-form-controls {
        flex: 1;
    }

    .comment-textarea {
        width: 100%;
        min-height: 60px;
        max-height: 120px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-size: 0.875rem;
        color: #2d3748;
        resize: vertical;
        font-family: inherit;
        transition: border-color 0.2s ease;
        line-height: 1.5;
    }

    .comment-textarea:focus {
        outline: none;
        border-color: {{ $primaryColor }};
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
    }

    .comment-textarea::placeholder {
        color: #94a3b8;
    }

    .comment-header-actions {
        display: inline-flex;
        gap: 0.5rem;
    }

    .comment-header-actions .comment-action-btn {
        transition: color 0.2s ease;
    }

    .comment-header-actions .comment-action-btn:hover {
        color: {{ $primaryColor }};
    }

    .comment-form-toggle:hover {
        background: #f1f5f9 !important;
    }

    .file-upload-area {
        margin-top: 0.75rem;
        padding: 0.75rem;
        border: 2px dashed #e2e8f0;
        border-radius: 4px;
        background: #f8f9fa;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-upload-area:hover {
        border-color: {{ $primaryColor }};
        background: rgba(17, 154, 72, 0.02);
    }

    .file-upload-area.dragover {
        border-color: {{ $primaryColor }};
        background: rgba(17, 154, 72, 0.05);
    }

    .file-upload-text {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .file-upload-hint {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .file-preview {
        margin-top: 0.75rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .file-preview-item {
        position: relative;
        display: inline-block;
    }

    .file-preview-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .file-preview-remove {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
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
    }

    .comment-form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .comment-submit-btn {
        background: {{ $primaryColor }};
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.9375rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .comment-submit-btn:hover {
        background: {{ $secondaryColor }};
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(17, 154, 72, 0.3);
    }

    .comment-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Sidebar Styling */
    .sidebar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .sidebar-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .forum-sidebar-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 4px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .forum-sidebar-item:hover {
        background: rgba(17, 154, 72, 0.05);
        border-color: {{ $primaryColor }};
        text-decoration: none;
        transform: translateX(4px);
    }

    .forum-sidebar-item:last-child {
        margin-bottom: 0;
    }

    .forum-sidebar-img {
        width: 60px;
        height: 60px;
        border-radius: 4px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid #e2e8f0;
    }

    .forum-sidebar-content {
        flex: 1;
        min-width: 0;
    }

    .forum-sidebar-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .forum-sidebar-time {
        font-size: 0.75rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .forum-sidebar-time i {
        color: {{ $auGold }};
    }

    .tag-item {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        background: rgba(180, 162, 105, 0.1);
        color: {{ $auGold }};
        border: 1px solid {{ $auGold }};
        border-radius: 4px;
        font-size: 0.8125rem;
        font-weight: 500;
        margin: 0.25rem 0.25rem 0.25rem 0;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .tag-item:hover {
        background: {{ $auGold }};
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .no-comments {
        text-align: center;
        padding: 3rem 2rem;
        color: #94a3b8;
    }

    .reply-comment {
        margin-left: 3rem;
    }

    .reply-comment .comment-content {
        background: #f8f9fa;
        border-color: #e2e8f0;
    }

    .reply-comment .comment-content:hover {
        background: #f1f5f9;
        border-color: #cbd5e0;
    }

    @media (max-width: 768px) {
        .forum-actions {
            gap: 1rem;
        }

        .comment-item {
            padding: 0.75rem 0;
        }

        .reply-comment {
            margin-left: 1rem;
        }

        .comment-input-wrapper {
            gap: 0.5rem;
        }
    }

    /* Dark mode: forum thread page */
    html[data-bs-theme="dark"] .forum-thread-wrapper { background: #1a1d21 !important; }
    html[data-bs-theme="dark"] .forum-post-card {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .forum-author-name { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-post-time { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .forum-title { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-content { color: #d1d5db !important; }
    html[data-bs-theme="dark"] .forum-actions { border-top-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-action-btn { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .forum-action-btn:hover { color: var(--theme-color-primary, #119A48) !important; background: rgba(17, 154, 72, 0.15) !important; }
    html[data-bs-theme="dark"] .forum-author-avatar-inline i { background: #2d3136 !important; color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comments-header { color: #e4e6eb !important; border-bottom-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .comment-item { border-color: transparent; }
    html[data-bs-theme="dark"] .comment-avatar { border-color: #3e4348 !important; background: #2d3136 !important; }
    html[data-bs-theme="dark"] .comment-avatar i { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comment-content {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .comment-content:hover { border-color: #4b5262 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.2) !important; }
    html[data-bs-theme="dark"] .comment-author { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .comment-time { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comment-text { color: #d1d5db !important; }
    html[data-bs-theme="dark"] .comment-actions { border-top-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .comment-action-btn { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comment-action-btn:hover { color: var(--theme-color-primary, #119A48) !important; background: rgba(17, 154, 72, 0.15) !important; }
    html[data-bs-theme="dark"] .comment-attachment-file {
        background: #2d3136 !important;
        border-color: #3e4348 !important;
        color: #d1d5db !important;
    }
    html[data-bs-theme="dark"] .comment-attachment-file:hover { border-color: var(--theme-color-primary, #119A48) !important; color: var(--theme-color-primary, #119A48) !important; background: rgba(17, 154, 72, 0.1) !important; }
    html[data-bs-theme="dark"] .comment-attachment-img { border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .comment-form-card {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .comment-form-header { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .comment-input-avatar { border-color: #3e4348 !important; background: #2d3136 !important; }
    html[data-bs-theme="dark"] .comment-input-avatar i { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comment-textarea {
        background: #2d3136 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb !important;
    }
    html[data-bs-theme="dark"] .comment-textarea::placeholder { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .comment-form-footer { border-top-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .file-upload-area {
        background: #2d3136 !important;
        border-color: #3e4348 !important;
    }
    html[data-bs-theme="dark"] .file-upload-area:hover { border-color: var(--theme-color-primary, #119A48) !important; background: rgba(17, 154, 72, 0.1) !important; }
    html[data-bs-theme="dark"] .file-upload-text,
    html[data-bs-theme="dark"] .file-upload-hint { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .file-preview-img { border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .sidebar-card {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .sidebar-card-title { color: #e4e6eb !important; border-bottom-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-sidebar-item { color: inherit; }
    html[data-bs-theme="dark"] .forum-sidebar-item:hover { background: rgba(17, 154, 72, 0.15) !important; border-color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .forum-sidebar-title { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-sidebar-time { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .forum-sidebar-img { border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .tag-item {
        background: rgba(180, 162, 105, 0.2) !important;
        border-color: {{ $auGold }};
        color: {{ $auGold }};
    }
    html[data-bs-theme="dark"] .tag-item:hover { background: {{ $auGold }} !important; color: #fff !important; }
    html[data-bs-theme="dark"] .no-comments { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .reply-comment .comment-content { background: #2d3136 !important; border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .reply-comment .comment-content:hover { background: #363b40 !important; border-color: #4b5262 !important; }
    html[data-bs-theme="dark"] .image-view-modal-close { background: #2d3136 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .image-view-modal-close:hover { background: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .card { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .card .text-muted,
    html[data-bs-theme="dark"] .forum-thread-wrapper .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .btn-outline-primary { border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .btn-outline-primary:hover { background: rgba(17, 154, 72, 0.2); border-color: var(--theme-color-primary); color: var(--theme-color-primary); }
    html[data-bs-theme="dark"] .forum-thread-wrapper .alert-info { background: #2d3136 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .comment-form-toggle { background: #2d3136 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .comment-form-toggle .comment-form-header,
    html[data-bs-theme="dark"] .comment-form-toggle h5 { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .comment-form-toggle .fa-chevron-down { color: #9ca3af !important; }
    html[data-bs-theme="dark"] #commentFormContainer { background: #242628 !important; border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .forum-author-avatar-inline { border-color: #3e4348 !important; background: #2d3136 !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper img[style*="background-color: #f1f5f9"] { background-color: #2d3136 !important; border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .reply-form-container { border-top-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .cancel-reply-btn { background: #3e4348 !important; border-color: #4b5262 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper .comment-char-count { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper h5[style*="color: #2d3748"] { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper p[style*="color: #64748b"] { color: #d1d5db !important; }
    html[data-bs-theme="dark"] .forum-thread-wrapper a[style*="color: #374151"] { color: #d1d5db !important; }
</style>
@endsection

@section('content')
{{-- Custom Cover Banner with Title and Description (matching forums index page exactly) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        {!! cleanHtmlContent($forum->forum_title) !!}
                    </h1>
                    @if($forumDescriptionShort)
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        @php
                            // Process short description to detect and embed video links and convert URLs to clickable links
                            $processedDescriptionShort = detect_and_embed_video_links($forumDescriptionShort, 180, 180);
                        @endphp
                        {!! $processedDescriptionShort !!}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="forum-thread-wrapper">
        <div class="container">
            <div class="row">
            <!-- Main Content -->
                <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                    @include('forums.partials.content_request_referral_banner', ['linkedContentRequest' => $linkedContentRequest ?? null])
                    @include('forums.partials.forum_details')
                </div>

            <!-- Sidebar -->
                <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                <!-- Recent Forums -->
                @if($forums && $forums->count() > 0)
                <div class="sidebar-card">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-comments me-2" style="color: {{ $primaryColor }};"></i>Recent Forums
                    </h5>
                    @foreach ($forums->take(5) as $other)
                    <a href="{{ url('forums/thread') }}?id={{ $other->id }}" class="forum-sidebar-item">
                        <div>
                                        @if (is_image($other->forum_image))
                                <img class="forum-sidebar-img" src="{{ $other->forum_image }}" alt="{{ $other->forum_title ?? 'Forum Discussion' }}" loading="lazy">
                            @else
                                <div class="forum-sidebar-img" style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }}); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                    <i class="fa fa-comments"></i>
                                </div>
                                        @endif
                        </div>
                        <div class="forum-sidebar-content">
                            <div class="forum-sidebar-title">{!! strip_tags($other->forum_title) !!}</div>
                            <div class="forum-sidebar-time">
                                <i class="fa fa-clock"></i>
                                            {{ time_ago($other->created_at) }}
                    </div>
                        </div>
                    </a>
                                @endforeach
                        </div>
                    @endif

                <!-- Tags -->
                @if ($forum->tags && count($forum->tags) > 0)
                <div class="sidebar-card">
                    <h5 class="sidebar-card-title">
                        <i class="fa fa-tags me-2" style="color: {{ $auGold }};"></i>Tags
                    </h5>
                    <div>
                        @foreach ($forum->tags as $tag)
                            <a href="{{ url('forums') }}?tag={{ $tag->tag }}" class="tag-item">{{ $tag->tag }}</a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

    @include('common.ai-summary')
    @include('common.attachment_js')
    
    {{-- Lobibox Notifications - Include before forum scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />
    
    {{-- Forum JavaScript - Moved here because @section('scripts') doesn't seem to execute --}}
    @include('forums.partials.scripts')
    
    <script>
        // Word count and character limit for comment textareas
        function updateCharCount(textarea) {
            const maxWords = 300;
            const maxChars = 20000;
            const text = textarea.value.trim();
            const words = text.split(/\s+/).filter(word => word.length > 0);
            const wordCount = words.length;
            const charCount = text.length;
            
            const charCountSpan = textarea.parentElement.querySelector('.comment-char-count .char-count');
            if (charCountSpan) {
                charCountSpan.textContent = wordCount;
                
                // Change color if approaching limit
                const charCountDiv = textarea.parentElement.querySelector('.comment-char-count');
                if (wordCount >= maxWords || charCount >= maxChars) {
                    charCountDiv.style.color = '#ef4444';
                } else if (wordCount >= maxWords * 0.8 || charCount >= maxChars * 0.8) {
                    charCountDiv.style.color = '#f59e0b';
                } else {
                    charCountDiv.style.color = '#94a3b8';
                }
            }
            
            // Limit words on paste/input
            if (wordCount > maxWords) {
                const truncatedText = words.slice(0, maxWords).join(' ');
                textarea.value = truncatedText;
                updateCharCount(textarea);
            }
            
            // Hard character ceiling (unicode-safe upper bound for 300 words)
            if (charCount > maxChars) {
                textarea.value = text.substring(0, maxChars);
                updateCharCount(textarea);
            }
        }
        
        // Apply to all comment textareas
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('.comment-textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    updateCharCount(this);
                });
                
                textarea.addEventListener('paste', function(e) {
                    setTimeout(() => updateCharCount(this), 10);
                });
                
                // Initialize count
                updateCharCount(textarea);
            });
            
            // Also handle dynamically added textareas (reply forms)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            const newTextareas = node.querySelectorAll ? node.querySelectorAll('.comment-textarea') : [];
                            newTextareas.forEach(textarea => {
                                if (!textarea.dataset.countInitialized) {
                                    textarea.dataset.countInitialized = 'true';
                                    textarea.addEventListener('input', function() {
                                        updateCharCount(this);
                                    });
                                    textarea.addEventListener('paste', function() {
                                        setTimeout(() => updateCharCount(this), 10);
                                    });
                                    updateCharCount(textarea);
                                }
                            });
                        }
                    });
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>

    <script>
        // Toggle comment form collapse
        function toggleCommentForm() {
            const container = document.getElementById('commentFormContainer');
            const icon = document.getElementById('commentFormToggleIcon');
            const toggle = document.querySelector('.comment-form-toggle');
            
            if (container && icon) {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    icon.style.transform = 'rotate(180deg)';
                    toggle.style.background = '#ffffff';
                    toggle.style.borderBottom = 'none';
                    // Focus on textarea when expanded
                    setTimeout(() => {
                        const textarea = document.getElementById('forumCommentTextarea');
                        if (textarea) {
                            textarea.focus();
                        }
                    }, 100);
                } else {
                    container.style.display = 'none';
                    icon.style.transform = 'rotate(0deg)';
                    toggle.style.background = '#f8f9fa';
                    toggle.style.borderBottom = '1px solid #e2e8f0';
                }
            }
        }
        
        // Make sure toggle works on page load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('commentFormContainer');
            const icon = document.getElementById('commentFormToggleIcon');
            const toggle = document.querySelector('.comment-form-toggle');
            
            if (container && icon && toggle) {
                // Ensure form is collapsed by default
                container.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        });
    </script>

    <script>
        // File preview function
        function previewFile(filePath, extension) {
            const extensionLower = extension.toLowerCase();
            
            if (extensionLower === 'pdf') {
                // Open PDF in new tab/window
                window.open(filePath, '_blank');
            } else if (['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'].includes(extensionLower)) {
                // Show video in modal
                showVideoModal(filePath);
            } else {
                // For other files, just open in new tab
                window.open(filePath, '_blank');
            }
        }

        // Video preview modal
        function showVideoModal(videoUrl) {
            let modal = document.getElementById('videoPreviewModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'videoPreviewModal';
                modal.className = 'video-preview-modal';
                modal.innerHTML = `
                    <div class="video-preview-modal-overlay" onclick="closeVideoModal()"></div>
                    <div class="video-preview-modal-content">
                        <button class="video-preview-modal-close" onclick="closeVideoModal()">&times;</button>
                        <video id="videoPreviewPlayer" controls style="width: 100%; max-height: 80vh; border-radius: 4px;">
                            <source src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                `;
                document.body.appendChild(modal);
                
                // Add CSS if not already added
                if (!document.getElementById('videoPreviewModalStyles')) {
                    const style = document.createElement('style');
                    style.id = 'videoPreviewModalStyles';
                    style.textContent = `
                        .video-preview-modal {
                            display: none;
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.8);
                            z-index: 10000;
                            align-items: center;
                            justify-content: center;
                        }
                        .video-preview-modal-overlay {
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                        }
                        .video-preview-modal-content {
                            position: relative;
                            background: #ffffff;
                            padding: 2rem;
                            border-radius: 8px;
                            max-width: 90%;
                            max-height: 90vh;
                            z-index: 10001;
                        }
                        .video-preview-modal-close {
                            position: absolute;
                            top: 0.5rem;
                            right: 0.5rem;
                            background: rgba(0, 0, 0, 0.5);
                            color: white;
                            border: none;
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            font-size: 1.5rem;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            z-index: 10002;
                        }
                        .video-preview-modal-close:hover {
                            background: rgba(0, 0, 0, 0.7);
                        }
                    `;
                    document.head.appendChild(style);
                }
            }

            const videoPlayer = document.getElementById('videoPreviewPlayer');
            videoPlayer.src = videoUrl;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Play video when modal opens
            videoPlayer.play();
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoPreviewModal');
            if (modal) {
                const videoPlayer = document.getElementById('videoPreviewPlayer');
                if (videoPlayer) {
                    videoPlayer.pause();
                    videoPlayer.src = '';
                }
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Close video modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVideoModal();
            }
        });
    </script>

@endsection

@section('scripts')
@include('common.select2')
<!-- Lobibox Notifications JS -->
<script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />
@endsection
