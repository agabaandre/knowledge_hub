<!doctype html>
<html lang="en">

<head>

    @include('layouts.partials.header_resources')

    <link rel="stylesheet" href="{{ asset('theme1/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('theme1/css/colors/theme1-colors.css') }}">

</head>

<body>

    <!-- Wrapper -->
    <div id="wrapper">

        @if (!@$is_home)
            <div class="clearfix" style="height: 82px; width: 100%;"></div>
        @endif
