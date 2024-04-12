@extends('admin.layouts.main')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ count($publications)}} Publications</h5>
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Total Number of publications</p>
                    <a href="{{ url('admin/publications') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ count($authors)}} Resource Authors</h5>
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Number of resource conttibuting authors</p>
                    <a href="{{ url('admin/authors') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"> {{ count($experts) }} Experts</h5>
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Workforce Experts</p>
                    <a href="{{ url('admin/experts') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ count($forums) }} Forum Discussions</h5>
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Active Forum Discussions</p>
                    <a href="{{ url('admin/forums') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row charts"></div>

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <h4 class="card-title mb-4">Most Recent Resources</h4>
                </div>
                <div class="card-body">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- Filter By Title -->
                                <div class="form-group">
                                    <!-- <label>Filter By Title</label> -->
                                    <input type="text" class="form-control" id="filter_title" placeholder="Filter By Title">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Filter By Author -->
                                <div class="form-group">
                                    <!-- <label>Filter By Author</label> -->
                                    <input type="text" class="form-control" id="filter_author" placeholder="Filter By Author">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Filter By Description -->
                                <div class="form-group">
                                    <!-- <label>Filter By Description</label> -->
                                    <input type="text" class="form-control" id="filter_description" placeholder="Filter By Description">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Filter By Date range -->
                                <div class="form-group">
                                    <!-- <label>Filter By Date range</label> -->
                                    <input type="text" class="form-control" id="filter_date" placeholder="Filter By Date range">
                                </div>
                            </div>
                        </div>
                        <div-row>
                            <div class="col-md-12 text-right p-0">
                                <!-- Export Button -->
                                <button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
                                <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                                <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>
                            </div>
                        </div-row>
                    </div>

                    <!-- Datatable -->
                    <div class="table-responsive">
                        <table id="resource-table" class="table table-bordered table-striped table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="10%">Created</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($publications as $row)
                                <tr>
                                    <td>#</td>
                                    <td>{!! time_ago($row->created_at) !!}</td>
                                    <td>{!! $row->title !!}</td>
                                    <td>{!! truncate($row->description,20) !!}</td>
                                    <td>{!! truncate($row->author->name,20) !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<!-- Add Datatbales to this table -->
<script>
    $(document).ready(function() {
        console.log('Document raeady')
        $('#resource-table').DataTable({
            "searching": false,
            lengthChange: false,
        });

        $('.dataTables_wrapper').removeClass('form-inline');

        $.ajax({
            method:'GET',
            url:'<?php echo url('admin/metrics'); ?>',
            success:function(response){
                
                console.log('Metrics',response)
                $('.charts').html(response);
            },
            error:function(error){
                console.log(error);
            }
        })
    })
</script>

@endsection
