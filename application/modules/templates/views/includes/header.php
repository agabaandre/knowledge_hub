<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="<?php echo setting()->site_description; ?>" />
	<meta name="keywords" content="<?php echo setting()->seo_keywords; ?>">
	<meta name="author" content="Africa CDC" />
	<!-- Title -->
	<title><?php echo $title; ?></title>

	<!-- Favicon -->
	<link rel="icon" href="<?php echo base_url() ?>assets/img/brand/favicon.png" type="image/x-icon" />

	<!-- Icons css -->
	<link href="<?php echo base_url() ?>assets/css/icons.css" rel="stylesheet">

	<!-- Bootstrap css -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap/css/bootstrap.min.css">

	<!-- Right-sidemenu css -->
	<link href="<?php echo base_url() ?>assets/plugins/sidebar/sidebar.css" rel="stylesheet">

	<!-- Style css -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>assets/css/style.css">

	<!-- Colors css -->
	<link id="theme" rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>assets/css/colors/color.css">

	<!-- Horizontal css -->
	<link href="<?php echo base_url() ?>assets/css/horizontalmenu/horizontal-menu.css" rel="stylesheet">

	<!-- Skinmodes css -->
	<link href="<?php echo base_url() ?>assets/css/skin-modes.css" rel="stylesheet">

	<!-- Darktheme css -->
	<link href="<?php echo base_url() ?>assets/css/style-dark.css" rel="stylesheet">

	<!-- Map css-->
	<link href="<?php echo base_url() ?>assets/plugins/jqvmap/jqvmap.min.css" rel="stylesheet">

	<!-- Switcher css-->
	<link href="<?php echo base_url() ?>assets/switcher/css/switcher.css" rel="stylesheet">
	<link href="<?php echo base_url() ?>assets/switcher/demo.css" rel="stylesheet">

	<!-- Animations css -->
	<link href="<?php echo base_url() ?>assets/css/animate.css" rel="stylesheet">
	<script src="<?php echo base_url() ?>node_modules/jquery/dist/jquery.min.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/highcharts.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/highcharts-more.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/modules/solid-gauge.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/modules/exporting.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/modules/export-data.js"></script>
	<script src="<?php echo base_url() ?>node_modules/highcharts/modules/accessibility.js"></script>


	<style>
		.select2-close-mask {
			z-index: 2099;
		}

		.select2-dropdown {
			z-index: 3051;
		}

		/* File Input Styling */
		input[type="file"]::-webkit-file-upload-button {
			visibility: hidden;
		}

		input[type="file"]::before {
			content: 'Click to Upload';
			display: inline-block;
		}
	</style>

</head>

<body class="main-body">
