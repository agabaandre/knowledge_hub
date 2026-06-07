@php
    $menuIconsEnabled = (bool) (settings()->menu_icons_enabled ?? 0);
    $counts = get_menu_counts(auth()->id());
    $filteredTags = isset($tags) ? $tags->filter(fn ($tag) => $tag->is_health_emergency ?? false)->values() : collect();
    $discussionsBadgeCount = $counts['total'] ?? 0;
    $forumsBadgeCount = $counts['forums'] ?? 0;
    $communitiesBadgeCount = $counts['communities'] ?? 0;
@endphp

<li class="{{ request()->is('/') ? 'active' : '' }}">
    <a href="{{ url('/') }}">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-house kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.home') }}</span>
        </span>
    </a>
</li>

@if(function_exists('federation_consumer_enabled') && federation_consumer_enabled())
<li class="{{ request()->is('federated*') ? 'active' : '' }}">
    <a href="{{ route('federation.browse') }}">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-globe-africa kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.network') }}</span>
        </span>
    </a>
</li>
@endif

<li class="categories {{ ((request()->is('records*') && !request()->has('tag')) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*')) ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-compass kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.browse') }}</span>
        </span>
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
        <li><a href="{{ url('/health-topics') }}">{{ __('frontend_nav.health_topics') }}</a></li>
        @foreach ($data_categories ?? [] as $category)
            @if ($category->is_special ?? false)
                @auth
                    <li><a href="{{ url($category->url_path ?? '') }}?slug={{ $category->slug }}">{{ $category->category_name }}</a></li>
                @endauth
            @else
                @if (strlen($category->required_permission ?? '') > 0)
                    @auth
                        @can($category->required_permission)
                            <li><a href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
                        @endcan
                    @endauth
                @else
                    <li><a href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
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

<li class="categories has-mega-menu {{ request()->has('tag') ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-briefcase-medical kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.health_emergencies') }}</span>
        </span>
        <span class="submenu-indicator"></span>
    </a>
    @include('layouts.partials.tags_menu')
</li>

<li class="categories health_emergencies" style="display: none;">
    <a href="javascript:void(0);">{{ __('frontend_nav.health_emergencies') }} <span class="submenu-indicator"></span></a>
    <ul class="nav-dropdown nav-submenu">
        @foreach($filteredTags as $tag)
            <li data-tag-id="{{ $tag->id }}" class="{{ $loop->first ? 'active' : '' }}">
                <a href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a>
            </li>
        @endforeach
    </ul>
</li>

<li class="categories {{ (request()->is('tools') || (isset($staticLinks) && collect($staticLinks)->pluck('link')->contains(url()->current()))) ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-book kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.key_links') }}</span>
        </span>
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu">
        @if(isset($staticLinks) && count($staticLinks))
            @foreach($staticLinks as $link)
                <li><a href="{{ $link->link }}" @if($link->open_in_new_tab ?? false) target="_blank" rel="noopener" @endif>{{ $link->title }}</a></li>
            @endforeach
        @endif
        <li><a href="{{ url('tools') }}">{{ __('frontend_nav.tools') }}</a></li>
    </ul>
</li>

<li class="categories {{ (request()->is('forums*') || request()->is('communities*')) ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-comments kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.discussions') }}</span>
        </span>
        @if($discussionsBadgeCount > 0)
            <span class="kh-nav-badge">{{ $discussionsBadgeCount > 9 ? '9+' : $discussionsBadgeCount }}</span>
        @endif
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu">
        <li>
            <a href="{{ url('forums') }}">
                {{ __('frontend_nav.forums') }}
                @if($forumsBadgeCount > 0)
                    <span class="menu-item-badge">{{ $forumsBadgeCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ url('communities') }}">
                {{ __('frontend_nav.communities') }}
                @if($communitiesBadgeCount > 0)
                    <span class="menu-item-badge">{{ $communitiesBadgeCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>

<li class="categories {{ request()->is('courses*') ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-graduation-cap kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.learning') }}</span>
        </span>
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu">
        <li><a href="{{ url('courses') }}">{{ __('frontend_nav.courses') }}</a></li>
    </ul>
</li>

<li class="categories {{ (request()->is('faqs') || request()->is('publications/content*') || request()->is('publications/request-content')) ? 'active' : '' }}">
    <a href="javascript:void(0);">
        <span class="kh-nav-item-stack">
            @if($menuIconsEnabled)<i class="fas fa-life-ring kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label">{{ __('frontend_nav.support') }}</span>
        </span>
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu">
        <li><a href="{{ url('faqs') }}">{{ __('frontend_nav.faqs') }}</a></li>
        <li><a href="{{ url('publications/request-content') }}">{{ __('frontend_nav.content_request') }}</a></li>
    </ul>
</li>

@include('layouts.partials.create_menu', ['menuIconsEnabled' => $menuIconsEnabled])
