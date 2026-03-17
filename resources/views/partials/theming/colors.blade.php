@include('partials.theming.typography')
<style>
    :root {
    --theme-color-primary: {{settings()->primary_color}};
    --theme-color-secondary: {{settings()->secondary_color}};
    --text-color-primary: {{settings()->primary_text_color}};
    --text-color-secondary: {{settings()->links_active_color}};
    --icon-color: {{settings()->icon_font_color}};
    --nav-font-weight: {{ settings()->nav_font_weight ?? '500' }};
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
    .navigation .nav-menu > li > a,
    .navigation .nav-dropdown a { font-weight: var(--nav-font-weight, 500) !important; }
    #main-wrapper,
    body { font-family: var(--font-family-primary) !important; font-size: var(--front-body-font-size) !important; color: var(--default-font-color) !important; }
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

    /* ========== Default theme: dark mode – records/search, health-topics, forums, account ========== */
    html[data-bs-theme="dark"] .gray { background-color: #1a1d21 !important; }
    html[data-bs-theme="dark"] .custom-bg { background-color: #1a1d21 !important; }
    html[data-bs-theme="dark"] .sidebar-content,
    html[data-bs-theme="dark"] .sidebar-content[style] { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .sidebar-content h5,
    html[data-bs-theme="dark"] .sidebar-content .fw-bold,
    html[data-bs-theme="dark"] .sidebar-content span { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .sidebar-content .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .sidebar-content .popular-tags-title { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .card.border,
    html[data-bs-theme="dark"] .card.border .card-body { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .card h6,
    html[data-bs-theme="dark"] .card a:not(.btn) { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .card .text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .btn-outline-secondary { background: transparent; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .btn-outline-secondary:hover { background: #3e4348; border-color: #4b5262; color: #fff; }
    html[data-bs-theme="dark"] select.form-control,
    html[data-bs-theme="dark"] .form-control,
    html[data-bs-theme="dark"] input[type="text"],
    html[data-bs-theme="dark"] input[type="search"],
    html[data-bs-theme="dark"] input[type="email"],
    html[data-bs-theme="dark"] input[type="password"] { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .form-control::placeholder { color: #9ca3af; }
    html[data-bs-theme="dark"] .text-muted { color: #9ca3af !important; }

    /* Health Topics */
    html[data-bs-theme="dark"] .health-topics-wrapper { background: linear-gradient(135deg, #1a1d21 0%, #242628 100%) !important; }
    html[data-bs-theme="dark"] .page-header { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .header-subtitle { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .search-wrapper { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .search-input { color: #e4e6eb !important; background: transparent !important; }
    html[data-bs-theme="dark"] .search-input::placeholder { color: #9ca3af; }
    html[data-bs-theme="dark"] .search-icon { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .search-clear { background: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .search-clear:hover { background: #4b5262 !important; }
    html[data-bs-theme="dark"] .alphabet-filter { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .filter-label { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .filter-letter { background: #2d3136 !important; color: #e4e6eb !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .filter-letter:hover,
    html[data-bs-theme="dark"] .filter-letter.active { background: var(--theme-color-primary) !important; color: #fff !important; }
    html[data-bs-theme="dark"] .letter-group { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .letter-header { border-bottom-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .letter-title,
    html[data-bs-theme="dark"] .letter-count { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .topic-card { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .topic-card:hover { background: #2d3136 !important; border-color: #4b5262; }
    html[data-bs-theme="dark"] .topic-title,
    html[data-bs-theme="dark"] .topic-card a { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .topic-description,
    html[data-bs-theme="dark"] .topic-description.text-muted { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .empty-state h3,
    html[data-bs-theme="dark"] .empty-state p { color: #e4e6eb !important; }

    /* Forums */
    html[data-bs-theme="dark"] .forums-wrapper { background: #1a1d21 !important; }
    html[data-bs-theme="dark"] .forums-filters { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .search-bar input { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-card,
    html[data-bs-theme="dark"] .forums-list .card { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .forum-card h5,
    html[data-bs-theme="dark"] .forum-card h6,
    html[data-bs-theme="dark"] .forum-card a { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .forum-card .text-muted { color: #9ca3af !important; }

    /* Account & profile */
    html[data-bs-theme="dark"] section.middle { background: #1a1d21 !important; }
    html[data-bs-theme="dark"] .profile-card,
    html[data-bs-theme="dark"] .account-card,
    html[data-bs-theme="dark"] .card-body { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .card-title,
    html[data-bs-theme="dark"] .table { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .table thead th { background: #2d3136 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .table tbody td,
    html[data-bs-theme="dark"] .table tbody tr { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .table-striped tbody tr:nth-of-type(odd) { background: #2d3136 !important; }
    html[data-bs-theme="dark"] .dataTables_wrapper { color: #e4e6eb; }
    html[data-bs-theme="dark"] .dataTables_filter input { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .modal-content { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .modal-header { border-color: #3e4348; }
    html[data-bs-theme="dark"] .modal-title { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .modal-body { background: #242628 !important; color: #e4e6eb; }
    html[data-bs-theme="dark"] .modal-footer { border-color: #3e4348; }
    html[data-bs-theme="dark"] #previewModal .modal-content { background: #242628 !important; }
    html[data-bs-theme="dark"] #previewModal .modal-body,
    html[data-bs-theme="dark"] #previewModalBody { background: #2d3136 !important; }

    /* Publication list cards (search results) */
    html[data-bs-theme="dark"] .pub-card,
    html[data-bs-theme="dark"] .publication-item { background: #242628 !important; border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .pub-card h5,
    html[data-bs-theme="dark"] .pub-card h6,
    html[data-bs-theme="dark"] .pub-card a:not(.btn) { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .main_search { background: #242628 !important; color: #e4e6eb !important; border-color: #3e4348 !important; }
    html[data-bs-theme="dark"] .main_search::placeholder { color: #9ca3af; }

    /* Forum thread page */
    html[data-bs-theme="dark"] .thread-content,
    html[data-bs-theme="dark"] .comment-box { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] .breadcrumb { background: transparent !important; }
    html[data-bs-theme="dark"] .breadcrumb-item,
    html[data-bs-theme="dark"] .breadcrumb-item a { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .breadcrumb-item.active { color: #e4e6eb !important; }

    /* Search bar widget (page_search) */
    html[data-bs-theme="dark"] .widget_search,
    html[data-bs-theme="dark"] .single_widgets.widget_search { background: #242628 !important; border-color: #3e4348; }
    html[data-bs-theme="dark"] .pagination .page-link { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .pagination .page-item.active .page-link { background: var(--theme-color-primary) !important; border-color: var(--theme-color-primary); color: #fff !important; }
    html[data-bs-theme="dark"] .pagination .page-link:hover { background: #2d3136 !important; color: #fff !important; }
    html[data-bs-theme="dark"] .publication-list-card,
    html[data-bs-theme="dark"] .publication-list-card .card-body { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .publication-title-desktop,
    html[data-bs-theme="dark"] .publication-list-card .theme-cl,
    html[data-bs-theme="dark"] .publication-list-card .muted { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .publication-list-card .text-muted { color: #9ca3af !important; }
    /* Login modal (default theme) */
    html[data-bs-theme="dark"] #login .modal-content,
    html[data-bs-theme="dark"] #loginmodal .modal-content { background: #242628 !important; border-color: #3e4348; color: #e4e6eb; }
    html[data-bs-theme="dark"] #login .modal-header,
    html[data-bs-theme="dark"] #login .modal-body { border-color: #3e4348; }
    html[data-bs-theme="dark"] #login label,
    html[data-bs-theme="dark"] #login .form-control { color: #e4e6eb !important; }
</style>