@extends('admin.layouts.main')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $publications_count }} Publications</h5>

                    <i class="fa fa-pen"></i>
                    @php
                    $percentage = ($publications_count / 10000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Total Number of publications</p>
                    <a href="{{ url('admin/publications') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $authors_count }} Resource Authors</h5>
                    <i class="fa fa-users"></i>
                    @php
                    $percentage = ($authors_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progress-bar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Number of resource contributing authors</p>
                    <a href="{{ url('admin/authors') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $experts_count }} Experts</h5>
                    <i class="fas fa-user-graduate"></i>
                    @php
$percentage = ($experts_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Workforce Experts</p>
                    <a href="{{ url('admin/experts') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $forums_count }} Forum Discussions</h5>
                    <i class="fab fa-forumbee"></i>
                    @php
$percentage = ($forums_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Active Forum Discussions</p>
                    <a href="{{ url('admin/forums') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>

        {{-- Others --}}
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $states_count }} Total Member States</h5>
                    <i class="fas fa-globe-africa"></i>
                    @php
$percentage = ($states_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Total Member States</p>
                    <a href="{{ url('admin/areas') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $visits_count }} Daily Visits</h5>
                    <i class="fas fa-signal"></i>
                    @php
$percentage = ($visits_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Daily Visits</p>
                    <a href="{{ url('admin/logs/user') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $admin_units_count }} Total Administrative Units</h5>
                    <i class="far fa-building"></i>
                    @php
$percentage = ($admin_units_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Total Administrative Units</p>
                    <a href="{{ url('admin/forums') }}" class="text-primary">View List</a>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $users_count }} Total Users</h5>
                    <i class="fas fa-users-cog"></i>
                    @php
$percentage = ($users_count / 1000) * 100;
                    @endphp
                    <div class="progress mt-1 mb-2" style="height: 5px;">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text">Total Platform Users</p>
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

                <div class="card-header">
                    <form class="container-fluid" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- Filter By Title -->
                                <div class="form-group">
                                    <!-- <label>Filter By Title</label> -->
                                    <input type="text" class="form-control" id="filter_title" placeholder="Filter By Title" name="search[title]">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <!-- Filter By Author -->
                                <div class="form-group">
                                    <!-- <label>Filter By Author</label> -->
                                    <input type="text" class="form-control" id="filter_author" placeholder="Filter By Author" name="search[author]">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <!-- Filter By Description -->
                                <div class="form-group">
                                    <!-- <label>Filter By Description</label> -->
                                    <input type="text" class="form-control" id="filter_description" placeholder="Filter By Description" name="search[description]">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <!-- Filter By Date range -->
                                <div class="form-group">
                                    <!-- <label>Filter By Date range</label> -->
                                    <input type="text" class="form-control" id="filter_date" placeholder="Filter By Date range" name="search[date]">
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

                <div class="card-body">
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
                                    <td>{!! truncate($row->description, 20) !!}</td>
                                    <td>{!! truncate($row->author->name, 20) !!}</td>
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
