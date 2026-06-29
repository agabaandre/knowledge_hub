@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0d7a3a';
@endphp
<style>
    :root {
        --khub-chrome-top: 5.5rem;
        --khub-chrome-footer: 4.5rem;
        --khub-preloader-accent: {{ $primaryColor }};
        --khub-preloader-accent-dark: {{ $secondaryColor }};
    }

    .khub-page-content,
    #content.content.khub-gt-content {
        position: relative;
        min-height: 12rem;
    }

    /* Fixed band between header and footer — spinner stays viewport-centered like helpdesk */
    .khub-content-preloader {
        position: fixed;
        top: var(--khub-chrome-top);
        right: 0;
        bottom: var(--khub-chrome-footer);
        left: 0;
        z-index: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 249, 250, 0.97);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        transition: opacity 0.35s ease, visibility 0.35s ease;
        pointer-events: auto;
    }

    .khub-content-preloader.khub-content-preloader--out,
    .khub-content-preloader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .khub-content-preloader__inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
        text-align: center;
    }

    .khub-content-preloader__spinner {
        position: relative;
        width: 2.75rem;
        height: 2.75rem;
    }

    .khub-content-preloader__ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid rgba(17, 154, 72, 0.14);
        border-top-color: var(--khub-preloader-accent);
        animation: khub-preloader-spin 0.72s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    }

    .khub-content-preloader__spinner::after {
        content: '';
        position: absolute;
        inset: 0.72rem;
        border-radius: 50%;
        background: var(--khub-preloader-accent);
        opacity: 0.85;
        animation: khub-preloader-pulse 1.4s ease-in-out infinite;
    }

    .khub-content-preloader__label {
        margin: 0;
        font-family: Arial, Roboto, sans-serif;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--khub-preloader-accent-dark);
    }

    @keyframes khub-preloader-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes khub-preloader-pulse {
        0%, 100% {
            transform: scale(0.92);
            opacity: 0.65;
        }
        50% {
            transform: scale(1);
            opacity: 1;
        }
    }

    html[data-bs-theme="dark"] .khub-content-preloader {
        background: rgba(15, 23, 42, 0.97);
    }

    html[data-bs-theme="dark"] .khub-content-preloader__ring {
        border-color: rgba(74, 222, 128, 0.16);
        border-top-color: #4ade80;
    }

    html[data-bs-theme="dark"] .khub-content-preloader__spinner::after {
        background: #4ade80;
    }

    html[data-bs-theme="dark"] .khub-content-preloader__label {
        color: #4ade80;
    }

    @media (prefers-color-scheme: dark) {
        html[data-bs-theme="system"] .khub-content-preloader,
        html:not([data-bs-theme="light"]) .khub-content-preloader {
            background: rgba(15, 23, 42, 0.97);
        }
    }

    /* Legacy gif preloader inside content shell */
    #khub-page-content > .preloader,
    #content.content > .preloader,
    .khub-page-content > .preloader {
        position: fixed;
        top: var(--khub-chrome-top);
        right: 0;
        bottom: var(--khub-chrome-footer);
        left: 0;
        width: auto;
        height: auto;
        z-index: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 249, 250, 0.97);
    }
</style>
