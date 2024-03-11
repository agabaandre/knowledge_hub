<!DOCTYPE html>
<html lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="{{ @$setting->site_description }}" />
<meta name="keywords" content="{{ @$setting->seo_keywords }}">
<meta name="author" content="{{@$setting->site_name }}" />
<!-- Title -->
<title>{{@$setting->title }}</title>
{{ @$setting->analytics_script }}
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="icon" href="{{ settings()->favicon }}" type="image/x-icon" />
@include('partials.theming.colors')
<link href="{{ asset('frontend/css/styles.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('frontend/css/quiz.css')}}">
<link rel="stylesheet" href="{{ asset('frontend/css/sharing.css')}}">
<link rel="stylesheet" href="{{ asset('frontend/css/cookie-alert.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Wruczek/Bootstrap-Cookie-Alert@gh-pages/cookiealert.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('frontend/js/aos/dist/aos.css')}}">
<link rel="stylesheet" href="{{ asset('assets/webfont-medical-icons/css/wfmi-style.css')}}">

{{-- Captcha CSS --}}
<link href="{{ captcha_layout_stylesheet_url() }}" type="text/css" rel="stylesheet">


<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" type="text/javascript"></script>

<!-- CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" /> -->
<link rel="stylesheet" href="{{ asset('assets/plugins/tour/tour.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/smart_wizard_all.min.css')}}">

 <script src="{{ asset('frontend/js/jquery.min.js')}}"></script>

 <script>
        function initGoogleTranslate() {
            // Initialize the Google Translate widget
            googleTranslateElementInit();

            // Add event listener to ensure a language is selected
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "value") {
                        var dropdown = document.querySelector('.goog-te-combo');
                        if (dropdown.value === 'en') {
                            dropdown.setAttribute("required", true);
                        } else {
                            dropdown.removeAttribute("required");
                        }
                    }
                });
            });
            observer.observe(document.querySelector('.goog-te-combo'), {
                attributes: true
            });
        }

        // Function called by the Google Translate script
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,fr,ar,es,pt,sw',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false,
                disableAutoHover: true,
                showBanner: false
            }, 'google_translate_element');

            // Manually set the language of the Google Translate widget to English
            var dropdown = document.querySelector('.goog-te-combo');
            dropdown.value = 'en';
        }
    </script>

    <style>
        .VIpgJd-ZVi9od-vH1Gmf-ibnC6b div, .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:link div, .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:visited div, .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:active div{
          color:#000 !important;
        }
        .VIpgJd-ZVi9od-vH1Gmf-ibnC6b:hover div {
        color: #FFF;
        background: --;
        }

        .sw-btn{
        padding: 0.775rem .95rem!important;
        }
        .main-search{
        height: 54px !important;
        padding: 10px 15px;
        }
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .goog-logo-link {
            display: none !important;
        }

        .trans-section {
            margin: 100px;
        }


        .favbtn a:hover {
            color:white!important;
        }

        .app-comment{
            padding: 10px; 
            background-color:rgba(222, 224, 222,0.5); 
            border-radius:8px; 
            margin-top:10px;
        }

        label{
            font-weight: bold!important;
        }

        .mobileonly{
            display: none;
        }

        @media only screen and  (max-width:800px){
            .mobileonly{
                display: block!important;
            }
        }

        @media only screen and  (min-width:801px){
            .mobileonly{
                display: none!important;
            }
        }
        
        /* Hide the Google Translate top banner */
        .goog-te-banner-frame {
            display: none !important;
        }
            
        .goog-te-gadget-icon {
                display:none !important;
                background-color:#FFF;
               
         }
         .VIpgJd-ZVi9od-ORHb-OEVmcd{
            display:none !important;
         }
         .goog-te-gadget-simple{
            border-radius: 4px;
         }
     
         .custom-bg{
            background-color:var(--theme-color-primary)!important; 
            background-image:url('{{ settings()->spotlight_banner}}'); 
            background-repeat:no-repeat; 
            background-size:cover;
         }

        
         @media only screen and (min-device-width: 100px)  and (max-device-width: 850px) {
            .custom-row{
              display: flex;
              justify-content: center;
            }

            .custom-row-item{ 
                max-width: 45.5%;
            }

        }

        .select2-selection__rendered{
            /*fixes jquery ui dropdown arrow merging with content */
            padding-left: 17px!important;
        }

        .nav-dropdown{
            z-index: 1000!important;
        }
        .header-top{
           background: var(--theme-color-primary) !important;
           color:#FFFF;
        }

        
        
        </style>

</head>

<body onload="initGoogleTranslate()">
 
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader"></div>
