@extends('admin.layouts.main')

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">RCC Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">RCC Dashboard</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        
                    </div>
                    <div class="card-body">

                     @include('dashboards.partials.rcc_chart', ['chart_url' => url('admin/rccdashboards')])

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
