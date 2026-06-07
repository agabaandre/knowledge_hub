<style>
    /* LinkedIn-style main nav: icon stacked above label */
    .kh-linkedin-nav { align-items: stretch; gap: 0.05rem; }
    .kh-linkedin-nav .kh-nav-item,
    .kh-linkedin-nav > li > a {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center;
        min-width: 3.25rem;
        padding: 0.3rem 0.35rem 0.15rem !important;
        text-align: center;
        border-bottom: 2px solid transparent;
        position: relative;
        gap: 0.08rem;
        font-size: var(--nav-font-size, 11px);
        font-weight: var(--nav-font-weight, 500);
    }
    .kh-linkedin-nav .kh-nav-item-stack {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.08rem;
    }
    .kh-linkedin-nav .kh-nav-icon {
        display: block;
        font-size: var(--nav-icon-size, 16px);
        line-height: 1;
        margin: 0 !important;
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome" !important;
        font-weight: 900 !important;
        font-style: normal;
        width: auto !important;
        height: auto !important;
        position: static !important;
        transform: none !important;
        -webkit-font-smoothing: antialiased;
        opacity: 0.88;
    }
    .kh-linkedin-nav .kh-nav-label {
        display: block;
        font-size: var(--nav-font-size, 11px);
        line-height: 1.15;
        white-space: nowrap;
        letter-spacing: 0.01em;
    }
    .kh-linkedin-nav .kh-nav-badge,
    .kh-linkedin-nav .menu-badge {
        position: absolute;
        top: 0.1rem;
        right: 0.15rem;
        min-width: 1rem;
        height: 1rem;
        padding: 0 0.25rem;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 0.6rem;
        font-weight: 700;
        line-height: 1rem;
        margin: 0 !important;
        float: none;
    }
    .kh-linkedin-nav .submenu-indicator {
        position: absolute;
        bottom: 0.15rem;
        right: 0.2rem;
        transform: scale(0.65);
        margin: 0 !important;
    }
    html.menu-icons-disabled .kh-linkedin-nav .kh-nav-icon {
        display: none !important;
    }
    /* Legacy navigation plugin */
    .navigation-landscape .nav-menus-wrapper {
        display: flex !important;
        align-items: stretch !important;
        justify-content: center;
        width: 100%;
        float: none !important;
    }
    .navigation-landscape .nav-menus-wrapper > .nav-menu.kh-linkedin-nav {
        display: flex !important;
        flex-wrap: nowrap;
        align-items: stretch;
        float: none !important;
        margin: 0;
    }
    .navigation-landscape .nav-menus-wrapper > .nav-menu.nav-menu-social.kh-linkedin-nav {
        float: none !important;
        margin-left: auto;
        flex-shrink: 0;
        display: flex !important;
        align-items: stretch;
    }
    .navigation-landscape .nav-menus-wrapper > .nav-menu.nav-menu-social.kh-linkedin-nav > li {
        float: none;
        display: flex;
        align-items: stretch;
    }
    .navigation .nav-menu.kh-linkedin-nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: stretch;
    }
    .navigation .nav-menu.kh-linkedin-nav > li > a {
        height: 100%;
        padding: 0.3rem 0.45rem 0.15rem !important;
    }
    .navigation .nav-menu.nav-menu-social.kh-linkedin-nav > li > a {
        padding: 0.3rem 0.55rem 0.15rem !important;
        height: 100%;
        min-width: 4rem;
    }
    .navigation .nav-menu.kh-linkedin-nav > li > a > i:not(.kh-nav-icon) {
        width: auto !important;
        height: auto !important;
        transform: none !important;
        position: static !important;
    }
    .navigation .nav-menu.kh-linkedin-nav > li.active > a,
    .navigation .nav-menu.kh-linkedin-nav > li > a:hover,
    .kh-linkedin-nav .kh-nav-item.active {
        border-bottom-color: var(--theme-color-primary, #119A48);
        color: var(--theme-color-primary, #119A48);
        font-weight: 600;
    }
    @media (max-width: 991.98px) {
        .kh-linkedin-nav .kh-nav-item,
        .kh-linkedin-nav > li > a {
            flex-direction: row !important;
            justify-content: flex-start;
            min-width: 0;
            padding: 0.55rem 0.75rem !important;
            border-bottom: none;
        }
        .kh-linkedin-nav .kh-nav-item-stack {
            flex-direction: row;
            gap: 0.55rem;
        }
        .kh-linkedin-nav .kh-nav-badge,
        .kh-linkedin-nav .menu-badge {
            position: static;
            margin-left: 0.35rem !important;
        }
        .kh-linkedin-nav .submenu-indicator {
            position: static;
            transform: none;
            margin-left: auto !important;
        }
    }
</style>
