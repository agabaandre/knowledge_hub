@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0e7a3a';
    $auGold = settings()->au_gold ?? '#B4A269';
@endphp
<style>
    .cr-track-messages { margin-bottom: 0.5rem; }

    .cr-track-message {
        display: flex;
        gap: 0.85rem;
        margin-bottom: 1.1rem;
    }

    .cr-track-message__avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cr-track-message__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cr-track-message__avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #fff;
        background: {{ $primaryColor }};
    }

    .cr-track-message--requester .cr-track-message__avatar-fallback {
        background: {{ $auGold }};
        color: #1e293b;
    }

    .cr-track-message__bubble {
        flex: 1;
        min-width: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.9rem 1rem 1rem;
        box-shadow: 0 1px 3px rgba(15,23,42,0.04);
    }

    .cr-track-message__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.35rem 0.65rem;
        margin-bottom: 0.5rem;
    }

    .cr-track-message__author {
        color: #1e293b;
        font-size: 0.9375rem;
    }

    .cr-track-message__badge {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        background: rgba(180,162,105,0.2);
        color: #7c6a2e;
    }

    .cr-track-message__badge--team {
        background: rgba(17,154,72,0.12);
        color: {{ $secondaryColor }};
    }

    .cr-track-message__time {
        font-size: 0.8125rem;
        color: #94a3b8;
        margin-left: auto;
    }

    .cr-track-message__body {
        color: #334155;
        line-height: 1.65;
        font-size: 0.9375rem;
    }

    .cr-track-message__body.rich-text-content p:last-child { margin-bottom: 0; }

    .cr-track-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #64748b;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
    }

    .cr-track-empty__icon {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 768px) {
        .cr-track-message__time { margin-left: 0; width: 100%; }
    }
</style>
