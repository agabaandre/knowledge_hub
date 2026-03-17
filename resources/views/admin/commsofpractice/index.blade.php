@extends(admin_layout('tabular'))

@section('styles')
 @include('common.table')
 @include('partials.general.summernote')
 <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card .card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card .card-body{padding:16px}
    
    /* Ensure red badges show in table and on buttons */
    #communities-table .badge-danger,
    .badge.badge-danger.badge-pill { background-color: #dc3545 !important; color: #fff !important; }

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
            <div class="d-flex align-items-center">
                <strong>Communities of Practice</strong>
            </div>
            <div class="d-flex align-items-center">
                @if(isset($pending_member_approvals_count) && $pending_member_approvals_count > 0)
                    <div class="dropdown nav-item mr-2" id="communities-notification-dropdown">
                        <a class="nav-link position-relative" href="#" data-toggle="dropdown" title="Communities with Pending Approvals">
                            <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span class="badge badge-danger badge-pill" style="position:absolute;top:-4px;right:-6px;min-width:20px;background:#dc3545!important;color:#fff!important;">{{ $pending_member_approvals_count }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width:320px;max-width:400px;max-height:500px;overflow-y:auto;">
                            <div class="p-3 border-bottom">
                                <h6 class="mb-2" style="font-weight: 600;">Communities with Pending Approvals</h6>
                                <small class="text-muted">{{ $pending_member_approvals_count }} members(s) awaiting approval</small>
                            </div>
                            <div class="list-group list-group-flush">
                                @if(isset($communities_with_pending) && $communities_with_pending->count() > 0)
                                    @foreach($communities_with_pending->take(10) as $item)
                                        <a href="{{ route('admin.commsofpractice.details', $item['community']->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">{{ $item['community']->community_name }}</h6>
                                                    <small class="text-muted">{{ $item['pending_count'] }} member(s) pending</small>
                                                </div>
                                                <span class="badge badge-warning badge-pill">{{ $item['pending_count'] }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="p-3 text-center text-muted">
                                        <small>No pending approvals</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()"><i class="fa fa-plus mr-1"></i>Add Community</button>
            </div>
        </div>
        <div class="card-body text-left">
            @include('layouts.partials.alerts')
            <form method="GET" action="{{ request()->url() }}" class="mb-3 d-flex flex-wrap align-items-center gap-2">
                <label class="mb-0 font-weight-medium">Search:</label>
                <input type="text" name="term" value="{{ request('term') }}" class="form-control" style="max-width: 280px;" placeholder="By community name or creator email..." aria-label="Search communities">
                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fa fa-search mr-1"></i>Search</button>
                @if(request()->filled('term'))
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </form>
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
                                    <a href="{{ route('admin.commsofpractice.details', $c->id) }}" class="btn btn-outline-info btn-sm" style="position: relative;">
                                        <i class="fa fa-users mr-1"></i>Group Members
                                        @if(isset($c->pending_members_count) && $c->pending_members_count > 0)
                                            <span class="badge badge-danger badge-pill" style="position: absolute; top: -4px; right: -6px; min-width: 18px; height: 18px; font-size: 0.7rem; padding: 2px 5px; background:#dc3545!important;color:#fff!important;">{{ $c->pending_members_count }}</span>
                                        @endif
                                    </a>
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
@include('common.select2')
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
    $(document).on('shown.bs.modal', '#create-modal', function(){ 
        initSN(); 
        // Initialize Select2 for tags dropdown if not already initialized
        var $tagsSelect = $('#tags\\[\\]');
        if ($tagsSelect.length && typeof $.fn.select2 !== 'undefined' && !$tagsSelect.hasClass('select2-hidden-accessible')) {
            $tagsSelect.select2({
                theme: 'bootstrap4',
                placeholder: 'Select Tags',
                allowClear: true,
                dropdownParent: $('#create-modal') // Ensure dropdown appears in modal
            });
        }
    });
});

function openCreateModal(){
    $('#id').val('');
    $('#community_name').val('');
    if ($('#description').data('summernote')) { $('#description').summernote('code',''); } else { $('#description').val(''); }
    $('#region_id').val('all').trigger('change');
    $('#country_id').val('').trigger('change');
    $('#organisation').val('');
    $('#department').val('');
    $('#is_public').prop('checked', true);
    // Clear tags selection
    if ($('#tags\\[\\]').length) {
        $('#tags\\[\\]').val(null).trigger('change');
    }
    $('#create-modal').modal('show');
}

function openEditCommunity(id){
    // Fetch community data with tags via AJAX
    $.ajax({
        url: '{{ url("admin/commsofpractice/get") }}',
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(item) {
            $('#id').val(item.id);
            $('#community_name').val(item.community_name || '');
            if ($('#description').data('summernote')) { 
                $('#description').summernote('code', item.description || ''); 
            } else { 
                $('#description').val(item.description || ''); 
            }
            
            // Set region and country
            if (item.region_id) {
                $('#region_id').val(item.region_id).trigger('change');
            } else {
                $('#region_id').val('all').trigger('change');
            }
            
            // Wait for country dropdown to update, then set country
            setTimeout(function() {
                if (item.country_id) {
                    $('#country_id').val(item.country_id).trigger('change');
                } else {
                    $('#country_id').val('').trigger('change');
                }
            }, 300);
            
            $('#organisation').val(item.organisation || '');
            $('#department').val(item.department || '');
            $('#is_public').prop('checked', item.is_public == 1 || item.is_public === true || item.is_public === null);
            
            // Set tags - wait a bit to ensure Select2 is ready
            setTimeout(function() {
                if ($('#tags\\[\\]').length && item.tags && Array.isArray(item.tags)) {
                    var tagIds = item.tags.map(function(tag) { 
                        return tag.id || (typeof tag === 'object' ? tag.tag_id : tag); 
                    });
                    $('#tags\\[\\]').val(tagIds).trigger('change');
                }
            }, 400);
            
            $('#create-modal').modal('show');
        },
        error: function() {
            alert('Error loading community data');
        }
    });
}

// Chained region/country dropdowns
$(document).ready(function() {
    // Get regions data with countries (from ViewComposer)
    var regionsData = @json($regions ?? []);
    var allCountries = [];
    
    // Build a map of all countries with their region_id for quick lookup
    if (regionsData && Array.isArray(regionsData)) {
        regionsData.forEach(function(region) {
            if (region.countries && Array.isArray(region.countries)) {
                region.countries.forEach(function(country) {
                    allCountries.push({
                        id: country.id,
                        name: country.name,
                        region_id: region.id
                    });
                });
            }
        });
    }
    
    // Handle region change - filter countries
    $(document).on('change', '#region_id', function() {
        var selectedRegion = $(this).val();
        var countrySelect = $('#country_id');
        var currentCountryId = countrySelect.val();
        
        // Clear existing options except the "All Countries" option
        countrySelect.find('option:not(:first)').remove();
        
        // If "all" is selected, show all countries
        if (selectedRegion === 'all' || selectedRegion === '' || selectedRegion === null) {
            // Add all countries
            allCountries.forEach(function(country) {
                countrySelect.append($('<option></option>')
                    .attr('value', country.id)
                    .text(country.name));
            });
        } else {
            // Filter countries by selected region
            var regionId = parseInt(selectedRegion);
            allCountries.forEach(function(country) {
                if (country.region_id === regionId) {
                    countrySelect.append($('<option></option>')
                        .attr('value', country.id)
                        .text(country.name));
                }
            });
        }
        
        // Reinitialize Select2 if it exists
        if (typeof $.fn.select2 !== 'undefined' && countrySelect.data('select2')) {
            countrySelect.trigger('change.select2');
        } else {
            countrySelect.trigger('change');
        }
        
        // Try to restore previous selection if it's still valid
        if (currentCountryId && countrySelect.find('option[value="' + currentCountryId + '"]').length) {
            countrySelect.val(currentCountryId).trigger('change');
        }
    });
});
</script>
@endsection