@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0e7a3a';
    $auGold = settings()->au_gold ?? '#B4A269';
    $gradientStart = settings()->gradient_start_color ?? '#119A48';
    $gradientEnd = settings()->gradient_end_color ?? '#16c653';
@endphp
<style>
    :root {
        --community-ui-radius: 4px;
    }

    .community-detail-page .card,
    .community-detail-page .btn,
    .community-detail-page .alert,
    .community-detail-page .form-control,
    .community-detail-page .badge:not(.community-tab-new-badge) {
        border-radius: var(--community-ui-radius) !important;
    }

    .community-detail-page .btn-group .btn:first-child {
        border-top-left-radius: var(--community-ui-radius) !important;
        border-bottom-left-radius: var(--community-ui-radius) !important;
    }

    .community-detail-page .btn-group .btn:last-child {
        border-top-right-radius: var(--community-ui-radius) !important;
        border-bottom-right-radius: var(--community-ui-radius) !important;
    }

    .theme-text {
        color: {{ $primaryColor }};
    }

    .community-detail-shell {
        background: #f4f5f7;
        padding: 2rem 0 3rem;
    }

    .community-detail-hero__metrics {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .community-detail-hero__metric {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(4px);
    }

    .community-detail-hero__metric--members { background: linear-gradient(135deg, rgba(17, 154, 72, 0.85), rgba(22, 198, 83, 0.75)); }
    .community-detail-hero__metric--forums { background: linear-gradient(135deg, rgba(59, 130, 246, 0.85), rgba(37, 99, 235, 0.75)); }
    .community-detail-hero__metric--publications { background: linear-gradient(135deg, rgba(180, 162, 105, 0.9), rgba(146, 124, 58, 0.8)); }
    .community-detail-hero__metric--comments { background: linear-gradient(135deg, rgba(168, 85, 247, 0.85), rgba(126, 34, 206, 0.75)); }

    .community-about-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        border-left: 4px solid {{ $primaryColor }};
    }

    .community-about-card__title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }

    .community-detail-tabs {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
        gap: 0.25rem;
    }

    .community-detail-tabs .nav-link {
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.85rem 1.25rem;
        font-weight: 600;
        border-radius: var(--community-ui-radius) var(--community-ui-radius) 0 0;
        transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }

    .community-detail-tabs .nav-link:hover {
        color: {{ $primaryColor }};
        background: rgba(17, 154, 72, 0.06);
    }

    .community-detail-tabs .nav-link.active {
        color: {{ $primaryColor }};
        border-bottom-color: {{ $primaryColor }};
        background: rgba(17, 154, 72, 0.08);
    }

    .community-detail-tabs .nav-link .badge {
        font-size: 0.6875rem;
        vertical-align: middle;
    }

    .community-detail-tab-btn {
        background: transparent;
        border: none;
        width: 100%;
        text-align: inherit;
        cursor: pointer;
        font: inherit;
        color: inherit;
        line-height: inherit;
    }

    .community-detail-tab-btn:focus {
        outline: 2px solid rgba(17, 154, 72, 0.35);
        outline-offset: 2px;
    }

    .community-detail-sidebar-card--other {
        overflow: visible;
    }

    .community-other-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        padding-top: 0.15rem;
    }

    .community-other-card {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.85rem;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        text-decoration: none;
        color: inherit;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        position: relative;
    }

    .community-other-card:hover {
        text-decoration: none;
        color: inherit;
        border-color: rgba(17, 154, 72, 0.35);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    .community-other-card__avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--community-ui-radius);
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    }

    .community-other-card__body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .community-other-card__name {
        display: block;
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        line-height: 1.35;
    }

    .community-other-card:hover .community-other-card__name {
        color: {{ $primaryColor }};
    }

    .community-other-card__desc {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.45;
    }

    .community-other-card__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.15rem;
    }

    .community-other-card__stat {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.45rem;
        border-radius: var(--community-ui-radius);
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        font-size: 0.68rem;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
    }

    .community-other-card__stat i {
        color: {{ $primaryColor }};
        opacity: 0.85;
    }

    .community-other-card__arrow {
        flex-shrink: 0;
        align-self: center;
        font-size: 0.7rem;
        color: #94a3b8;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .community-other-card:hover .community-other-card__arrow {
        color: {{ $primaryColor }};
        transform: translateX(2px);
    }

    .community-other-empty {
        color: #64748b;
        font-size: 0.875rem;
    }

    .community-detail-sidebar-card {
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .community-detail-sidebar-card:hover {
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.07);
    }

    .community-tab-new-badge {
        background: {{ $primaryColor }} !important;
        color: #fff !important;
        font-size: 0.625rem !important;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .community-hero-post-btn {
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(17, 154, 72, 0.35);
    }

    .community-wall-card--tab {
        margin-top: 0;
        border: none;
        box-shadow: none;
        padding: 0;
        background: transparent;
    }

    .community-wall-post-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem 1.15rem;
        margin-bottom: 1rem;
        border-radius: var(--community-ui-radius);
        border: 1px solid rgba(17, 154, 72, 0.22);
        background: linear-gradient(135deg, rgba(17, 154, 72, 0.08), rgba(180, 162, 105, 0.08));
    }

    .community-wall-post-cta__btn {
        font-weight: 600;
        white-space: nowrap;
    }

    .community-recent-wall-notice {
        padding: 0.75rem 1rem;
        border-radius: var(--community-ui-radius);
        background: #ecfdf5;
        border: 1px solid rgba(17, 154, 72, 0.2);
        color: #166534;
        font-size: 0.875rem;
    }

    .community-recent-wall-notice a {
        font-weight: 700;
        color: {{ $primaryColor }};
    }

    .community-forum-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .community-forum-card__intro {
        display: flex;
        gap: 1rem;
        padding: 1.15rem 1.15rem 0.75rem;
    }

    .community-forum-card__thumb-wrap {
        flex-shrink: 0;
        width: 4.5rem;
        height: 4.5rem;
        border-radius: var(--community-ui-radius);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
    }

    .community-forum-card__thumb-wrap--placeholder {
        color: {{ $primaryColor }};
        font-size: 1.35rem;
    }

    .community-forum-card__thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-forum-card__body {
        min-width: 0;
        flex: 1;
    }

    .community-forum-card__title {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0 0 0.35rem;
        line-height: 1.35;
    }

    .community-forum-card__title a {
        color: #1e293b;
        text-decoration: none;
    }

    .community-forum-card__title a:hover {
        color: {{ $primaryColor }};
    }

    .community-forum-card__excerpt {
        margin: 0 0 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
        line-height: 1.55;
    }

    .community-forum-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .community-forum-card__tag {
        display: inline-flex;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 0.6875rem;
        font-weight: 600;
    }

    .community-forum-card__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding: 0.75rem 1.15rem 1rem;
        border-top: 1px solid #eef2f6;
        background: #fafbfc;
    }

    .community-forum-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        color: #64748b;
    }

    .community-wall-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    .community-wall-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .community-wall-card__title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .community-pub-author {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.65rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px dashed #e2e8f0;
    }

    @include('partials.publications.publication_feed_card_styles')

    .community-pub-author__avatar {
        position: relative;
        width: 2.5rem;
        height: 2.5rem;
        min-width: 2.5rem;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .community-pub-author__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-pub-author__avatar i {
        color: {{ $primaryColor }};
        opacity: 0.85;
    }

    .community-pub-author__name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9375rem;
    }

    .community-pub-author__meta {
        display: block;
        font-size: 0.8125rem;
        color: #64748b;
    }

    /* Sidebar */
    .community-detail-sidebar-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        margin-bottom: 1.25rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .community-detail-sidebar-card__head {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid #eef2f6;
        background: linear-gradient(135deg, rgba(17, 154, 72, 0.06), rgba(180, 162, 105, 0.08));
    }

    .community-detail-sidebar-card__head--forums {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(37, 99, 235, 0.05));
    }

    .community-detail-sidebar-card__head--members {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.08), rgba(126, 34, 206, 0.05));
    }

    .community-detail-sidebar-card__head--badges {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.06));
    }

    .community-detail-sidebar-card__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: var(--community-ui-radius);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }

    .community-detail-sidebar-card__icon--green { background: linear-gradient(135deg, {{ $gradientStart }}, {{ $gradientEnd }}); }
    .community-detail-sidebar-card__icon--blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .community-detail-sidebar-card__icon--purple { background: linear-gradient(135deg, #a855f7, #7e22ce); }
    .community-detail-sidebar-card__icon--gold { background: linear-gradient(135deg, {{ $auGold }}, #927c3a); }

    .community-detail-sidebar-card__title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .community-detail-sidebar-card__hint {
        font-size: 0.8125rem;
        color: #64748b;
        margin: 0.15rem 0 0;
    }

    .community-detail-sidebar-card__body {
        padding: 1rem 1.15rem;
    }

    .community-sidebar-forum-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.65rem 0;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s ease;
    }

    .community-sidebar-forum-item:last-child { border-bottom: none; }

    .community-sidebar-forum-item:hover {
        text-decoration: none;
        color: inherit;
    }

    .community-sidebar-forum-item__thumb {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: var(--community-ui-radius);
        object-fit: cover;
        flex-shrink: 0;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: {{ $primaryColor }};
        border: 1px solid #e2e8f0;
    }

    .community-sidebar-forum-item__title {
        font-weight: 600;
        font-size: 0.875rem;
        color: #1e293b;
        line-height: 1.35;
        display: block;
    }

    .community-sidebar-forum-item__meta {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.2rem;
        display: block;
    }

    .community-sidebar-community-link {
        display: block;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
    }

    .community-sidebar-community-link:last-child { border-bottom: none; }

    .community-sidebar-community-link:hover {
        text-decoration: none;
        color: {{ $primaryColor }};
    }

    .community-sidebar-community-link__name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .community-sidebar-community-link:hover .community-sidebar-community-link__name {
        color: {{ $primaryColor }};
    }

    .community-sidebar-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 0.75rem;
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.35rem;
    }

    /* Comments (forum-inspired) */
    .comments-section { margin-top: 0; }

    .comments-header {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .comment-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        margin-bottom: 0.5rem;
    }

    .comment-item.reply-comment {
        margin-left: 2.75rem;
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

    .comment-content {
        flex: 1;
        min-width: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .comment-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .comment-author { font-weight: 600; color: #2d3748; font-size: 0.9375rem; }
    .comment-time { color: #94a3b8; font-size: 0.8125rem; }
    .comment-text { color: #4a5568; line-height: 1.6; word-wrap: break-word; }

    .comment-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        color: #64748b;
        font-size: 0.8125rem;
        font-weight: 500;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: var(--community-ui-radius);
        transition: all 0.2s ease;
        border: none;
        background: none;
    }

    .comment-action-btn:hover {
        background: rgba(17, 154, 72, 0.05);
        color: {{ $primaryColor }};
    }

    .comment-form-card { margin-bottom: 1.5rem; }

    .comment-form-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
    }

    .comment-input-wrapper { display: flex; gap: 0.75rem; }
    .comment-textarea {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 0.75rem;
        resize: vertical;
        min-height: 80px;
    }

    .comment-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        background: {{ $primaryColor }};
        color: #fff;
        border: none;
        border-radius: var(--community-ui-radius);
        font-weight: 600;
        cursor: pointer;
    }

    .file-upload-area {
        margin-top: 0.75rem;
        padding: 0.75rem;
        border: 1px dashed #cbd5e0;
        border-radius: var(--community-ui-radius);
        background: #f8fafc;
        text-align: center;
        font-size: 0.8125rem;
        color: #64748b;
    }

    .no-comments {
        text-align: center;
        padding: 2rem 1rem;
        color: #64748b;
        background: #f8fafc;
        border-radius: var(--community-ui-radius);
        border: 1px dashed #e2e8f0;
    }

    .member-item-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        overflow: hidden;
        background: #e4e6e8;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        color: #3c4146;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .member-item-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .member-name-link { color: inherit; text-decoration: none; font-weight: 600; }
    .member-name-link:hover { color: {{ $primaryColor }}; text-decoration: underline; }
    .member-item { padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9; }
    .member-item:last-child { border-bottom: none; }
    .member-name { font-weight: 600; color: #2d3748; margin-bottom: 0.25rem; }
    .member-title { font-size: 0.875rem; color: #64748b; }

    #memberSearchForm .input-group { align-items: stretch; }
    #memberSearchForm .form-control { height: 40px; border-right: 0; }
    #memberSearchForm .btn {
        height: 40px;
        min-width: 44px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .community-content-request-card { border-left: 4px solid {{ $primaryColor }}; }

    .community-featured-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .community-featured-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.35rem;
    }

    .community-featured-meta {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .community-detail-participants {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    .community-detail-participants__label {
        width: 100%;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .community-detail-participants .community-room-card__avatar-wrap {
        width: 36px;
        height: 36px;
        min-width: 36px;
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
        border-radius: 50%;
        overflow: hidden;
        background: #e4e6e8;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.06);
        text-decoration: none;
        color: #3c4146;
    }

    .community-detail-participants .community-room-card__avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-detail-participants .community-room-card__avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 0.7rem;
        font-weight: 700;
    }

    html[data-bs-theme="dark"] .community-detail-shell { background: #18191a; }
    html[data-bs-theme="dark"] .community-about-card,
    html[data-bs-theme="dark"] .community-wall-card,
    html[data-bs-theme="dark"] .community-detail-sidebar-card,
    html[data-bs-theme="dark"] .comment-content {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .community-about-card__title,
    html[data-bs-theme="dark"] .community-wall-card__title,
    html[data-bs-theme="dark"] .community-detail-sidebar-card__title,
    html[data-bs-theme="dark"] .comment-author { color: #e4e6eb !important; }
</style>
@include('publications.partials.publication_list_styles')
