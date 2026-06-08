<!DOCTYPE html>
@include('partials.theming.document_direction')
<html lang="{{ $htmlLang }}" dir="{{ $htmlDir }}" class="{{ $isRtlUi ? 'khub-rtl-document' : '' }}">

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
	<link rel="icon" href="{{ site_favicon_url() }}" type="{{ site_favicon_mime() }}" />
	<link rel="apple-touch-icon" sizes="180x180" href="{{ site_favicon_url() }}" />

	<!-- Icons css -->
	<link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

	<!-- Bootstrap css -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">

	<!-- Right-sidemenu css -->
	<link href="{{ asset('assets/plugins/sidebar/sidebar.css') }}" rel="stylesheet">

	<!-- Style css -->
	@if($isRtlUi)
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css-rtl/style.css') }}">
	@else
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/style.css') }}">
	@endif
	<link rel="stylesheet" href="{{ asset('frontend/css/khub-rtl.css') }}">

	<!-- Colors css -->
	<link id="theme" rel="stylesheet" type="text/css" media="all" href="{{ asset('assets/css/colors/color.css') }}">

	<!-- Horizontal css -->
	@if($isRtlUi)
	<link href="{{ asset('assets/css-rtl/horizontalmenu/horizontal-menu.css') }}" rel="stylesheet">
	@else
	<link href="{{ asset('assets/css/horizontalmenu/horizontal-menu.css') }}" rel="stylesheet">
	@endif

	<!-- Skinmodes css -->
	<link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet">

	<!-- Darktheme css -->
	<link href="{{ asset('assets/css/style-dark.css') }}" rel="stylesheet">

	<!-- Admin Select2 CSS - Modern dropdown styling -->
	<link href="{{ asset('assets/css/admin-select2.css') }}" rel="stylesheet">
    <!-- Lobibox Notifications CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />

	<!-- Animations css -->
	<link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
	<!-- Font Awesome for profile placeholder and icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	{{-- After all framework CSS so settings typography (primary font, body size, colors) overrides style.css / Bootstrap --}}
	@include('partials.theming.colors')
	@include('partials.theming.admin_body_font_size')
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
    @include('common.datatable_defaults')


    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <!-- jQuery UI for Datepicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-ui/themes/base/datepicker.css') }}">
    <script src="{{ asset('assets/plugins/jquery/jquery-ui.min.js') }}"></script>

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

		.select2-selection__rendered{
            /*fixes jquery ui dropdown arrow merging with content */
            padding-left: 17px!important;
        }
		 .goog-te-banner-frame {
            display: none !important;
        }
       .flag-icon {
        font-size: 22px; 
        border-radius:20px;
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
            background-position: center;
         }
		 body{
			top: 0px !important;
		 }


	</style>

</head>

<body class="main-body">
