@php
    $primaryColor = settings()->primary_color ?? '#119A48';
    $secondaryColor = settings()->secondary_color ?? '#0d7a3a';
@endphp
<style>
    :root {
        /* Fallbacks; JS sets exact values from header/footer/content top */
        --khub-chrome-top: 96px;
        --khub-chrome-footer: 4.5rem;
    }

    .khub-page-content,
    #content.content.khub-gt-content {
        min-height: 12rem;
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
        justify-content: center;
        gap: 1.25rem;
        text-align: center;
        padding: 1.5rem;
    }

    .khub-content-preloader__spinner {
        position: relative;
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
    }

    .khub-content-preloader__ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid rgba(17, 154, 72, 0.14);
        border-top-color: {{ $primaryColor }};
        animation: khub-preloader-spin 0.72s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
    }

    .khub-content-preloader__spinner::after {
        content: '';
        position: absolute;
        inset: 0.72rem;
        border-radius: 50%;
        background: {{ $primaryColor }};
        opacity: 0.85;
        animation: khub-preloader-pulse 1.4s ease-in-out infinite;
    }

    .khub-content-preloader__label {
        margin: 0;
        max-width: 18rem;
        font-family: inherit;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        line-height: 1.4;
        color: {{ $secondaryColor }};
    }

    [data-bs-theme="dark"] .khub-content-preloader {
        background: rgba(15, 23, 42, 0.97);
    }

    [data-bs-theme="dark"] .khub-content-preloader__ring {
        border-color: rgba(74, 222, 128, 0.16);
        border-top-color: {{ $primaryColor }};
    }

    [data-bs-theme="dark"] .khub-content-preloader__label {
        color: {{ $primaryColor }};
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
</style>
