@php
    $counts = get_menu_counts(auth()->id());
    $filteredTags = \App\Support\HealthEmergencyTags::from($tags ?? []);
    $federationEnabled = function_exists('federation_consumer_enabled') && federation_consumer_enabled();
    $menuIconsEnabled = (bool) (settings()->menu_icons_enabled ?? 0);
    $browseActive = (request()->is('records*') && ! request()->has('tag')) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*');
    $discussionsActive = request()->is('forums*') || request()->is('communities*');
    $networkActive = request()->is('federated*');
    $moreActive = request()->is('tools') || request()->is('faqs') || request()->is('courses*') || request()->is('publications/request-content');
@endphp
<ul class="navbar-nav kh-linkedin-nav mx-auto">
    <li class="nav-item">
        <a class="nav-link kh-nav-item {{ request()->is('/') && ! $browseActive && ! $discussionsActive && ! $networkActive ? 'active' : '' }}" href="{{ url('/') }}">
            @if($menuIconsEnabled)<i class="fa-regular fa-house kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.home">{{ __('frontend_nav.home') }}</span></span>
        </a>
    </li>

    @if($federationEnabled)
    <li class="nav-item">
        <a class="nav-link kh-nav-item {{ $networkActive ? 'active' : '' }}" href="{{ route('federation.browse') }}">
            @if($menuIconsEnabled)<i class="fa-regular fa-globe kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.network">{{ __('frontend_nav.network') }}</span></span>
        </a>
    </li>
    @endif

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $browseActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            @if($menuIconsEnabled)<i class="fa-regular fa-compass kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.browse">{{ __('frontend_nav.browse') }}</span></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg">
            <li><a class="dropdown-item" href="{{ url('/health-topics') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.health_topics">{{ __('frontend_nav.health_topics') }}</span></a></li>
            @foreach ($data_categories ?? [] as $category)
                @php
                    $categoryI18nKey = 'frontend_nav.' . \App\Support\UiLocaleLabels::navCategoryTranslationKey((string) ($category->slug ?? ''));
                    $categoryLabel = \App\Support\UiLocaleLabels::navCategoryLabel($category);
                @endphp
                @if(\App\Support\DataCategoryAccess::userCanView($category))
                    @if($category->is_special ?? false)
                        <li>
                            <a class="dropdown-item" href="{{ url($category->url_path ?? '') }}?slug={{ $category->slug }}">
                                <span class="khub-i18n-text" data-khub-i18n="{{ $categoryI18nKey }}">{{ $categoryLabel }}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="dropdown-item" href="{{ url('/records') }}?category={{ $category->id }}">
                                <span class="khub-i18n-text" data-khub-i18n="{{ $categoryI18nKey }}">{{ $categoryLabel }}</span>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
            @if($filteredTags->isNotEmpty())
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.health_emergencies">{{ __('frontend_nav.health_emergencies') }}</span></h6></li>
                @foreach($filteredTags as $tag)
                    <li><a class="dropdown-item" href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a></li>
                @endforeach
            @endif
            @if(states_enabled())
                <li><a class="dropdown-item" href="{{ url('countries') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.member_states">{{ __('frontend_nav.member_states') }}</span></a></li>
            @else
                <li><a class="dropdown-item" href="{{ url('adminunits') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.administrative_units">{{ __('frontend_nav.administrative_units') }}</span></a></li>
            @endif
            @if($federationEnabled)
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('federation.browse') }}"><i class="fa-regular fa-globe me-1"></i><span class="khub-i18n-text" data-khub-i18n="frontend_nav.partner_hubs">{{ __('frontend_nav.partner_hubs') }}</span></a></li>
            @endif
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $discussionsActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            @if($menuIconsEnabled)<i class="fa-regular fa-comments kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.discussions">{{ __('frontend_nav.discussions') }}</span></span>
            @if(($counts['total'] ?? 0) > 0)
                <span class="kh-nav-badge">{{ $counts['total'] > 9 ? '9+' : $counts['total'] }}</span>
            @endif
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ url('forums') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.forums">{{ __('frontend_nav.forums') }}</span> @if(($counts['forums'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['forums'] }}</span>@endif</a></li>
            <li><a class="dropdown-item" href="{{ url('communities') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.communities">{{ __('frontend_nav.communities') }}</span> @if(($counts['communities'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['communities'] }}</span>@endif</a></li>
        </ul>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link kh-nav-item dropdown-toggle {{ $moreActive ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" role="button">
            @if($menuIconsEnabled)<i class="fa-regular fa-ellipsis kh-nav-icon" aria-hidden="true"></i>@endif
            <span class="kh-nav-label"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.more">{{ __('frontend_nav.more') }}</span></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><h6 class="dropdown-header"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.learning">{{ __('frontend_nav.learning') }}</span></h6></li>
            <li><a class="dropdown-item" href="{{ url('courses') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.courses">{{ __('frontend_nav.courses') }}</span></a></li>
            <li><a class="dropdown-item" href="{{ url('/') }}#home-events"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.events">{{ __('frontend_nav.events') }}</span></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><h6 class="dropdown-header"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.support">{{ __('frontend_nav.support') }}</span></h6></li>
            <li><a class="dropdown-item" href="{{ url('faqs') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.faqs">{{ __('frontend_nav.faqs') }}</span></a></li>
            <li><a class="dropdown-item" href="{{ url('publications/request-content') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.content_request">{{ __('frontend_nav.content_request') }}</span></a></li>
            @if(isset($staticLinks) && count($staticLinks) > 0)
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.key_links">{{ __('frontend_nav.key_links') }}</span></h6></li>
                @foreach($staticLinks as $link)
                    @php
                        $linkI18nKey = 'frontend_nav.' . \App\Support\UiLocaleLabels::navStaticLinkTranslationKey((int) $link->id);
                        $linkLabel = \App\Support\UiLocaleLabels::navStaticLinkLabel($link);
                    @endphp
                    <li>
                        <a class="dropdown-item" href="{{ $link->link }}" @if($link->open_in_new_tab ?? false) target="_blank" rel="noopener" @endif>
                            <span class="khub-i18n-text" data-khub-i18n="{{ $linkI18nKey }}">{{ $linkLabel }}</span>
                        </a>
                    </li>
                @endforeach
            @endif
            <li><a class="dropdown-item" href="{{ url('tools') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.tools">{{ __('frontend_nav.tools') }}</span></a></li>
            @auth
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.create">{{ __('frontend_nav.create') }}</span></h6></li>
                <li><a class="dropdown-item" href="{{ route('account.publish') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.resource_publication">{{ __('frontend_nav.resource_publication') }}</span></a></li>
                <li><a class="dropdown-item" href="{{ url('forums/create') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.forum_discussion">{{ __('frontend_nav.forum_discussion') }}</span></a></li>
            @endauth
        </ul>
    </li>
</ul>
