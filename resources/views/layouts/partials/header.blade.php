<!-- Top header -->
<style>

/* Mega Menu Styling for mega Items */
.menu-container{
    min-width: 95%;
    max-width: 95%;
    display: flex;
    justify-content: center;
}
.mega-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    display: none;
    z-index: 10000;
    border-top: 3px solid var(--theme-color-primary, #119A48);
}

.mega-menu-container {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 28px 24px 32px;
}

.mega-menu-grid {
    display: flex;
    align-items: stretch;
    gap: 0;
    min-height: 380px;
}

.mega-menu-sidebar {
    width: 240px;
    flex-shrink: 0;
    padding-right: 24px;
    border-right: 1px solid #e8ecef;
}

.mega-sidebar-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #6b7280;
    margin: 0 0 12px;
    padding: 0 12px;
}

.mega-menu-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mega-sidebar-item {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
    border-radius: 6px;
    transition: background-color 0.15s ease;
}

.mega-sidebar-item.active {
    background-color: #f0fdf4;
}

.mega-sidebar-btn {
    flex: 1;
    display: block;
    padding: 10px 12px;
    color: #374151;
    background: none;
    border: none;
    text-align: left;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.35;
    cursor: pointer;
    border-radius: 6px;
    transition: color 0.15s ease;
}

.mega-sidebar-item.active .mega-sidebar-btn {
    color: var(--theme-color-primary, #119A48);
    font-weight: 600;
}

.mega-sidebar-btn:hover {
    color: var(--theme-color-primary, #119A48);
}

.mega-sidebar-link {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.15s ease, color 0.15s ease, background 0.15s ease;
}

.mega-sidebar-item:hover .mega-sidebar-link,
.mega-sidebar-item.active .mega-sidebar-link {
    opacity: 1;
}

.mega-sidebar-link:hover {
    color: var(--theme-color-primary, #119A48);
    background: #e5e7eb;
}

.mega-menu-content {
    flex: 1;
    min-width: 0;
    padding-left: 28px;
    display: flex;
    flex-direction: column;
}

.mega-grid-content {
    display: none;
    flex-direction: column;
    height: 100%;
}

.mega-grid-content.active {
    display: flex;
}

.mega-panel-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e8ecef;
}

.mega-panel-title {
    margin: 0 0 4px;
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    line-height: 1.3;
}

.mega-panel-subtitle {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
}

.mega-view-all {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    color: var(--theme-color-primary, #119A48);
    text-decoration: none;
    border: 1px solid var(--theme-color-primary, #119A48);
    border-radius: 6px;
    transition: background 0.15s ease, color 0.15s ease;
}

.mega-view-all:hover {
    background: var(--theme-color-primary, #119A48);
    color: #fff;
    text-decoration: none;
}

.mega-cards-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    flex: 1;
    align-items: stretch;
}

.mega-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 280px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
}

.mega-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
    text-decoration: none;
    color: inherit;
}

.mega-card-image {
    height: 140px;
    flex-shrink: 0;
    overflow: hidden;
    background: #f3f4f6;
}

.mega-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.35s ease;
}

.mega-card:hover .mega-card-image img {
    transform: scale(1.04);
}

.mega-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 14px;
    min-height: 140px;
}

.mega-card-title {
    flex: 1;
    margin: 0 0 10px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.45;
    color: var(--theme-color-primary, #119A48);
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.mega-card-meta {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 11px;
    color: #6b7280;
    line-height: 1.35;
}

.mega-card-theme {
    font-weight: 600;
    color: #374151;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.mega-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 240px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

.mega-empty p {
    margin: 0 0 12px;
}

/* Show mega menu on hover or click */
.has-mega-menu:hover .mega-menu,
.has-mega-menu.mega-open .mega-menu {
    display: block;
    animation: fadeInDown 0.25s ease;
}


.nav-menu > li:hover .nav-dropdown {
    display: block;
    animation: fadeInDown 0.3s ease;
}

.menu-badge {
    background: #ef4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: bold;
    margin-left: 4px;
    display: inline-block;
    vertical-align: middle;
    line-height: 1.2;
}

.menu-item-badge {
    background: #ef4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 0.75rem;
    font-weight: bold;
    float: right;
    margin-top: 2px;
    line-height: 1.2;
}

.nav-dropdown li a {
    position: relative;
    display: block;
}

.has-mega-menu .submenu-indicator {
    margin-left: 6px;
    display: inline-block;
    transition: all 0.3s;
}


.has-mega-menu .submenu-indicator:after {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-right: 2px solid #333;
    border-bottom: 2px solid #333;
    transform: rotate(45deg);
    position: relative;
    top: -10px!important;
}


@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Social menu */
.nav-menu-social {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.nav-menu-social li {
    margin-left: 5px;
}

.nav-menu-social li a {
    display: block;
    padding: 20px 10px;
    color: #333;
    text-decoration: none;
}

/* Active state styling */
.nav-menu > li > a { color: #333; border-bottom: 2px solid transparent; }
.nav-menu > li.active > a,
.nav-menu > li > a:hover { color: var(--theme-color-primary, #119A48); border-bottom-color: var(--theme-color-primary, #119A48); }

/* Adjust active indicator for mega menu (Health Emergencies) so the green bar doesn't touch the bottom border */
.nav-menu > li.has-mega-menu > a { position: relative; }
.nav-menu > li.has-mega-menu.active > a { border-bottom-color: transparent; }
.nav-menu > li.has-mega-menu.active > a::after,
.nav-menu > li.has-mega-menu > a:hover::after {
    content: '';
    position: absolute;
    left: 0; right: 0;
    bottom: 4px; /* lift the bar a bit so it doesn't touch the bottom edge */
    height: 2px;
    background: var(--theme-color-primary, #119A48);
    border-radius: 2px;
}

.search-btn {
    font-size: 18px;
}

/* Mobile styles */
.nav-toggle {
    display: none;
    cursor: pointer;
    font-size: 24px;
}

.mobile_nav {
    display: none;
}

/* Mobile language selector - only for phones (not tablets) */
.mobile-language-selector {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    display: none;
}

@media (max-width: 767px) {
    .nav-header {
        position: relative;
        padding-right: 60px; /* Make room for language selector */
    }
    
    .navigation-portrait .nav-header {
        position: relative;
        padding-right: 60px; /* Make room for language selector */
    }
    
    .mobile-language-selector {
        display: block !important;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 100;
    }
    
    .mobile-language-selector .menu-language-menu-container {
        width: auto;
        justify-content: flex-end;
        margin: 0;
    }
    
    .mobile-language-selector .language-selector-wrapper {
        margin-left: 0;
        margin-right: 0;
    }
    
    .mobile-language-selector .language-selector-btn {
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 4px;
        min-width: auto;
    }
    
    .mobile-language-selector .language-selector-btn .lang-code {
        display: none; /* Hide language code on very small screens, show only flag */
    }
    
    .mobile-language-selector .language-dropdown {
        right: 0;
        left: auto;
        min-width: 180px;
    }
}

@media (min-width: 768px) {
    .mobile-language-selector {
        display: none !important;
    }
}

@media (max-width: 1200px) {
    .mega-cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .mega-card {
        min-height: 260px;
    }
}
@media (max-width: 992px) {
    .has-mega-menu {
        display: none !important;
    }
    .health_emergencies{
        display: block !important;
    }
}

</style>

<!-- Top header -->
<div id="langauge-container" style="margin-bottom:-4px">
    <div class="p-3 bg-light">
        <div class="container" style="min-width: 90%;">
            <div class="row align-items-center">
                @php
                    $logoScale = (int)(settings()->logo_scale ?? 80);
                    $logoPx = in_array($logoScale, [40,50,60,70,80,100,120]) ? $logoScale : 80;
                @endphp
                <div class="col-lg-3 col-md-3 d-none d-md-block">
                    <div><a class="nav-brand" href="{{ url('/') }}">
                            @if(settings()->logo ?? null)
                            <img src="{{ settings()->logo }}" class="logo {{ (settings()->header_logo_inverse ?? false) ? 'logo-inverse' : '' }}" alt=""
                                style="max-height:{{ $logoPx }}px; width:auto; margin-bottom:-16px;">
                            @endif
                        </a>
                    </div>
                    <div class="mt-1 text-secondary">
                        <!-- slogan -->
                    </div>
                </div>
                <div class="col-lg-5 col-md-5 col-12 text-center">
                    <h3 style="color:black !important; font-weight:bold; margin-bottom: 7px;" class="notranslate">
                        {{ settings()->site_name }}</h3>
                    <h6 class="slogan fw-bold" style="font-size: 14px; margin-bottom: 7px; margin-left: 0;">
                        {{ settings()->slogan }}</h6>
                </div>
                <div class="col-lg-4 col-md-4 d-none d-md-block" style="padding-right: 10px; display: flex; align-items: center; justify-content: flex-end;">
                    @include('layouts.partials.langselect')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<div class="header modern-header justify-content-center" style="display: flex; justify-content: center;" >
    <div class="menu-container">
        <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
                <a class="nav-brand" href="{{ url('/') }}"></a>
                <div class="nav-toggle"></div>
                {{-- Mobile Language Selector (phones only, not tablets) --}}
                <div class="mobile-language-selector d-block d-md-none">
                    @include('layouts.partials.langselect')
                </div>
                <div class="mobile_nav">
                    <ul>
                        <li>
                            @guest
                                <a href="{{ route('login') }}" class="theme-cl fs-lg">
                                    <i class="fa fa-user"></i>
                                </a>
                            @else
                                <a href="{{ route('account.profile') }}" class="theme-cl fs-lg">
                                    <i class="fa fa-user"></i>
                                </a>
                            @endguest
                        </li>
                    </ul>
                </div>
            </div>
            
            @php $menuIconsEnabled = settings()->menu_icons_enabled ?? 0; @endphp
            <div class="nav-menus-wrapper notranslate" style="transition-property: none;">
                <ul class="nav-menu">
                    <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">@if($menuIconsEnabled)<i class="fa fa-home mr-1"></i> @endif {{ __('frontend_nav.home') }}</a></li>
                    
                     <li class="categories {{ ( ((request()->is('records*') && !request()->has('tag'))) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*') ) ? 'active' : '' }}"><a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-compass mr-1"></i> @endif {{ __('frontend_nav.browse') }}<span
                                class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">

                            <li>
                                <a
                                    href="{{ url('/health-topics') }}">{{ __('frontend_nav.health_topics') }}</a>
                            </li>

                            @foreach ($data_categories as $category)
                                @if ($category->is_special)
                                    @auth
                                        <li>
                                            <a
                                                href="{{ url($category->url_path) }}?slug={{ $category->slug }}">{{ $category->category_name }}</a>
                                        </li>
                                    @endauth
                                @else
                                    @if (strlen($category->required_permission) > 0)
                                        @auth
                                            @can($category->required_permission)
                                                <li>
                                                    <a
                                                        href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a>
                                                </li>
                                            @endcan
                                        @endauth
                                    @else
                                        <li>
                                            <a
                                                href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a>
                                        </li>
                                    @endif
                                @endif
                            @endforeach

                            @if (states_enabled())
                                <li><a href="{{ url('countries') }}">{{ __('frontend_nav.member_states') }}</a></li>
                            @else
                                <li><a href="{{ url('adminunits') }}">{{ __('frontend_nav.administrative_units') }}</a></li>
                            @endif

                        </ul>
                    </li>
                    
                    
                        @php
                        $filteredTags = $tags->filter(fn($tag) => $tag->is_health_emergency)->values();
                        @endphp

                    <li class="categories has-mega-menu {{ request()->has('tag') ? 'active' : '' }}">
                        <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-briefcase-medical mr-1"></i> @endif {{ __('frontend_nav.health_emergencies') }} <span class="submenu-indicator"></span></a>

                        @include('layouts.partials.tags_menu')
                        
                    </li>

                    <li class="categories health_emergencies" style="display: none;">
                        <a href="javascript:void(0);">{{ __('frontend_nav.health_emergencies') }} <span class="submenu-indicator"></span></a>

                        <ul class="nav-dropdown nav-submenu">
                            @foreach($filteredTags as $tag)
                            <li 
                              data-tag-id="{{ $tag->id }}" 
                              class="{{ $loop->first ? 'active' : '' }}"
                            >
                              <a href="{{ tag_records_url($tag) }}">
                                {{ $tag->tag_text }}
                              </a>
                            </li>
                            @endforeach
                       </ul>
                        
                    </li>
                    <li class="categories {{ (request()->is('tools')|| (isset($staticLinks) && collect($staticLinks)->pluck('link')->contains(url()->current()))) ? 'active' : '' }}">
                        <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-book mr-1"></i>@endif {{ __('frontend_nav.key_links') }} @if($menuIconsEnabled)<i class="fa fa-angle-right ml-1"></i>@endif<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            
                            @if(isset($staticLinks) && count($staticLinks))
                                @foreach($staticLinks as $link)
                                    <li><a href="{{ $link->link }}" @if($link->open_in_new_tab) target="_blank" @endif>{{ $link->title }}</a></li>
                                @endforeach
                            @endif
                            <li><a href="{{ url('tools') }}">{{ __('frontend_nav.tools') }}</a></li>
                        </ul>
                    </li>

               @php
                    // Use cached counts helper for better performance
                    $userId = auth()->check() ? auth()->id() : null;
                    $counts = get_menu_counts($userId);
                    
                    $totalForums = $counts['forums'];
                    $totalCommunities = $counts['communities'];
                    $discussionsBadgeCount = $counts['total'];
                    $forumsBadgeCount = $counts['forums'];
                    $communitiesBadgeCount = $counts['communities'];
                @endphp
                    <li class="categories {{ (request()->is('forums*') || request()->is('communities*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);">
                            @if($menuIconsEnabled)<i class="fa fa-comments mr-1"></i> @endif 
                            {{ __('frontend_nav.discussions') }}
                            @if($discussionsBadgeCount > 0)
                                <span class="menu-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px;">{{ $discussionsBadgeCount }}</span>
                            @endif
                            <span class="submenu-indicator"></span>
                        </a>
                        <ul class="nav-dropdown nav-submenu">
                             <li>
                                 <a href="{{ url('forums') }}">
                                     {{ __('frontend_nav.forums') }}
                                     @if($forumsBadgeCount > 0)
                                         <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px; float: right;">{{ $forumsBadgeCount }}</span>
                                     @endif
                                 </a>
                             </li>
                            <li>
                                <a href="{{ url('communities') }}">
                                    {{ __('frontend_nav.communities') }}
                                    @if($communitiesBadgeCount > 0)
                                        <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px; float: right;">{{ $communitiesBadgeCount }}</span>
                                    @endif
                                </a>
                            </li>
                           
                        </ul>
                    </li>


                    <li class="categories {{ request()->is('courses*') ? 'active' : '' }}">
                        <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-graduation-cap mr-1"></i> @endif {{ __('frontend_nav.learning') }}<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="{{ url('courses') }}">{{ __('frontend_nav.courses') }}</a></li>  
                        </ul>
                    </li>

                    <li class="categories {{ (request()->is('faqs') || request()->is('publications/content*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-life-ring mr-1"></i> @endif {{ __('frontend_nav.support') }}<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="{{ url('faqs') }}">{{ __('frontend_nav.faqs') }}</a></li> 
                            <li><a href="{{ url('publications/request-content') }}">{{ __('frontend_nav.content_request') }}</a></li>
                        </ul>
                    </li>

                    @auth
                    <li class="categories {{ (request()->routeIs('account.publish') || request()->routeIs('account.publication') || request()->routeIs('forums.create')) ? 'active' : '' }}">
                        <a href="javascript:void(0);">
                            @if($menuIconsEnabled)<i class="fa fa-plus-circle mr-1"></i> @endif 
                            {{ __('frontend_nav.create') }}
                            <span class="submenu-indicator"></span>
                        </a>
                        <ul class="nav-dropdown nav-submenu">
                            <li>
                                <a href="{{ route('forums.create') }}">
                                    {{ __('frontend_nav.forum_discussion') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('account.publish') }}">
                                    {{ __('frontend_nav.resource_publication') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endauth

                    @include('partials.account.authlinks', ['class' => 'mobileonly'])
                    
                    
                </ul>

                <ul class="nav-menu nav-menu-social align-to-right">
                    @include('partials.account.authlinks')
                </ul>
            </div>
        </nav>
    </div>
</div>
<div class="clearfix"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.has-mega-menu').forEach(function (item) {
        const trigger = item.querySelector(':scope > a');
        if (!trigger) return;

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = item.classList.contains('mega-open');
            document.querySelectorAll('.has-mega-menu').forEach(function (el) {
                el.classList.remove('mega-open');
            });
            if (!isOpen) {
                item.classList.add('mega-open');
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.has-mega-menu')) {
            document.querySelectorAll('.has-mega-menu').forEach(function (el) {
                el.classList.remove('mega-open');
            });
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.has-mega-menu').forEach(function (el) {
                el.classList.remove('mega-open');
            });
        }
    });
});
</script>