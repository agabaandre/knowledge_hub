@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">RCC Dashboard</h3>
                    </div>
                    <div class="card-body">

                     @include('dashboards.partials.rcc_chart',['chart_url'=>url('admin/rccdashboards')])

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

@include('common.select2')
@include('dashboards.partials.rcc_chart_js') 

@endsection
