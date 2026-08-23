<style>
    .home-spotlight,
    .theme1-spotlight {
        --spotlight-anim-duration: 18s;
        isolation: isolate;
        position: relative;
    }
    .home-spotlight--speed-slow { --spotlight-anim-duration: 28s; }
    .home-spotlight--speed-medium { --spotlight-anim-duration: 16s; }
    .home-spotlight--speed-fast { --spotlight-anim-duration: 9s; }

    .home-spotlight__stage {
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .home-spotlight::before,
    .home-spotlight::after,
    .theme1-spotlight::after {
        pointer-events: none;
        z-index: 1;
    }
    .home-spotlight > *:not(.home-spotlight__stage) {
        position: relative;
        z-index: 2;
    }

    .home-spotlight--anim-kenburns,
    .home-spotlight--anim-pan {
        overflow: hidden;
    }

    .home-spotlight--anim-kenburns .home-spotlight__stage {
        inset: -12%;
        animation: spotlightKenBurns var(--spotlight-anim-duration) ease-in-out infinite alternate;
    }

    .home-spotlight--anim-pan .home-spotlight__stage {
        width: 128%;
        height: 114%;
        inset: auto;
        top: -7%;
        left: -14%;
        animation: spotlightPan var(--spotlight-anim-duration) ease-in-out infinite alternate;
    }

    .home-spotlight--anim-gradient::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(120deg, rgba(17,154,72,.38), rgba(22,198,83,.18), rgba(15,23,42,.28), rgba(17,154,72,.38));
        background-size: 300% 300%;
        animation: spotlightGradient var(--spotlight-anim-duration) ease infinite;
        mix-blend-mode: soft-light;
    }

    .home-spotlight--anim-aurora::before,
    .home-spotlight--anim-aurora::after {
        content: '';
        position: absolute;
        width: 58%;
        height: 72%;
        z-index: 1;
        filter: blur(52px);
        opacity: .48;
        border-radius: 50%;
    }
    .home-spotlight--anim-aurora::before {
        left: -12%;
        top: -22%;
        background: radial-gradient(circle, {{ settings()->primary_color ?? '#119A48' }} 0%, transparent 70%);
        animation: spotlightAuroraA var(--spotlight-anim-duration) ease-in-out infinite alternate;
    }
    .home-spotlight--anim-aurora::after {
        right: -10%;
        bottom: -28%;
        background: radial-gradient(circle, #16c653 0%, transparent 70%);
        animation: spotlightAuroraB var(--spotlight-anim-duration) ease-in-out infinite alternate;
    }

    .home-spotlight--anim-shimmer::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(115deg, transparent 0%, transparent 38%, rgba(255,255,255,.26) 50%, transparent 62%, transparent 100%);
        background-size: 220% 100%;
        animation: spotlightShimmer var(--spotlight-anim-duration) linear infinite;
    }

    .home-spotlight--anim-pulse::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        background: rgba(0,0,0,.2);
        animation: spotlightPulse var(--spotlight-anim-duration) ease-in-out infinite;
    }

    @keyframes spotlightKenBurns {
        from { transform: scale(1) translate3d(0, 0, 0); }
        to { transform: scale(1.12) translate3d(-2.4%, -1.6%, 0); }
    }
    @keyframes spotlightPan {
        from { transform: translate3d(0, 0, 0); }
        to { transform: translate3d(9%, 1.5%, 0); }
    }
    @keyframes spotlightGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes spotlightAuroraA {
        from { transform: translate3d(0, 0, 0) scale(1); }
        to { transform: translate3d(18%, 12%, 0) scale(1.16); }
    }
    @keyframes spotlightAuroraB {
        from { transform: translate3d(0, 0, 0) scale(1); }
        to { transform: translate3d(-16%, -10%, 0) scale(1.22); }
    }
    @keyframes spotlightShimmer {
        from { background-position: 130% 0; }
        to { background-position: -40% 0; }
    }
    @keyframes spotlightPulse {
        0%, 100% { opacity: .12; }
        50% { opacity: .4; }
    }

    @media (prefers-reduced-motion: reduce) {
        .home-spotlight::before,
        .home-spotlight::after,
        .theme1-spotlight::before,
        .theme1-spotlight::after,
        .home-spotlight__stage {
            animation: none !important;
            transform: none !important;
        }
        .home-spotlight--anim-kenburns .home-spotlight__stage,
        .home-spotlight--anim-pan .home-spotlight__stage {
            inset: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
    }
</style>
