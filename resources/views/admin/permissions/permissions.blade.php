@extends('admin.layouts.main')

@section('styles')
<link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endsection

@section('content')

 @include('common.table')
  <!-- PAGE-HEADER -->
  <div class="page-header">
            <h1 class="page-title">Permissions</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </div>
        </div>
   <!-- PAGE-HEADER END -->

<!-- Highlighted tabs -->
    <div class="row bg-white bg-white py-4 rounded">

        
    @include('admin.permissions.partials.add_permission_modal')

        <div class="col-md-12">
          
                <div class="row">
                <div class="col-md-9">
                    <h3 class="card-title mb-0">{{ __('auth.permissions') }}</h3>
                </div>
                <div class="col-md-3">

                    <a class="modal-effect btn btn-outline-primary d-block d-grid mb-3 float-right" data-effect="effect-rotate-bottom" data-toggle="modal" href="#addPermission"><i class="fa fa-plus-circle"></i> Add Permission</a>
                    </div>

                </div>


                    @if(count($permissions)>0)
                        <div class="table-responsive">
                            <table id="permissions-table" class="table table-striped table-bordered align-middle mb-0 perm-table">
                                <thead>
                                    <tr>
                                        <th style="width:6%">#</th>
                                        <th>Permission Name</th>
                                        <th>Group</th>
                                        <th>Description</th>
                                        <th class="text-center" style="width:150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $perm)
                                        @php
                                            $parts = explode('.', $perm->name);
                                            $group = strtoupper($parts[0] ?? 'GENERAL');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="perm-name">
                                                <strong>{{ $perm->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $group }}</span>
                                            </td>
                                            <td class="perm-desc">{{ $perm->description ?? '-' }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a class="btn btn-sm btn-outline-success" data-toggle="tooltip" data-original-title="Audit" href="{{ route('permissions.trail') }}">
                                                        <i class="fa fa-bar-chart"></i>
                                                    </a>
                                                    <a href="#perm{{$perm->id}}0" class="btn btn-sm btn-outline-info" data-toggle="modal" data-original-title="Edit">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('admin.permissions.partials.permission_edit_form_modal')
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No permissions found</p>
                        </div>
                    @endif

                </div>
        </div>
    </div>

 
    <!-- /highlighted tabs -->

@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script>
    $(function(){
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize DataTable with custom search
        var table = $('#permissions-table').DataTable({
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [4] } // Disable sorting on Actions column
            ],
            language: {
                search: "",
                searchPlaceholder: "Search permissions by name, group, or description...",
                lengthMenu: "Show _MENU_ permissions per page",
                info: "Showing _START_ to _END_ of _TOTAL_ permissions",
                infoEmpty: "No permissions available",
                infoFiltered: "(filtered from _MAX_ total permissions)",
                zeroRecords: "No matching permissions found"
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            drawCallback: function(){
                // Reinitialize tooltips after table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
        
        // Customize search input styling
        $('.dataTables_filter input').addClass('form-control').css({
            'width': '300px',
            'display': 'inline-block',
            'margin-left': '10px'
        });
        
        // Add search icon to DataTables filter
        $('.dataTables_filter').prepend('<i class="fa fa-search" style="margin-right: 5px; color: #6c757d;"></i>');
    });
</script>
@endsection


    