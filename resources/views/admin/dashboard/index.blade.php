@extends('admin.layouts.main')

@section('content')
<style>
    /* Minimal AdminLTE-inspired widgets (reference: AdminLTE small-box) */
    .small-box{border-radius:12px;position:relative;display:block;margin-bottom:20px;box-shadow:0 1px 2px rgba(0,0,0,.05);color:#0f172a}
    .small-box>.inner{padding:16px 16px 14px 16px}
    .small-box h3{font-size:1.6rem;font-weight:800;margin:0 0 6px}
    .small-box p{font-size:.875rem;color:#0f172a99;margin:0}
    .small-box .icon{position:absolute;top:12px;right:16px;z-index:0;color:#00000020;font-size:42px}
    .small-box .small-box-footer{position:relative;display:flex;align-items:center;gap:6px;padding:10px 16px;border-top:1px solid #e2e8f0;border-bottom-left-radius:12px;border-bottom-right-radius:12px;color:#0f172aCC}
    .bg-gradient-primary{background:linear-gradient(135deg,#2563eb,#60a5fa);color:#fff}
    .bg-gradient-success{background:linear-gradient(135deg,#059669,#34d399);color:#fff}
    .bg-gradient-warning{background:linear-gradient(135deg,#d97706,#fbbf24);color:#fff}
    .bg-gradient-info{background:linear-gradient(135deg,#0ea5e9,#22d3ee);color:#fff}
    .bg-gradient-purple{background:linear-gradient(135deg,#7c3aed,#a78bfa);color:#fff}
    .bg-gradient-rose{background:linear-gradient(135deg,#e11d48,#fb7185);color:#fff}
    .bg-gradient-slate{background:linear-gradient(135deg,#475569,#94a3b8);color:#fff}
    .small-box .stat{font-weight:800;letter-spacing:.2px}
    .small-box a{color:inherit}
    .small-box a:hover{text-decoration:none;opacity:.95}
</style>
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<div class="container-fluid">
    <div class="row" style="row-gap:12px;">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('admin/publications') }}" class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3 class="stat">{{ $publications_count }}</h3>
                    <p>Publications</p>
                </div>
                <div class="icon"><i class="fa fa-pen"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('admin/authors') }}" class="small-box bg-gradient-success">
                <div class="inner">
                    <h3 class="stat">{{ $authors_count }}</h3>
                    <p>Resource Authors</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('admin/experts') }}" class="small-box bg-gradient-info">
                <div class="inner">
                    <h3 class="stat">{{ $experts_count }}</h3>
                    <p>Workforce Experts</p>
                </div>
                <div class="icon"><i class="fas fa-user-graduate"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('admin/forums') }}" class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3 class="stat">{{ $forums_count }}</h3>
                    <p>Forum Discussions</p>
                </div>
                <div class="icon"><i class="fab fa-forumbee"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>

        {{-- Others --}}
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="small-box bg-gradient-purple">
                <div class="inner">
                    <h3 class="stat">{{ $states_count }}</h3>
                    <p>Total Member States</p>
                </div>
                <div class="icon"><i class="fas fa-globe-africa"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('admin/logs/user') }}" class="small-box bg-gradient-rose">
                <div class="inner">
                    <h3 class="stat">{{ $visits_count }}</h3>
                    <p>Avg Daily Visits</p>
                </div>
                <div class="icon"><i class="fas fa-signal"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="small-box bg-gradient-slate">
                <div class="inner">
                    <h3 class="stat">{{ $admin_units_count }}</h3>
                    <p>Total Administrative Units</p>
                </div>
                <div class="icon"><i class="far fa-building"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ url('permissions/users') }}" class="small-box bg-gradient-success">
                <div class="inner">
                    <h3 class="stat">{{ $users_count }}</h3>
                    <p>Total Platform Users</p>
                </div>
                <div class="icon"><i class="fas fa-users-cog"></i></div>
                <div class="small-box-footer">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
    </div>

    <div class="row charts" style="row-gap:12px;"></div>

    {{-- Admin-only Dashboards List --}}
    @if(isset($dashboards) && count($dashboards))
    <div class="row mt-4" style="row-gap:12px;">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border-radius:12px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Admin Dashboards</h4>
                    <span class="text-muted small">{{ count($dashboards) }} available</span>
                </div>
                <div class="card-body">
                    <div class="row" style="row-gap:12px;">
                        @foreach($dashboards as $db)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100" style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="mb-1" style="font-weight:700;line-height:1.2;">{{ strip_tags(Str::limit($db->title, 60)) }}</h6>
                                    @if(!empty($db->theme))
                                        <div class="text-muted" style="font-size:.85rem;">{!! Str::limit($db->theme->description ?? '', 60) !!}</div>
                                    @endif
                                    <p class="mt-2 mb-3 text-muted" style="font-size:.9rem;">{{ Str::limit(strip_tags($db->description), 100) }}</p>
                                    <div class="mt-auto d-flex align-items-center justify-content-between">
                                        @php
                                            $statusText = $db->is_approved ? 'Approved' : ($db->is_rejected ? 'Rejected' : 'Pending');
                                            $statusClass = $db->is_approved ? 'badge-success' : ($db->is_rejected ? 'badge-danger' : 'badge-secondary');
                                            $url = !$db->is_embedded ? url('/admin/dashboards').'?resource='.$db->id : ($db->publication ?? '#');
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        <a href="{{ $url }}" target="{{ $db->is_embedded ? '_blank' : '_self' }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-chart-line mr-1"></i> Open
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                                    <td>{!! strip_tags($row->title) !!}</td>
                                    <td>{!! truncate(strip_tags($row->description), 20) !!}</td>
                                    <td>{!! truncate(strip_tags($row->author->name ?? ''), 20) !!}</td>
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
