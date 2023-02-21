<!DOCTYPE html>
<html lang="en" xmlns="https://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="<?php echo setting()->site_description; ?>" />
	<meta name="keywords" content="<?php echo setting()->seo_keywords; ?>">
	<meta name="author" content="Africa CDC" />
	<!-- Title -->
	<title><?php echo 'Africa CDC Knowledge Hub - '.@$title ?? 'Africa CDC Knowledge Hub'; ?></title>

    <!-- Custom CSS -->
    <!-- Favicon -->
	<link rel="icon" href="<?php echo base_url() ?>assets/images/fav.png" type="image/x-icon" />

    <link href="<?php echo base_url(); ?>resources/css/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>resources/css/quiz.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>resources/css/sharing.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>resources/css/cookie-alert.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Wruczek/Bootstrap-Cookie-Alert@gh-pages/cookiealert.css">

	<script src="<?php echo base_url(); ?>resources/js/jquery.min.js"></script>

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
        

    </style>



</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader"></div>

    <span class="base_url" style="display:none;"><?php echo  base_url(); ?></span>