@php
    $primaryColor = settings()->primary_color ?? '#119A48';
@endphp
    .publication-feed-card-scope {
        --community-ui-radius: 4px;
    }

    .publication-feed-card-scope .js-open-pdf-chat {
        border: none;
    }

    /* Publication cards — forum-inspired layout */
    .publication-feed-card-scope .community-pub-card {
        border-radius: var(--community-ui-radius);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.04);
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .publication-feed-card-scope .community-pub-card:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08), 0 12px 28px rgba(15, 23, 42, 0.06);
        border-color: rgba(17, 154, 72, 0.22);
    }

    .community-pub-intro {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        padding: 1.35rem 1.35rem 1rem 1.5rem;
    }

    .community-pub-intro__media {
        flex-shrink: 0;
    }

    .community-pub-intro__image-link {
        display: block;
        line-height: 0;
    }

    .community-pub-intro__image {
        width: 275px;
        height: 330px;
        object-fit: contain;
        border-radius: var(--community-ui-radius);
        border: 1px solid rgba(17, 154, 72, 0.15);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        background: #fff;
        transition: transform 0.25s ease;
    }

    .community-pub-intro__image-link:hover .community-pub-intro__image {
        transform: scale(1.02);
    }

    .community-pub-intro__text {
        flex: 1;
        min-width: 0;
    }

    .community-pub-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.65rem;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .community-pub-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .community-pub-title a:hover {
        color: {{ $primaryColor }};
    }

    .community-pub-description {
        margin: 0;
        color: #475569;
        line-height: 1.65;
        font-size: 0.9375rem;
        text-align: justify;
        text-justify: inter-word;
        display: -webkit-box;
        -webkit-line-clamp: 9;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .community-pub-description a {
        color: inherit;
        text-decoration: none;
    }

    .community-pub-description a:hover {
        color: {{ $primaryColor }};
    }

    .community-pub-body {
        padding: 0 1.35rem 1.35rem 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .community-pub-body::before {
        content: '';
        display: block;
        height: 1px;
        margin: 0 0 0.15rem;
        background: linear-gradient(90deg, rgba(17, 154, 72, 0.18), rgba(96, 165, 250, 0.12), transparent);
    }

    .community-pub-meta-list {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .community-pub-meta-row {
        display: flex;
        align-items: flex-start;
        gap: 0.55rem;
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.5;
    }

    .community-pub-meta-row i {
        flex-shrink: 0;
        width: 1rem;
        margin-top: 0.15rem;
        color: {{ $primaryColor }};
        opacity: 0.9;
    }

    .community-pub-meta-row strong {
        color: #334155;
        font-weight: 600;
    }

    .community-pub-meta-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.35rem 1.25rem;
    }

    .community-pub-meta-part {
        display: inline;
    }

    .community-pub-stats {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .community-pub-stat {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
    }

    .community-pub-stat i {
        font-size: 0.75rem;
    }

    .community-pub-stat--views {
        border-color: rgba(17, 154, 72, 0.18);
        background: #f0fdf4;
    }

    .community-pub-stat--views i {
        color: {{ $primaryColor }};
    }

    .community-pub-stat--comments:hover {
        border-color: rgba(59, 130, 246, 0.25);
        background: #eff6ff;
        color: #1d4ed8;
        text-decoration: none;
    }

    .community-pub-stat--comments i {
        color: #3b82f6;
    }

    .community-pub-stat--likes {
        border-color: rgba(239, 68, 68, 0.2);
        background: #fef2f2;
    }

    .community-pub-stat--likes i {
        color: #ef4444;
    }

    .community-pub-stat--time i {
        color: {{ $primaryColor }};
    }

    .community-pub-footer {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--community-ui-radius);
        background: linear-gradient(180deg, #fafbfc 0%, #f8fafc 100%);
        border: 1px solid #e8edf2;
    }

    .community-pub-footer__avatar {
        width: 4.25rem;
        height: 4.25rem;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 3px solid #fff;
        outline: 2px solid rgba(17, 154, 72, 0.25);
        background: linear-gradient(145deg, #ecfdf5, #dbeafe);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .community-pub-footer__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-pub-footer__avatar i {
        font-size: 2rem;
        color: {{ $primaryColor }};
        opacity: 0.85;
    }

    .community-pub-footer__body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .community-pub-footer__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 0.65rem;
    }

    .community-pub-footer__author-text {
        max-width: min(100%, 22rem);
    }

    .community-pub-footer__name {
        display: block;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        line-height: 1.25;
    }

    .community-pub-footer__subtitle {
        display: block;
        font-size: 0.8125rem;
        font-weight: 400;
        color: #64748b;
        line-height: 1.35;
        margin-top: 0.15rem;
    }

    .community-pub-footer__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-start !important;
        width: 100%;
    }

    .community-pub-action-btn--primary {
        background-color: {{ $primaryColor }} !important;
        border-color: {{ $primaryColor }} !important;
        color: #fff !important;
    }

    .community-pub-share-actions--inline {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-left: auto;
        flex-wrap: wrap;
    }

    .community-pub-share-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        color: #64748b;
        background: #fff;
        text-decoration: none;
        font-size: 0.875rem;
        padding: 0;
        cursor: pointer;
        transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }

    .community-pub-share-btn:hover {
        color: {{ $primaryColor }};
        border-color: {{ $primaryColor }};
        background: rgba(17, 154, 72, 0.08);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .community-pub-attachments {
        border-radius: var(--community-ui-radius);
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
        border: 1px solid rgba(17, 154, 72, 0.12);
        overflow: hidden;
    }

    .community-pub-attachments__toggle {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        width: 100%;
        padding: 0.65rem 0.85rem;
        margin: 0;
        list-style: none;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        user-select: none;
    }

    .community-pub-attachments__toggle::-webkit-details-marker {
        display: none;
    }

    .community-pub-attachments__toggle-hide,
    .community-pub-attachments[open] .community-pub-attachments__toggle-show {
        display: none;
    }

    .community-pub-attachments[open] .community-pub-attachments__toggle-hide {
        display: inline;
    }

    .community-pub-attachments__chevron {
        margin-left: auto;
        font-size: 0.7rem;
        color: #64748b;
        transition: transform 0.2s ease;
    }

    .community-pub-attachments[open] .community-pub-attachments__chevron {
        transform: rotate(180deg);
    }

    .community-pub-attachments__panel {
        padding: 0 0.85rem 0.85rem;
    }

    .community-pub-attachments__strip {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: stretch;
    }

    .community-pub-attachment-chip {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: min(100%, 220px);
        max-width: 280px;
        flex: 1 1 200px;
        padding: 0.45rem 0.55rem;
        border-radius: var(--community-ui-radius);
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .community-pub-attachment-chip--image {
        padding: 0.35rem;
    }

    .community-pub-attachment-thumb {
        border: none;
        background: transparent;
        padding: 0;
        width: 3.25rem;
        height: 3.25rem;
        border-radius: var(--community-ui-radius);
        overflow: hidden;
        flex-shrink: 0;
        cursor: pointer;
    }

    .community-pub-attachment-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-pub-attachment-chip__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--community-ui-radius);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.15rem;
    }

    .community-pub-attachment-chip__icon--pdf {
        background: #fef2f2;
        color: #dc2626;
    }

    .community-pub-attachment-chip__icon--file {
        background: #eff6ff;
        color: #2563eb;
    }

    .community-pub-attachment-chip__body {
        min-width: 0;
        flex: 1;
    }

    .community-pub-attachment-chip__name {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .community-pub-attachment-chip__actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.15rem;
    }

    .community-pub-attachment-more {
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.75rem;
        border-radius: var(--community-ui-radius);
        border: 1px dashed #cbd5e1;
        color: {{ $primaryColor }};
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
    }

    .community-pub-comments-panel {
        margin-top: 0.15rem;
    }

    .community-pub-comments-panel .comments-list {
        max-height: 360px;
        overflow-y: auto;
        padding-right: 0.35rem;
        margin-bottom: 0.65rem;
    }

    .community-pub-comment-mini {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem;
        border: 1px solid #e8edf2;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        border-radius: var(--community-ui-radius);
        margin-bottom: 0.5rem;
    }

    .community-pub-comment-mini .comment-avatar-mini {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }

    .community-pub-comment-mini .comment-avatar-mini img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-pub-comment-mini .comment-author-mini {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.875rem;
    }

    .community-pub-comment-mini .comment-text-mini {
        color: #475569;
        font-size: 0.875rem;
        line-height: 1.5;
        margin: 0.25rem 0;
    }

    .community-pub-comment-mini .comment-time-mini {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .community-pub-stats .pub-comments-toggle {
        border: 1px solid #e2e8f0;
        cursor: pointer;
        background: #fff;
    }

    .community-pub-stats .pub-comments-toggle:hover {
        border-color: rgba(59, 130, 246, 0.25);
        background: #eff6ff;
        color: #1d4ed8;
    }

    .inline-comment-form {
        margin-top: 0.35rem;
        padding: 0.85rem;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        background: #fff;
    }

    .inline-comment-form .comment-textarea {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: var(--community-ui-radius);
        padding: 0.65rem 0.75rem;
        resize: vertical;
        min-height: 72px;
        font-size: 0.875rem;
    }

    .inline-comment-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        margin-top: 0.55rem;
    }

    .community-pub-comments-panel .no-comments {
        text-align: center;
        padding: 1rem;
        color: #64748b;
        background: #f8fafc;
        border-radius: var(--community-ui-radius);
        border: 1px dashed #e2e8f0;
        font-size: 0.875rem;
    }

    @media (max-width: 767.98px) {
        .community-pub-intro {
            flex-direction: column;
            padding: 1rem;
        }

        .community-pub-intro__image {
            width: 100%;
            max-width: 275px;
            height: auto;
            min-height: 220px;
            max-height: 330px;
            aspect-ratio: 275 / 330;
        }

        .community-pub-body {
            padding: 0 1rem 1rem;
        }

        .community-pub-title {
            font-size: 1.1rem;
        }

        .community-pub-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .community-pub-footer__avatar {
            width: 3.25rem;
            height: 3.25rem;
        }

        .community-pub-footer__avatar i {
            font-size: 1.5rem;
        }

        .community-pub-share-actions--inline {
            margin-left: 0;
            width: 100%;
            margin-top: 0.35rem;
        }

        .community-pub-attachment-chip {
            max-width: 100%;
            flex-basis: 100%;
        }
    }

    .publication-feed-card-scope .publication-card-actions {
        justify-content: flex-start !important;
    }
