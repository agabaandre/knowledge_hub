@include('admin/layouts/partials/header')

@php
 $table_assets = config('uitable.js_asset_path');
 $theme = "blitzer";//"pepper-grinder";
@endphp


<link rel="stylesheet" type="text/css" media="screen" href="{{asset($table_assets)}}/themes/{{$theme}}/jquery-ui.custom.css"></link>
<link rel="stylesheet" type="text/css" media="screen" href="{{asset($table_assets)}}/jqgrid/css/ui.jqgrid.css"></link>
<style>
 #d_tg,.loading,.bottominfo{display: none!important;}
 #div_no_record_list1{
    display: none!important;
 }

 #div_no_record_list1::after{
    display: block;
    content: "No records"!important;
 }
</style>
<!-- <script src="{{asset($table_assets)}}jquery.min.js" type="text/javascript"></script> -->
<script src="{{asset($table_assets)}}/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="{{asset($table_assets)}}/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="{{asset($table_assets)}}/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

@yield('styles')
@include('admin/layouts/partials/preloader')

<!-- Page -->
<div class="page">

    @include("admin/layouts/partials/top_bar_main")
    @include("admin/layouts/partials/top_bar_mobile")
    @include("admin/layouts/partials/nav")

    <!-- Main Content -->
    <div class="main-content" style="margin-right: 20px; margin-left: 20px; margin-top: 20px;">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

</div>
<!-- Page closed -->
@include("admin/layouts/partials/footer")

@yield('scripts')


</body>

</html>