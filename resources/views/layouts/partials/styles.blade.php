<!DOCTYPE html>
<html lang="en" xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="{{ @$setting->site_description }}" />
<meta name="keywords" content="{{ @$setting->seo_keywords }}">
<meta name="author" content="Africa CDC" />
<!-- Title -->
<title>{{ 'Africa CDC Knowledge Hub - '.@$title ?? 'Africa CDC Knowledge Hub' }}</title>

    <!-- Custom CSS -->
    <!-- Favicon -->
<script type="text/javascript"
    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6GMM9ZKQTD"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'G-6GMM9ZKQTD');
</script>
<link rel="icon" href="{{ asset('assets/images/fav.png')}}" type="image/x-icon" />
<link href="{{ asset('frontend/css/styles.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('frontend/css/quiz.css')}}">
<link rel="stylesheet" href="{{ asset('frontend/css/sharing.css')}}">
<link rel="stylesheet" href="{{ asset('frontend/css/cookie-alert.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Wruczek/Bootstrap-Cookie-Alert@gh-pages/cookiealert.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ asset('frontend/js/aos/dist/aos.css')}}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/webfont-medical-icons/css/wfmi-style.css')}}">

<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/smartwizard@6/dist/css/smart_wizard_all.min.css" rel="stylesheet" type="text/css" />

 <link rel="stylesheet" href="{{ asset('assets/plugins/tour/tour.css')}}">

 <script src="{{ asset('frontend/js/jquery.min.js')}}"></script>

    <style>
         :root {
            --sw-border-color:  #eeeeee;
            --sw-toolbar-btn-color:  #ffffff;
            --sw-toolbar-btn-background-color:  #28a745;
            --sw-anchor-default-primary-color:  #82ba8f;
            --sw-anchor-default-secondary-color:  #b0b0b1;
            --sw-anchor-active-primary-color:  #28a745;
            --sw-anchor-active-secondary-color:  #ffffff;
            --sw-anchor-done-primary-color:  #82ba8f;
            --sw-anchor-done-secondary-color:  #fefefe;
            --sw-anchor-disabled-primary-color:  #82ba8f;
            --sw-anchor-disabled-secondary-color:  #dbe0e5;
            --sw-anchor-error-primary-color:  #dc3545;
            --sw-anchor-error-secondary-color:  #ffffff;
            --sw-anchor-warning-primary-color:  #ffc107;
            --sw-anchor-warning-secondary-color:  #ffffff;
            --sw-progress-color:  #28a745;
            --sw-progress-background-color:  #82ba8f;
            --sw-loader-color:  #28a745;
            --sw-loader-background-color:  #82ba8f;
            --sw-loader-background-wrapper-color:  rgba(255, 255, 255, 0.7);
          }
          .sw-btn{
            padding: 0.775rem .95rem!important;
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
            background-color:#7a7a7a!important; 
            background-image:url(<?php echo asset('frontend/img/overlay.png'); ?>); 
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
        
        </style>

</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader"></div>
