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
	<link rel="icon" href="{{ asset('assets/images/fav.png')}}" type="image/x-icon" />
    <link href="{{ asset('frontend/css/styles.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/quiz.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/sharing.css')}}">
    <link rel="stylesheet" href="{{ asset('frontend/css/cookie-alert.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Wruczek/Bootstrap-Cookie-Alert@gh-pages/cookiealert.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('frontend/js/aos/dist/aos.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    
	<script src="{{ asset('frontend/js/jquery.min.js')}}"></script>

    <style>
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
     
        </style>

</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader"></div>
