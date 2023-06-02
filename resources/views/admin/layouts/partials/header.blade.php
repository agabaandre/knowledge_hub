<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="{{ @settings()->site_description }}" />
	<meta name="robots" content="noindex">
	<meta name="author" content="Africa CDC" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />

	<!-- Title -->
	<title>{{ @$title ?? 'Africa CDC Knowledge Hub' }}</title>

	<!-- Favicon -->
	<link rel="icon" href="{{ asset('assets/images/fav.png')}}" type="image/x-icon" />

	<!-- Icons css -->
	<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

	<!-- Bootstrap css -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">

	<!-- Right-sidemenu css -->
	<link href="{{ asset('assets/plugins/sidebar/sidebar.css') }}" rel="stylesheet">

	<!-- Style css -->
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/style.css') }}">

	<!-- Colors css -->
	<link id="theme" rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/colors/color.css') }}">

	<!-- Horizontal css -->
	<link href="{{ asset('assets/css/horizontalmenu/horizontal-menu.css') }}" rel="stylesheet">

	<!-- Skinmodes css -->
	<link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet">

	<!-- Darktheme css -->
	<link href="{{ asset('assets/css/style-dark.css') }}" rel="stylesheet">


	<!-- Animations css -->
	<link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
	<!-- JQuery min js -->
    <script src="{{  asset('assets/plugins/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/highcharts-more.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/modules/solid-gauge.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/modules/exporting.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/modules/export-data.js') }}"></script>
	<script src="{{ asset('assets/plugins/highcharts/modules/accessibility.js') }}"></script>
	<script src="{{ asset('assets/plugins/blockui/jquery.blockui.min.js') }}"></script>

    <!-- DataTables css -->
    <!-- <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet"> -->

    <!-- DataTables js -->
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

	<script>

		function showLoader(message="Please wait..."){
			$.blockUI({ message: message});
		}

		function hideLoader(){
			$.unblockUI();
		}
	</script>

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
