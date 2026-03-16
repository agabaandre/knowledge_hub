@include('partials.theming.typography')
<style>
    :root {
    --theme-color-primary: {{settings()->primary_color}};
    --theme-color-secondary: {{settings()->secondary_color}};
    --text-color-primary: {{settings()->primary_text_color}};
    --text-color-secondary: {{settings()->links_active_color}};
    --icon-color: {{settings()->icon_font_color}};
    --banner-text-color: {{settings()->banner_text}};
    --sw-border-color:  #eeeeee;
    --sw-toolbar-btn-color:  #ffffff;
    --sw-toolbar-btn-background-color:  #28a745;
    --sw-anchor-default-primary-color:  #82ba8f;
    --sw-anchor-default-secondary-color:  #b0b0b1;
    --sw-anchor-active-primary-color:  #28a745;
    --sw-anchor-active-secondary-color:  #ffffff;
    --sw-anchor-done-primary-color:  #82ba8f;
    --sw-anchor-done-secondary-color:  #fefefe;
    --sw-anchor-disabled-primary-color:  #82ba8f;
    --sw-anchor-disabled-secondary-color:  #dbe0e5;
    --sw-anchor-error-primary-color:  #dc3545;
    --sw-anchor-error-secondary-color:  #ffffff;
    --sw-anchor-warning-primary-color:  #ffc107;
    --sw-anchor-warning-secondary-color:  #ffffff;
    --sw-progress-color:  #28a745;
    --sw-progress-background-color:  #82ba8f;
    --sw-loader-color:  #28a745;
    --sw-loader-background-color:  #82ba8f;
    --sw-loader-background-wrapper-color:  rgba(255, 255, 255, 0.7);
    }

    .w-100{
            min-width: 100%!important;
        }
    .logo-inverse { filter: brightness(0) invert(1); }
    /* Dark theme (user preference) */
    html[data-bs-theme="dark"] body { background-color: #1a1d21; color: #e4e6eb; }
    html[data-bs-theme="dark"] .bg-light { background-color: #242628 !important; }
    html[data-bs-theme="dark"] .card, html[data-bs-theme="dark"] .form-control { background-color: #242628; border-color: #3e4348; color: #e4e6eb; }
    /* Dark theme: main nav text visible */
    html[data-bs-theme="dark"] .header.modern-header { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .navigation .nav-menu > li > a,
    html[data-bs-theme="dark"] .navigation .nav-dropdown a,
    html[data-bs-theme="dark"] .navigation a.theme-cl { color: rgba(255,255,255,0.95) !important; }
    html[data-bs-theme="dark"] .navigation .nav-menu > li:hover > a,
    html[data-bs-theme="dark"] .navigation .nav-menu > li.active > a,
    html[data-bs-theme="dark"] .navigation .nav-dropdown li a:hover { color: #fff !important; }
    html[data-bs-theme="dark"] .navigation .nav-dropdown { background: #2d3136 !important; border-color: #3e4348; }
</style>