@extends('admin.layouts.main')

@section('styles')
@include('common.table')
@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12">
        <div class="card-header text-left">
            <h3 class="card-title float-left mt-2 mb-2">{{ $title ?? 'Indicators Data' }}</h3>
            <hr>
        </div>
        <!-- Card Header With Form Filters -->
        <div class="card-header">
            <form class="container-fluid">
                <div class="row">

                    <div class="col-md-12 text-right py-4">
                        <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Indicator Data</a>
                    </div>

                </div>

            </form>
        </div>
        <div class="card-body text-left">

            <!-- Data Table for KPI data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Country</th>
                            <th>Period</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($kpi_data as $data)
                        <tr>
                            <th>{{$data->kpi->name}}</th>
                            <th>{{($data->country)?$data->country->country_name:'N/A'}}</th>
                            <th>{{$data->period}}</th>
                            <th>{{$data->kpi_value}}</th>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                {{ $kpi_data->links() }}

            </div>

        </div>

        <!-- Include delete-modal.php -->
        @include('admin.kpi.partials.add-indicator-data-modal')

        @endsection