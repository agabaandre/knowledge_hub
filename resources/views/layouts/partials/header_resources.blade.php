<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- SEO Meta Tags --}}
@include('partials.seo.meta')

@php
    $__seoTitleSection = trim($__env->yieldContent('title'));
@endphp
<!-- Title: $pageTitle, @section('title'), or settings -->
<title>{{ strip_tags($pageTitle ?? ($__seoTitleSection !== '' ? $__seoTitleSection : (settings()->title ?? 'Africa CDC Knowledge Hub'))) }}</title>

{!! @settings()->analytics_script !!}
<!-- @notifyCss -->

<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

@if (empty($theme))
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
@endif

<link href="{{ asset('frontend/css/styles.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('frontend/css/quiz.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/sharing.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/css/cookie-alert.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Wruczek/Bootstrap-Cookie-Alert@gh-pages/cookiealert.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link rel="stylesheet" href="{{ asset('frontend/js/aos/dist/aos.css') }}">
<link rel="stylesheet" href="{{ asset('assets/webfont-medical-icons/css/wfmi-style.css') }}">
{{-- Theme (typography + colors) after frontend CSS so body/nav font settings override styles.css --}}
@include('partials.theming.colors')
@include('layouts.partials.content_preloader_styles')
@include('layouts.partials.rtl_stylesheet')
<script src="{{ asset('frontend/js/jquery.min.js') }}"></script>

<style>
    .VIpgJd-ZVi9od-vH1Gmf-ibnC6b div,
    .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:link div,
    .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:visited div,
    .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:active div {
        color: #000 !important;
    }

    .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:hover div {
        color: #FFF;
        background: --;
    }

    .sw-btn {
        padding: 0.775rem .95rem !important;
    }

    .main-search {
        height: 54px !important;
        padding: 10px 15px;
    }

    .goog-te-banner-frame.skiptranslate {
        display: none !important;
    }

    body {
        top: 0px !important;
        background-color: #f4f5f7 !important;
    }

    html {
        background-color: #f4f5f7 !important;
    }

    .goog-logo-link {
        display: none !important;
    }

    .trans-section {
        margin: 100px;
    }


    .favbtn a:hover {
        color: white !important;
    }

    .app-comment {
        padding: 10px;
        background-color: rgba(222, 224, 222, 0.5);
        border-radius: 8px;
        margin-top: 10px;
    }

    label {
        font-weight: bold !important;
    }

    .mobileonly {
        display: none;
    }

    @media only screen and (max-width:800px) {
        .mobileonly {
            display: block !important;
        }
    }

    @media only screen and (min-width:801px) {
        .mobileonly {
            display: none !important;
        }
    }

    .goog-te-banner-frame {
        display: none !important;
    }

    .flag-icon {
        font-size: 22px;
        border-radius: 20px;
    }

    .goog-te-gadget-icon {
        display: none !important;
        background-color: #FFF;

    }

    .VIpgJd-ZVi9od-ORHb-OEVmcd {
        display: none !important;
    }

    .goog-te-gadget-simple {
        border-radius: 4px;
    }

    /* Search/Spotlight area — gradient, or banner image blended with gradient overlay */
    .spotlight.custom-bg,
    .custom-bg.spotlight,
    .custom-bg,
    .home-spotlight,
    .theme1-spotlight {
        {!! \App\Support\SpotlightBackground::cssBackgroundRule() !!}
    }
    
    /* Ensure other .custom-bg uses (if any) don't conflict */
    body.custom-bg,
    html.custom-bg {
        background: #f4f5f7 !important;
    }

    /* Consistent background for all pages */
    .gray {
        background-color: #f4f5f7 !important;
        position: relative;
    }

    /* Ensure content areas have semi-transparent backgrounds for readability */
    .gray > .container,
    .gray .container {
        position: relative;
        z-index: 1;
    }

    /* User Avatar Fallback Styling */
    .user-avatar-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #e2e8f0;
        color: #718096;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .user-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    /* Nav profile: photo must match placeholder size (do not grow the header bar) */
    .nav-menu.nav-menu-social .kh-nav-profile-link {
        display: inline-flex !important;
        align-items: center;
        gap: 0.4rem;
        line-height: 1;
        vertical-align: middle;
    }

    .nav-menu.nav-menu-social .kh-nav-avatar.user-avatar-wrapper,
    .nav-menu.nav-menu-social .user-avatar-wrapper.kh-nav-avatar {
        width: 45px !important;
        height: 45px !important;
        min-width: 45px !important;
        min-height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        margin: 0;
        vertical-align: middle;
    }

    .nav-menu.nav-menu-social .user-avatar-img,
    .nav-menu.nav-menu-social img.user-avatar-img {
        width: 45px !important;
        height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        object-fit: cover;
        display: block;
        border-radius: 50%;
    }

    .nav-menu.nav-menu-social .kh-nav-label {
        line-height: 1.2;
        white-space: nowrap;
    }


    @media only screen and (min-device-width: 100px) and (max-device-width: 850px) {
        .custom-row {
            display: flex;
            justify-content: center;
        }

        .custom-row-item {
            max-width: 45.5%;
        }

    }

    .select2-selection__rendered {
        /*fixes jquery ui dropdown arrow merging with content */
        padding-left: 17px !important;
    }

    .nav-dropdown {
        z-index: 1000 !important;
    }

    .header-top {
        background: var(--theme-color-primary) !important;
        color: #FFFF;
    }

    .circular {
        min-width: 100px !important;
        min-height: 100px !important;
        max-width: 100px !important;
        max-height: 100px !important;
        border-radius: 50%;
    }

    .theme-primary {
        background: var(--theme-color-primary) !important;
        color: #fff;
        border-radius: 3px;
    }

    .theme-secondary {
        background: var(--theme-color-secondary) !important;
        color: #FFF;
        border-radius: 3px;
    }

    .text-primary {
        color: var(--theme-color-primary) !important;
    }

    .text-secondary {
        color: var(--theme-color-secondary) !important;
    }
</style>


{!! ReCaptcha::htmlScriptTagJsApi() !!}


<!--Start of Tawk.to Script-->
{{-- <script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/66ea02194cbc4814f7d9aa81/1i811gdbu';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script> --}}
<!--End of Tawk.to Script-->

<script>
    // Global fallback for user avatar images that fail to load
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all user avatar images that fail to load
        document.querySelectorAll('img.user-avatar-img, img[src*="/users/"], img[src*="user.jpg"]').forEach(function(img) {
            img.addEventListener('error', function() {
                // Hide the broken image
                this.style.display = 'none';
                
                // Try to find existing fallback
                var fallback = this.nextElementSibling;
                if (fallback && fallback.classList.contains('user-avatar-fallback')) {
                    fallback.style.display = 'inline-flex';
                } else {
                    // Create fallback if it doesn't exist
                    fallback = document.createElement('span');
                    fallback.className = 'user-avatar-fallback';
                    var imgWidth = this.style.width || this.getAttribute('width') || '25px';
                    var imgHeight = this.style.height || this.getAttribute('height') || '25px';
                    fallback.style.cssText = 'display:inline-flex; width: ' + imgWidth + '; height: ' + imgHeight + '; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%; border: 1px solid #cbd5e0;';
                    fallback.innerHTML = '<i class="fa fa-user"></i>';
                    this.parentElement.insertBefore(fallback, this.nextSibling);
                }
            });
        });
    });
</script>
