@php
    $navStyle = settings()->nav_style ?? 'colored';
    $logoScale = (int)(settings()->logo_scale ?? 80);
    $logoPx = in_array($logoScale, [40,50,60,70,80,100,120]) ? $logoScale : 80;
@endphp
<header id="header" class="header sticky-top border-bottom {{ $navStyle === 'light' ? 'nav-light' : 'nav-colored' }}">
    <div class="container">
        <nav class="p-0 navbar navbar-expand-lg navbar-light">
            <a href="{{ url('/') }}" class="navbar-brand p-0 m-0 d-flex align-items-center">
                @if(settings()->logo ?? null)
                    <img src="{{ settings()->logo }}" alt="{{ settings()->site_name }}" class="{{ (settings()->header_logo_inverse ?? false) ? 'logo-inverse' : '' }}" style="max-height: {{ $logoPx }}px; width: auto;">
                @else
                    <span class="fw-semibold text-body">{{ Str::limit(settings()->site_name ?? 'Knowledge Hub', 24) }}</span>
                @endif
            </a>

            <div class="d-flex gap-2 align-items-center ms-auto me-3 order-lg-3">
                <div class="d-none d-md-block">@include('layouts.partials.langselect')</div>
                @auth
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm btn-light rounded-circle p-0 overflow-hidden user-avatar-btn" type="button" data-bs-toggle="dropdown" aria-label="Account" style="width:45px;height:45px;">
                            @if(!empty(current_user()->photo))
                                <img src="{{ current_user()->photo }}" alt="" class="rounded-circle w-100 h-100" style="object-fit:cover;" onerror="this.style.display='none';var s=this.nextElementSibling;if(s){s.classList.remove('d-none');s.classList.add('d-inline-flex','user-avatar-placeholder-show');s.style.display='inline-flex';}">
                                <span class="user-avatar-placeholder d-none align-items-center justify-content-center rounded-circle w-100 h-100" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:1.125rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                            @else
                                <span class="user-avatar-placeholder d-inline-flex align-items-center justify-content-center rounded-circle w-100 h-100" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:1.125rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="px-3 py-2 border-bottom notranslate" translate="no">
                                <h6 class="mb-0">{{ ucwords(current_user()->name) }}</h6>
                                <small class="text-body-secondary">{{ current_user()->email ?? '' }}</small>
                            </div>
                            <div id="khub-account-dropdown-menu" class="notranslate">
                                @include('layouts.theme1.partials.account_dropdown_menu')
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ url('login') }}" class="btn btn-outline-primary btn-sm d-none d-md-inline-block notranslate">
                        <span class="khub-i18n-text" data-khub-i18n="ui_body.log_in">{{ __('ui_body.log_in') }}</span>
                    </a>
                    <a href="{{ url('register') }}" class="btn btn-primary btn-sm notranslate">
                        <span class="khub-i18n-text" data-khub-i18n="ui_body.sign_up">{{ __('ui_body.sign_up') }}</span>
                    </a>
                @endauth
                <button class="navbar-toggler border-0 py-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#theme1Navbar" aria-label="Menu">
                    <i class="fa fa-bars fs-5"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="theme1Navbar">
                <div id="khub-theme1-nav" class="notranslate w-100">
                    @include('layouts.theme1.partials.nav_linkedin')
                </div>
            </div>
        </nav>
    </div>
</header>
