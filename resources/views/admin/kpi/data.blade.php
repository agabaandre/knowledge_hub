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

                    <div class="col-md-12 text-right">
                        <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Indicator</a>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title">Search</label>
                            <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Filter by name" value="{{ @$search->term ?? ''}}">
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <!-- Export Button -->
                        <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                        <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                        <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>

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
                            <th>Description</th>



            </div>

        </div>

        <!-- Include delete-modal.php -->
        @include('admin.kpi.partials.delete-modal')
        @include('admin.kpi.partials.create-modal')

        @endsection