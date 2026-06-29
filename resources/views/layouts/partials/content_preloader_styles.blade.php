@php
    $primaryColor = settings()->primary_color ?? '#119A48';
@endphp
<style>
    :root {
        --khub-chrome-top: 5.5rem;
        --khub-chrome-footer: 4.5rem;
        --khub-preloader-accent: {{ $primaryColor }};
    }

    .khub-page-content,
    #content.content.khub-gt-content {
        position: relative;
        min-height: 12rem;
    }

    .khub-page-content.khub-content-loading,
    #content.content.khub-content-loading {
        min-height: calc(100vh - var(--khub-chrome-top) - var(--khub-chrome-footer));
    }

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
        padding: 1rem;
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
        border: 3px solid color-mix(in srgb, var(--khub-preloader-accent) 14%, transparent);
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
        color: var(--khub-preloader-accent);
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

    [data-bs-theme="dark"] .khub-content-preloader {
        background: rgba(15, 23, 42, 0.97);
    }

    @media (max-width: 991.98px) {
        :root {
            --khub-chrome-top: 4.25rem;
        }
    }
</style>
