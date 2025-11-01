@extends('admin.layouts.tabular')

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
 <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card .card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card .card-body{padding:16px}
    
    /* Table organization improvements */
    #communities-table {
        width: 100% !important;
        table-layout: auto;
    }
    
    #communities-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #0f172a;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
    }
    
    #communities-table tbody td {
        padding: 12px 15px;
        vertical-align: top;
        border-top: 1px solid #f1f5f9;
    }
    
    /* Column width management */
    #communities-table th:nth-child(1),
    #communities-table td:nth-child(1) {
        width: 60px;
        text-align: center;
        white-space: nowrap;
    }
    
    #communities-table th:nth-child(2),
    #communities-table td:nth-child(2) {
        min-width: 216px; /* Increased by 20% from 180px */
        max-width: 300px; /* Increased by 20% from 250px */
        font-weight: 600;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        line-height: 1.5;
    }
    
    #communities-table th:nth-child(3),
    #communities-table td:nth-child(3) {
        min-width: 200px; /* Reduced from 300px */
        max-width: 300px; /* Reduced */
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        line-height: 1.5;
    }
    
    #communities-table th:nth-child(4),
    #communities-table td:nth-child(4) {
        width: 192px; /* Increased by 20% from 160px */
        text-align: center;
        white-space: normal;
    }
    
    /* Description column - truncate after 20 words */
    #communities-table td:nth-child(3) {
        text-align: left;
    }
    
    /* Action buttons */
    #communities-table .btn-group {
        display: flex;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    #communities-table .btn-group .btn {
        padding: 6px 12px;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    /* Striped rows */
    #communities-table tbody tr:nth-of-type(even) {
        background-color: #f9fafb;
    }
    
    #communities-table tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        #communities-table th:nth-child(3),
        #communities-table td:nth-child(3) {
            min-width: 200px;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }
    }
    @media (max-width: 576px) {
        #communities-table th:nth-child(3),
        #communities-table td:nth-child(3) {
            min-width: 150px;
        }
        #communities-table .btn-group .btn {
            padding: 4px 8px;
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
                            <th>#</th>
                            <th>Community</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communities as $idx => $c)
                        <tr>
                            <td class="text-center">{{ $communities->firstItem() + $idx }}</td>
                            <td><strong>{{ $c->community_name }}</strong></td>
                            <td>{!! \Illuminate\Support\Str::words(strip_tags($c->description), 20, '...') !!}</td>
                            <td>
                                <div class="btn-group-vertical btn-group-sm" role="group" style="gap: 4px;">
                                    <a href="{{ route('admin.commsofpractice.details', $c->id) }}" class="btn btn-outline-info btn-sm"><i class="fa fa-users mr-1"></i>Group Members</a>
                                    <button class="btn btn-outline-dark btn-sm" onclick="openEditCommunity({{ $c->id }})"><i class="fa fa-edit mr-1"></i>Edit</button>
                                    @can('delete_publication_metadata')
                                    <button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal({{ $c->id }})"><i class="fa fa-trash mr-1"></i>Delete</button>
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
        order: [[0, 'asc']], // Sort by # column (number) in ascending order
        columnDefs: [
            { orderable: false, targets: [3] }, // Disable sorting on Actions column
            { width: "60px", targets: [0], className: "text-center" }, // # column
            { width: "240px", targets: [1] }, // Community column (increased by 20%)
            { width: "300px", targets: [2] }, // Description column (reduced)
            { width: "192px", targets: [3], className: "text-center" }, // Actions column (increased by 20%)
        ],
        responsive: true, // Enable responsive mode
        scrollX: false, // Disable horizontal scroll for better organization
        autoWidth: true, // Allow table to adjust width
        language: {
            search: "",
            searchPlaceholder: "Search communities by name or description...",
            lengthMenu: "Show _MENU_ communities per page",
            info: "Showing _START_ to _END_ of _TOTAL_ communities",
            infoEmpty: "No communities available",
            infoFiltered: "(filtered from _MAX_ total communities)",
            zeroRecords: "No matching communities found"
        },
        dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
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