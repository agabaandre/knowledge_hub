@php
    $createForumUrl = auth()->check()
        ? route('forums.create')
        : route('login', ['redirect' => route('forums.create'), 'reason' => 'forum']);
    $createPublicationUrl = auth()->check()
        ? route('account.publish')
        : route('login', ['redirect' => route('account.publish'), 'reason' => 'publication']);
    $menuIconsEnabled = $menuIconsEnabled ?? (settings()->menu_icons_enabled ?? 0);
@endphp

<li class="categories {{ (request()->routeIs('account.publish') || request()->routeIs('account.publication') || request()->routeIs('forums.create')) ? 'active' : '' }}">
    <a href="javascript:void(0);">
        @if($menuIconsEnabled)<i class="fa fa-plus-circle mr-1"></i> @endif
        <span class="khub-i18n-text" data-khub-i18n="frontend_nav.create">{{ __('frontend_nav.create') }}</span>
        <span class="submenu-indicator"></span>
    </a>
    <ul class="nav-dropdown nav-submenu">
        <li>
            <a href="{{ $createForumUrl }}">
                <span class="khub-i18n-text" data-khub-i18n="frontend_nav.forum_discussion">{{ __('frontend_nav.forum_discussion') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ $createPublicationUrl }}">
                <span class="khub-i18n-text" data-khub-i18n="frontend_nav.resource_publication">{{ __('frontend_nav.resource_publication') }}</span>
            </a>
        </li>
    </ul>
</li>
