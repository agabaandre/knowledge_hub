@extends('layouts.app')

@section('styles')

@endsection

@section('content')
<!-- ======================= Publication Info ======================== -->
<div class="bg-light  rounded py-5" style="background-image: url({{asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        <div class="row">
            <div id="pivotContainer">The component will appear here</div>
        </div>
    </div>
</div>

@endsection
@section('sripts')
    <script src="https://cdn.flexmonster.com/flexmonster.js"></script>
    <script>
        let pivot = new Flexmonster({
            container: "pivotContainer",
            componentFolder: "https://cdn.flexmonster.com/",
            toolbar: true,
            report: {
                dataSource: {},
            },
            licenseKey: "XXXX-XXXX-XXXX-XXXX-XXXX"
        });
    </script>
@endsection

