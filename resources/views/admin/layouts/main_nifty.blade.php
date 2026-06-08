<!DOCTYPE html>
@include('partials.theming.document_direction')
<html lang="{{ $htmlLang }}" dir="{{ $htmlDir }}" data-bs-theme="light" data-scheme="navy" class="{{ (settings()->menu_icons_enabled ?? 0) ? 'menu-icons-enabled' : 'menu-icons-disabled' }}{{ $isRtlUi ? ' khub-rtl-document' : '' }}">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <meta name="robots" content="noindex">
   <title>{{ $title ?? 'Admin' }} | {{ settings()->site_name ?? 'Knowledge Hub' }}</title>
   <link rel="icon" href="{{ site_favicon_url() }}" type="{{ site_favicon_mime() }}">
   <link rel="apple-touch-icon" sizes="180x180" href="{{ site_favicon_url() }}">

   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

   <link rel="stylesheet" href="{{ asset('theme1/assets/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('theme1/assets/css/nifty.min.css') }}">
   <link rel="stylesheet" href="{{ asset('frontend/css/khub-rtl.css') }}">
   <link rel="stylesheet" href="{{ asset('theme1/assets/css/demo-purpose/demo-icons.min.css') }}">
   <link rel="stylesheet" href="{{ asset('theme1/assets/css/demo-purpose/demo-settings.min.css') }}">
   <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   @include('layouts.theme1.partials.theme1_colors')
   @include('partials.theming.admin_body_font_size')
   <style>
   /* Admin logo: match front-end size (same logo_scale), remove constraints */
   .header__brand .brand-wrap { max-width: none !important; min-height: 0; }
   .header__brand .brand-wrap a { display: flex !important; align-items: center; }
   .header__brand .brand-wrap img { object-fit: contain; vertical-align: middle; }
   /* User profile placeholder: visible circle with FA icon when no photo */
   .user-avatar-placeholder {
       background: #f1f5f9 !important;
       color: #334155 !important;
       border: 1px solid #e2e8f0;
       display: inline-flex !important;
       align-items: center !important;
       justify-content: center !important;
   }
   </style>
   {{-- jQuery and Bootstrap before @yield('styles') so wizard_res smartWizard script has jQuery (e.g. admin publications create) --}}
   <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
   <script src="{{ asset('theme1/assets/vendors/popperjs/popper.min.js') }}"></script>
   <script src="{{ asset('theme1/assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
   @yield('styles')
</head>
<body class="out-quart">

<div id="root" class="root mn--max tm--expanded-hd">

   <!-- HEADER (full layout per Nifty demo) -->
   <header class="header">
      <div class="header__inner">
         <div class="header__brand">
            <div class="brand-wrap">
               @php $adminLogoPx = (int)(settings()->logo_scale ?? 80); $adminLogoPx = in_array($adminLogoPx, [40,50,60,70,80,100,120]) ? $adminLogoPx : 80; @endphp
               <a href="{{ url('admin/dashboard') }}" class="brand-img stretched-link d-flex align-items-center" style="min-height: {{ $adminLogoPx }}px;">
                  <img src="{{ settings()->logo ?? asset('theme1/assets/img/logo.svg') }}" alt="" class="{{ (settings()->header_logo_inverse ?? false) ? 'logo-inverse' : '' }}" style="height: {{ $adminLogoPx }}px; max-height: {{ $adminLogoPx }}px; width: auto;" onerror="this.style.display='none'">
               </a>
            </div>
         </div>
         <div class="header__content">
            <!-- Left: Nav toggler + search -->
            <div class="header__content-start">
               <button type="button" class="nav-toggler header__btn btn btn-icon btn-sm" aria-label="Nav Toggler">
                  <i class="psi-view-list"></i>
               </button>
               <div class="vr mx-1 d-none d-md-block"></div>
               <div class="header-searchbox">
                  <label for="admin-header-search" class="header__btn d-md-none btn btn-icon rounded-pill shadow-none border-0 btn-sm" type="button">
                     <i class="psi-magnifi-glass"></i>
                  </label>
                  <form class="searchbox searchbox--auto-expand searchbox--hide-btn input-group" action="{{ url('admin/publications') }}" method="get">
                     <input id="admin-header-search" class="searchbox__input form-control bg-transparent" type="search" name="q" placeholder="Search..." aria-label="Search">
                     <div class="searchbox__backdrop">
                        <button class="searchbox__btn header__btn btn btn-icon rounded shadow-none border-0 btn-sm" type="submit">
                           <i class="pli-magnifi-glass"></i>
                        </button>
                     </div>
                  </form>
               </div>
            </div>
            <!-- Right: Notifications, Dark mode, User, Sidebar toggler -->
            <div class="header__content-end ms-auto">
               <!-- Notifications -->
               <div class="dropdown">
                  <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-label="Notifications">
                     <span class="d-block position-relative">
                        <i class="psi-bell"></i>
                        @if(isset($total_pending_count) && $total_pending_count > 0)
                           <span class="badge badge-super rounded-pill p-1 bg-danger" style="background:#dc3545!important;color:#fff!important;">{{ $total_pending_count > 99 ? '99+' : $total_pending_count }}</span>
                        @endif
                     </span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end w-md-300px">
                     <div class="border-bottom px-3 py-2 mb-3">
                        <h5 class="mb-0">Notifications</h5>
                     </div>
                     <div class="list-group list-group-borderless">
                        @if(isset($pending_publications_count) && $pending_publications_count > 0)
                           <a href="{{ url('admin/publications/pending') }}" class="list-group-item list-group-item-action d-flex align-items-center mb-2">
                              <div class="flex-shrink-0 me-3"><i class="pli-file text-primary fs-2"></i></div>
                              <div class="flex-grow-1">
                                 <span class="h6 fw-normal d-block mb-0">Resources pending approval</span>
                                 <small class="text-body-secondary">{{ $pending_publications_count }} item(s)</small>
                              </div>
                           </a>
                        @endif
                        @can('view_content_requests')
                        @if(isset($pending_content_requests_count) && $pending_content_requests_count > 0)
                           <a href="{{ route('admin.content-requests.index') }}" class="list-group-item list-group-item-action d-flex align-items-center mb-2">
                              <div class="flex-shrink-0 me-3"><i class="pli-envelope text-warning fs-2"></i></div>
                              <div class="flex-grow-1">
                                 <span class="h6 fw-normal d-block mb-0">Content requests</span>
                                 <small class="text-body-secondary">{{ $pending_content_requests_count }} pending</small>
                              </div>
                           </a>
                        @endif
                        @endcan
                        @if(isset($pending_forums_count) && $pending_forums_count > 0)
                           <a href="{{ url('admin/forums') }}" class="list-group-item list-group-item-action d-flex align-items-center mb-2">
                              <div class="flex-shrink-0 me-3"><i class="pli-speech-bubble-3 text-info fs-2"></i></div>
                              <div class="flex-grow-1">
                                 <span class="h6 fw-normal d-block mb-0">Forums pending approval</span>
                                 <small class="text-body-secondary">{{ $pending_forums_count }} item(s)</small>
                              </div>
                           </a>
                        @endif
                        @if(isset($pending_cop_approvals_count) && $pending_cop_approvals_count > 0)
                           <a href="{{ url('admin/commsofpractice') }}" class="list-group-item list-group-item-action d-flex align-items-center mb-2">
                              <div class="flex-shrink-0 me-3"><i class="pli-user text-success fs-2"></i></div>
                              <div class="flex-grow-1">
                                 <span class="h6 fw-normal d-block mb-0">COP membership requests</span>
                                 <small class="text-body-secondary">{{ $pending_cop_approvals_count }} pending</small>
                              </div>
                           </a>
                        @endif
                        @if(empty($total_pending_count) || $total_pending_count == 0)
                           <div class="list-group-item text-body-secondary text-center py-4">No pending notifications</div>
                        @endif
                     </div>
                     @if(isset($total_pending_count) && $total_pending_count > 0)
                        <div class="text-center p-2 border-top">
                           <a href="{{ url('admin/publications/pending') }}" class="btn-link text-primary icon-link icon-link-hover small">View all</a>
                        </div>
                     @endif
                  </div>
               </div>
               <div class="vr mx-1 d-none d-md-block"></div>
               <!-- Color mode (dark / light / system) -->
               <div class="btn-group color-mode">
                  <button type="button" class="cm-selected dropdown-toggle hide-arrow header__btn btn btn-icon btn-sm" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Theme">
                     <span class="cm-selected-icon"></span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end w-100px min-w-0">
                     <button class="dropdown-item cm-switcher" type="button" data-color-mode="light">
                        <i class="psi-sun-2 fs-5 cm-selected-icon"></i>
                        <span class="ms-2">Light</span>
                     </button>
                     <button class="dropdown-item cm-switcher" type="button" data-color-mode="dark">
                        <i class="psi-half-moon fs-5 cm-selected-icon"></i>
                        <span class="ms-2">Dark</span>
                     </button>
                     <button class="dropdown-item cm-switcher" type="button" data-color-mode="system">
                        <i class="psi-contrast fs-5 cm-selected-icon"></i>
                        <span class="ms-2">System</span>
                     </button>
                  </div>
               </div>
               <div class="vr mx-1 d-none d-md-block"></div>
               <!-- User menu -->
               <div class="dropdown">
                  <button class="header__btn btn btn-icon btn-sm rounded-circle p-0 overflow-hidden" type="button" data-bs-toggle="dropdown" aria-label="User menu" style="width:36px;height:36px;">
                     @if(!empty(auth()->user()->photo))
                        <img src="{{ auth()->user()->photo }}" alt="" class="rounded-circle w-100 h-100" style="object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                        <span class="d-none align-items-center justify-content-center rounded-circle w-100 h-100 user-avatar-placeholder" style="font-size:1rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                     @else
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle w-100 h-100 user-avatar-placeholder" style="font-size:1rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                     @endif
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                     <div class="d-flex align-items-center border-bottom px-3 py-2">
                        <div class="flex-shrink-0 me-2">
                           @if(!empty(auth()->user()->photo))
                              <img src="{{ auth()->user()->photo }}" alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                              <span class="d-none align-items-center justify-content-center rounded-circle user-avatar-placeholder" style="width:40px;height:40px;font-size:1.1rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                           @else
                              <span class="d-inline-flex align-items-center justify-content-center rounded-circle user-avatar-placeholder" style="width:40px;height:40px;font-size:1.1rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                           @endif
                        </div>
                        <div class="flex-grow-1">
                           <h6 class="mb-0">{{ auth()->user()->name ?? 'User' }}</h6>
                           <small class="text-body-secondary">{{ auth()->user()->email ?? '' }}</small>
                        </div>
                     </div>
                     <a href="{{ route('admin.configure') }}" class="dropdown-item"><i class="fa fa-cog me-2"></i> Settings</a>
                     <a href="{{ url('/') }}" class="dropdown-item" target="_blank"><i class="fa fa-external-link-alt me-2"></i> View Site</a>
                     <hr class="dropdown-divider">
                     <a href="{{ url('logout') }}" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
                  </div>
               </div>
               <div class="vr mx-1 d-none d-md-block"></div>
               <button class="sidebar-toggler header__btn btn btn-icon btn-sm" type="button" aria-label="Sidebar">
                  <i class="fa fa-ellipsis-v"></i>
               </button>
            </div>
         </div>
      </div>
   </header>

   <!-- MAIN NAVIGATION (Sidebar) -->
   <nav id="mainnav-container" class="mainnav notranslate">
      <div class="mainnav__inner">
         <div class="mainnav__top-content scrollable-content pb-5">
            @include('admin.layouts.partials.nifty_sidebar')
         </div>
      </div>
   </nav>

   <!-- CONTENT -->
   <section id="content" class="content">
      <div class="content__boxed">
         <div class="content__wrap">
            @include('layouts.partials.alerts')
            @yield('content')
         </div>
      </div>
   </section>

</div>

<button id="scrollToTop" class="scroll-btn" tabindex="0" type="button" aria-label="Scroll to top"></button>

<script src="{{ asset('theme1/assets/js/nifty.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').not('.select2-hidden-accessible').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } });
        $('.select2Modal').not('.select2-hidden-accessible').select2({ width: '100%', dir: 'ltr', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
        $('select.js-example-basic-single').each(function() { if (!$(this).hasClass('select2-hidden-accessible')) { $(this).addClass('select2').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } }); } });
        $('select.form-control').each(function() { if (!$(this).hasClass('select2-hidden-accessible') && !$(this).hasClass('no-select2')) { $(this).addClass('select2').select2({ width: '100%', dir: 'ltr', minimumResultsForSearch: 0, placeholder: function() { return $(this).data('placeholder') || 'Select an option'; } }); } });
        $('.services').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumInputLength: 0, minimumResultsForSearch: 0 });
        $('.trainings').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
        $('.districts').not('.select2-hidden-accessible').select2({ width: '100%', dropdownParent: $('.modalselect'), minimumResultsForSearch: 0 });
    }
});
</script>
{{-- Bootstrap 5 compatibility: data-toggle/data-target (BS4) and jQuery .modal() --}}
<script>
(function() {
    function init() {
        var body = document.body;
        if (!body) return;
        // 1) Click delegate: [data-toggle="modal"] and data-target or href="#id"
        body.addEventListener('click', function(e) {
            var el = e.target.closest('[data-toggle="modal"]');
            if (!el) return;
            e.preventDefault();
            var id = el.getAttribute('data-target') || (el.getAttribute('href') || '').replace(/^[^#]*#/, '#');
            if (!id || id === '#') return;
            var modalEl = document.querySelector(id);
            if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var m = bootstrap.Modal.getOrCreateInstance(modalEl);
                m.show();
            }
        }, true);
        // 2) Collapse: copy data-toggle/data-target to data-bs-* for BS5
        document.querySelectorAll('[data-toggle="collapse"]').forEach(function(el) {
            if (!el.hasAttribute('data-bs-toggle')) { el.setAttribute('data-bs-toggle', 'collapse'); }
            var t = el.getAttribute('data-target');
            if (t && !el.hasAttribute('data-bs-target')) { el.setAttribute('data-bs-target', t); }
        });
        // 3) Modal close buttons: data-dismiss="modal" -> trigger BS5 hide
        document.querySelectorAll('[data-dismiss="modal"]').forEach(function(btn) {
            btn.setAttribute('data-bs-dismiss', 'modal');
            btn.setAttribute('aria-label', 'Close');
        });
        // 4) jQuery .modal('show'|'hide') bridge for Bootstrap 5
        if (typeof $ !== 'undefined' && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            if (!$.fn._modalBs5) {
                $.fn._modalBs5 = true;
                $.fn.modal = function(action) {
                    return this.each(function() {
                        var el = this;
                        if (!el || !el.classList || !el.classList.contains('modal')) return;
                        var m = bootstrap.Modal.getOrCreateInstance(el);
                        if (action === 'show') m.show();
                        else if (action === 'hide') m.hide();
                        else if (action === 'toggle') m.toggle();
                    });
                };
            }
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
</script>
@yield('scripts')
@stack('modal-scripts')
</body>
</html>
