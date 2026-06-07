@auth
    @if (is_admin())
        <a href="{{ route('admin.index') }}" class="dropdown-item">
            <i class="fa fa-th-large me-2"></i>
            <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.admin_panel">{{ __('ui_body.admin_panel') }}</span>
        </a>
    @endif
    <a href="{{ route('account.profile') }}" class="dropdown-item">
        <i class="fa fa-user me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.my_profile">{{ __('ui_body.my_profile') }}</span>
    </a>
    <a href="{{ route('account.publications') }}" class="dropdown-item">
        <i class="fa fa-list me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.publications">{{ __('ui_body.publications') }}</span>
    </a>
    <a href="{{ route('account.my-forums') }}" class="dropdown-item">
        <i class="fa fa-comments me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.my_forums">{{ __('ui_body.my_forums') }}</span>
    </a>
    <a href="{{ route('account.my-communities') }}" class="dropdown-item">
        <i class="fa fa-users me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.my_communities">{{ __('ui_body.my_communities') }}</span>
    </a>
    <a href="{{ route('account.favourites') }}" class="dropdown-item">
        <i class="fa fa-star me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.my_favourites">{{ __('ui_body.my_favourites') }}</span>
    </a>
    <a href="{{ route('account.chats') }}" class="dropdown-item">
        <i class="fa fa-comment-dots me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.my_chats">{{ __('ui_body.my_chats') }}</span>
    </a>
    <a href="{{ route('account.publish') }}" class="dropdown-item">
        <i class="fa fa-plus me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.publish_a_resource">{{ __('ui_body.publish_a_resource') }}</span>
    </a>
    <hr class="dropdown-divider">
    <a href="{{ url('logout') }}" class="dropdown-item text-danger">
        <i class="fa fa-sign-out-alt me-2"></i>
        <span class="khub-i18n-text notranslate" data-khub-i18n="ui_body.log_out">{{ __('ui_body.log_out') }}</span>
    </a>
@endauth
