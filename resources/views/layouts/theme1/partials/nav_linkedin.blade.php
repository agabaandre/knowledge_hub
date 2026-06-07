@php
    $counts = get_menu_counts(auth()->id());
    $filteredTags = isset($tags) ? $tags->filter(fn ($tag) => $tag->is_health_emergency ?? false)->values() : collect();
    $federationEnabled = function_exists('federation_consumer_enabled') && federation_consumer_enabled();
    $menuIconsEnabled = (bool) (settings()->menu_icons_enabled ?? 0);
    $browseActive = (request()->is('records*') && ! request()->has('tag')) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*');
    $discussionsActive = request()->is('forums*') || request()->is('communities*');
    $networkActive = request()->is('federated*');
    $healthEmergenciesActive = request()->has('tag');
    $keyLinksActive = request()->is('tools') || (isset($staticLinks) && collect($staticLinks)->pluck('link')->contains(url()->current()));
    $learningActive = request()->is('courses*');
    $supportActive = request()->is('faqs') || request()->is('publications/content*') || request()->is('publications/request-content');
    $createActive = request()->routeIs('account.publish') || request()->routeIs('account.publication') || request()->routeIs('forums.create');
    $createForumUrl = auth()->check() ? route('forums.create') : route('login', ['redirect' => route('forums.create'), 'reason' => 'forum']);
    $createPublicationUrl = auth()->check() ? route('account.publish') : route('login', ['redirect' => route('account.publish'), 'reason' => 'publication']);
@endphp
<ul class="navbar-nav kh-linkedin-nav mx-auto">
    <li class="nav-item">
        <a class="nav-link kh-nav-item {{ request()->is('/') && ! $browseActive && ! $discussionsActive && ! $networkActive && ! $healthEmergenciesActive ? 'active' : '' }}" href="{{ url('/') }}">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-house kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.home') }}</span>
            </span>
        </a>
    </li>

    @if($federationEnabled)
    <li class="nav-item">
        <a class="nav-link kh-nav-item {{ $networkActive ? 'active' : '' }}" href="{{ route('federation.browse') }}">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-globe-africa kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.network') }}</span>
            </span>
        </a>
    </li>
    @endif

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $browseActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-compass kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.browse') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg">
            <li><a class="dropdown-item" href="{{ url('/health-topics') }}">{{ __('frontend_nav.health_topics') }}</a></li>
            @foreach ($data_categories ?? [] as $category)
                @if($category->is_special ?? false)
                    @auth
                        <li><a class="dropdown-item" href="{{ url($category->url_path ?? '') }}?slug={{ $category->slug }}">{{ $category->category_name }}</a></li>
                    @endauth
                @else
                    @if(strlen($category->required_permission ?? '') > 0)
                        @auth
                            @can($category->required_permission)
                                <li><a class="dropdown-item" href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
                            @endcan
                        @endauth
                    @else
                        <li><a class="dropdown-item" href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
                    @endif
                @endif
            @endforeach
            @if(states_enabled())
                <li><a class="dropdown-item" href="{{ url('countries') }}">{{ __('frontend_nav.member_states') }}</a></li>
            @else
                <li><a class="dropdown-item" href="{{ url('adminunits') }}">{{ __('frontend_nav.administrative_units') }}</a></li>
            @endif
        </ul>
    </li>

    @if($filteredTags->isNotEmpty())
    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $healthEmergenciesActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-briefcase-medical kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.health_emergencies') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu">
            @foreach($filteredTags as $tag)
                <li><a class="dropdown-item" href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a></li>
            @endforeach
        </ul>
    </li>
    @endif

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $keyLinksActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-book kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.key_links') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu">
            @if(isset($staticLinks) && count($staticLinks) > 0)
                @foreach($staticLinks as $link)
                    <li><a class="dropdown-item" href="{{ $link->link }}" @if($link->open_in_new_tab ?? false) target="_blank" rel="noopener" @endif>{{ $link->title }}</a></li>
                @endforeach
            @endif
            <li><a class="dropdown-item" href="{{ url('tools') }}">{{ __('frontend_nav.tools') }}</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $discussionsActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-comments kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.discussions') }}</span>
            </span>
            @if(($counts['total'] ?? 0) > 0)
                <span class="kh-nav-badge">{{ $counts['total'] > 9 ? '9+' : $counts['total'] }}</span>
            @endif
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ url('forums') }}">{{ __('frontend_nav.forums') }} @if(($counts['forums'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['forums'] }}</span>@endif</a></li>
            <li><a class="dropdown-item" href="{{ url('communities') }}">{{ __('frontend_nav.communities') }} @if(($counts['communities'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['communities'] }}</span>@endif</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $learningActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-graduation-cap kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.learning') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ url('courses') }}">{{ __('frontend_nav.courses') }}</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $supportActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-life-ring kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.support') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ url('faqs') }}">{{ __('frontend_nav.faqs') }}</a></li>
            <li><a class="dropdown-item" href="{{ url('publications/request-content') }}">{{ __('frontend_nav.content_request') }}</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $createActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            <span class="kh-nav-item-stack">
                @if($menuIconsEnabled)<i class="fas fa-circle-plus kh-nav-icon" aria-hidden="true"></i>@endif
                <span class="kh-nav-label">{{ __('frontend_nav.create') }}</span>
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ $createPublicationUrl }}">{{ __('frontend_nav.resource_publication') }}</a></li>
            <li><a class="dropdown-item" href="{{ $createForumUrl }}">{{ __('frontend_nav.forum_discussion') }}</a></li>
        </ul>
    </li>
</ul>
