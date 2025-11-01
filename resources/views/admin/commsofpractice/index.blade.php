@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
 <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card .card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card .card-body{padding:16px}
    /* Make description column wrap and limit width */
    #communities-table td:nth-child(3),
    #communities-table th:nth-child(3) {
        max-width: 300px;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
    }
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        #communities-table td:nth-child(3),
        #communities-table th:nth-child(3) {
            max-width: 200px;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }
    }
    @media (max-width: 576px) {
        #communities-table td:nth-child(3),
        #communities-table th:nth-child(3) {
            max-width: 150px;
        }
        #communities-table .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }
    }
 </style>
@endsection

@section('content')
<div class="row">
    <div class="card col-lg-12 af-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <strong>Communities of Practice</strong>
            <div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()"><i class="fa fa-plus mr-1"></i>Add Community</button>
            </div>
        </div>
        <div class="card-body text-left">
            @include('layouts.partials.alerts')
            <div class="table-responsive">
                <table id="communities-table" class="table table-striped table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">#</th>
                            <th style="min-width:150px;">Community</th>
                            <th style="min-width:200px;max-width:300px;">Description</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communities as $idx => $c)
                        <tr>
                            <td>{{ $communities->firstItem() + $idx }}</td>
                            <td class="font-weight-600">{{ $c->community_name }}</td>
                            <td class="text-muted" style="word-wrap: break-word; word-break: break-word; white-space: normal;">{!! truncate(strip_tags($c->description), 140) !!}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.commsofpractice.details', $c->id) }}" class="btn btn-outline-info" title="View Members"><i class="fa fa-users"></i></a>
                                    <button class="btn btn-outline-dark" onclick="openEditCommunity({{ $c->id }})" title="Edit"><i class="fa fa-edit"></i></button>
                                    @can('delete_publication_metadata')
                                    <button class="btn btn-outline-danger" onclick="openDeleteModal({{ $c->id }})" title="Delete"><i class="fa fa-trash"></i></button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="py-2">{{ $communities->links() }}</div>
        </div>
    </div>

    @include('admin.commsofpractice.partials.create-modal')
    @include('admin.commsofpractice.partials.delete-modal')

    @endsection

@section('scripts')
@include('partials.general.summernote')
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script>
$(function(){
    // Initialize DataTable with custom search
    var table = $('#communities-table').DataTable({
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
        order: [[1, 'asc']], // Sort by Community name
        columnDefs: [
            { orderable: false, targets: [3] }, // Disable sorting on Actions column
            { width: "60px", targets: [0] }, // # column
            { width: "150px", targets: [1] }, // Community column
            { width: "300px", targets: [2] }, // Description column (will wrap)
            { width: "180px", targets: [3] } // Actions column
        ],
        responsive: true, // Enable responsive mode
        scrollX: true, // Enable horizontal scrolling on small screens
        language: {
            search: "",
            searchPlaceholder: "Search communities by name or description...",
            lengthMenu: "Show _MENU_ communities per page",
            info: "Showing _START_ to _END_ of _TOTAL_ communities",
            infoEmpty: "No communities available",
            infoFiltered: "(filtered from _MAX_ total communities)",
            zeroRecords: "No matching communities found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        paging: false, // Disable DataTables pagination since we're using Laravel pagination
        info: false // Disable DataTables info since we're using Laravel pagination
    });
    
    // Customize search input styling
    $('.dataTables_filter input').addClass('form-control').css({
        'width': '300px',
        'display': 'inline-block',
        'margin-left': '10px'
    });
    
    // Add search icon to DataTables filter
    $('.dataTables_filter').prepend('<i class="fa fa-search" style="margin-right: 5px; color: #6c757d;"></i>');
    
    function initSN(){
        var $el = $('#description');
        if ($el.length && !$el.hasClass('summernote-sm')) { $el.addClass('summernote-sm'); }
    }
    initSN();
    $(document).on('shown.bs.modal', '#create-modal', function(){ initSN(); });
});

function openCreateModal(){
    $('#id').val('');
    $('#community_name').val('');
    if ($('#description').data('summernote')) { $('#description').summernote('code',''); } else { $('#description').val(''); }
    $('#create-modal').modal('show');
}

function openEditCommunity(id){
    const rows = @json($communities->items());
    const item = rows.find(x => x.id === id);
    if (!item) { return; }
    $('#id').val(item.id);
    $('#community_name').val(item.community_name || '');
    if ($('#description').data('summernote')) { $('#description').summernote('code', item.description || ''); } else { $('#description').val(item.description || ''); }
    $('#create-modal').modal('show');
}
</script>
@endsection