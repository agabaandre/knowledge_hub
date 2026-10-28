@php $menuIconsEnabled = settings()->menu_icons_enabled ?? 0; @endphp
<div class="nav-menus-wrapper notranslate" id="khub-nav-menus" style="transition-property: none;">
    <ul class="nav-menu">
        <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">@if($menuIconsEnabled)<i class="fa fa-home mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.home">{{ __('frontend_nav.home') }}</span></a></li>

        @if(function_exists('federation_consumer_enabled') && federation_consumer_enabled())
        <li class="{{ request()->is('federated*') ? 'active' : '' }}"><a href="{{ route('federation.browse') }}">@if($menuIconsEnabled)<i class="fa fa-globe-africa mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.network">{{ __('frontend_nav.network') }}</span></a></li>
        @endif

         <li class="categories {{ ( ((request()->is('records*') && !request()->has('tag'))) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*') ) ? 'active' : '' }}"><a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-compass mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.browse">{{ __('frontend_nav.browse') }}</span><span
                    class="submenu-indicator"></span></a>
            <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">

                <li>
                    <a
                        href="{{ url('/health-topics') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.health_topics">{{ __('frontend_nav.health_topics') }}</span></a>
                </li>

                @foreach ($data_categories as $category)
                    @php
                        $categoryI18nKey = 'frontend_nav.' . \App\Support\UiLocaleLabels::navCategoryTranslationKey((string) ($category->slug ?? ''));
                        $categoryLabel = \App\Support\UiLocaleLabels::navCategoryLabel($category);
                    @endphp
                    @if (\App\Support\DataCategoryAccess::userCanView($category))
                        @if ($category->is_special)
                            <li>
                                <a href="{{ url($category->url_path) }}?slug={{ $category->slug }}">
                                    <span class="khub-i18n-text" data-khub-i18n="{{ $categoryI18nKey }}">{{ $categoryLabel }}</span>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ url('/records') }}?category={{ $category->id }}">
                                    <span class="khub-i18n-text" data-khub-i18n="{{ $categoryI18nKey }}">{{ $categoryLabel }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach

                @if (states_enabled())
                    <li><a href="{{ url('countries') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.member_states">{{ __('frontend_nav.member_states') }}</span></a></li>
                @else
                    <li><a href="{{ url('adminunits') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.administrative_units">{{ __('frontend_nav.administrative_units') }}</span></a></li>
                @endif

            </ul>
        </li>


        @php
            $filteredTags = \App\Support\HealthEmergencyTags::from($tags ?? []);
        @endphp

        @if($filteredTags->isNotEmpty())
        <li class="categories has-mega-menu {{ request()->has('tag') ? 'active' : '' }}">
            <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-briefcase-medical mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.health_emergencies">{{ __('frontend_nav.health_emergencies') }}</span> <span class="submenu-indicator"></span></a>

            @include('layouts.partials.tags_menu')

        </li>

        <li class="categories health_emergencies" style="display: none;">
            <a href="javascript:void(0);"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.health_emergencies">{{ __('frontend_nav.health_emergencies') }}</span> <span class="submenu-indicator"></span></a>

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
        @endif
        <li class="categories {{ (request()->is('tools')|| (isset($staticLinks) && collect($staticLinks)->pluck('link')->contains(url()->current()))) ? 'active' : '' }}">
            <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-book mr-1"></i>@endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.key_links">{{ __('frontend_nav.key_links') }}</span> @if($menuIconsEnabled)<i class="fa fa-angle-right ml-1"></i>@endif<span class="submenu-indicator"></span></a>
            <ul class="nav-dropdown nav-submenu">

                @if(isset($staticLinks) && count($staticLinks))
                    @foreach($staticLinks as $link)
                        @php
                            $linkI18nKey = 'frontend_nav.' . \App\Support\UiLocaleLabels::navStaticLinkTranslationKey((int) $link->id);
                            $linkLabel = \App\Support\UiLocaleLabels::navStaticLinkLabel($link);
                        @endphp
                        <li>
                            <a href="{{ $link->link }}" @if($link->open_in_new_tab) target="_blank" @endif>
                                <span class="khub-i18n-text" data-khub-i18n="{{ $linkI18nKey }}">{{ $linkLabel }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
                <li><a href="{{ url('tools') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.tools">{{ __('frontend_nav.tools') }}</span></a></li>
            </ul>
        </li>

   @php
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
                <span class="khub-i18n-text" data-khub-i18n="frontend_nav.discussions">{{ __('frontend_nav.discussions') }}</span>
                @if($discussionsBadgeCount > 0)
                    <span class="menu-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px;">{{ $discussionsBadgeCount }}</span>
                @endif
                <span class="submenu-indicator"></span>
            </a>
            <ul class="nav-dropdown nav-submenu">
                 <li>
                     <a href="{{ url('forums') }}">
                         <span class="khub-i18n-text" data-khub-i18n="frontend_nav.forums">{{ __('frontend_nav.forums') }}</span>
                         @if($forumsBadgeCount > 0)
                             <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px; float: right;">{{ $forumsBadgeCount }}</span>
                         @endif
                     </a>
                 </li>
                <li>
                    <a href="{{ url('communities') }}">
                        <span class="khub-i18n-text" data-khub-i18n="frontend_nav.communities">{{ __('frontend_nav.communities') }}</span>
                        @if($communitiesBadgeCount > 0)
                            <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px; float: right;">{{ $communitiesBadgeCount }}</span>
                        @endif
                    </a>
                </li>

            </ul>
        </li>


        <li class="categories {{ request()->is('courses*') ? 'active' : '' }}">
            <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-graduation-cap mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.learning">{{ __('frontend_nav.learning') }}</span><span class="submenu-indicator"></span></a>
            <ul class="nav-dropdown nav-submenu">
                <li><a href="{{ url('courses') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.courses">{{ __('frontend_nav.courses') }}</span></a></li>
                <li><a href="{{ url('/') }}#home-events"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.events">{{ __('frontend_nav.events') }}</span></a></li>
            </ul>
        </li>

        <li class="categories {{ (request()->is('faqs') || request()->is('publications/content*')) ? 'active' : '' }}">
            <a href="javascript:void(0);">@if($menuIconsEnabled)<i class="fa fa-life-ring mr-1"></i> @endif <span class="khub-i18n-text" data-khub-i18n="frontend_nav.support">{{ __('frontend_nav.support') }}</span><span class="submenu-indicator"></span></a>
            <ul class="nav-dropdown nav-submenu">
                <li><a href="{{ url('faqs') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.faqs">{{ __('frontend_nav.faqs') }}</span></a></li>
                <li><a href="{{ url('publications/request-content') }}"><span class="khub-i18n-text" data-khub-i18n="frontend_nav.content_request">{{ __('frontend_nav.content_request') }}</span></a></li>
            </ul>
        </li>

        @include('layouts.partials.create_menu', ['menuIconsEnabled' => $menuIconsEnabled])

        @include('partials.account.authlinks', ['class' => 'mobileonly'])


    </ul>

    <ul class="nav-menu nav-menu-social align-to-right">
        @include('partials.account.authlinks')
    </ul>
</div>
