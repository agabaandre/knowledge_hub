@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
    <style>
        .pub-row-featured { background-color: #ecfdf3 !important; }
        .pub-row-inactive { background-color: #fef2f2 !important; }
        .pub-state-key { display:inline-flex; align-items:center; gap:8px; padding:4px 10px; border:1px solid #e2e8f0; border-radius:16px; font-size:12px; color:#334155; background:#fff; }
        .pub-state-swatch { width:14px; height:14px; border-radius:3px; border:1px solid #cbd5e1; }
        .pub-state-swatch-featured { background:#ecfdf3; }
        .pub-state-swatch-inactive { background:#fef2f2; }
        #publicationTable_wrapper .dataTables_length label { margin-bottom: 0; }
        @media (min-width: 768px) {
            #publicationTable .pub-col-checkbox { width: 2.5rem; min-width: 2.5rem; }
            #publicationTable .pub-col-index { width: 3rem; min-width: 3rem; }
            #publicationTable .pub-col-title { min-width: 160px; }
            #publicationTable .pub-col-description { min-width: 140px; }
            #publicationTable .pub-col-author { min-width: 100px; }
            #publicationTable .pub-col-affiliation { min-width: 120px; }
            #publicationTable .pub-col-member-state { min-width: 100px; }
            #publicationTable .pub-col-status { min-width: 88px; }
            #publicationTable .pub-col-date { min-width: 108px; white-space: nowrap; }
            #publicationTable .pub-col-moderator { min-width: 120px; }
            #publicationTable .pub-col-actions { min-width: 11.5rem; white-space: nowrap; vertical-align: middle; }
        }
        .pub-actions-group {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
            max-width: 100%;
        }
        .pub-actions-group .btn { padding: 0.25rem 0.45rem; }
        #publicationTable .pub-title-link {
            word-break: break-word;
            overflow-wrap: anywhere;
        }
        #publicationTable thead th {
            white-space: normal !important;
            line-height: 1.25;
            font-size: 0.72rem;
        }
        .pub-desc-preview {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--theme-color-primary, #119A48) !important;
            text-decoration: none !important;
            white-space: nowrap;
        }
        .pub-desc-preview:hover {
            text-decoration: underline !important;
        }
        #pubDescriptionPreviewBody {
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.6;
            color: #334155;
        }
        .pub-stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0;
            border-top: 3px solid var(--theme-color-primary, #119A48);
        }
        .pub-stat-card--pending { border-top-color: #d97706; }
        .pub-stat-card--featured { border-top-color: #eab308; }
        .pub-stat-card--inactive { border-top-color: #64748b; }
        .pub-stat-card__label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .pub-stat-card__value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="page-title">Manage Publications</h1>
            @if(isset($pending_publications_count) && $pending_publications_count > 0)
                <div class="dropdown nav-item">
                    <a class="nav-link position-relative" href="{{ url('admin/publications/pending') }}" title="Pending Publications">
                        <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="badge badge-danger badge-pill" style="position:absolute;top:-4px;right:-6px;min-width:20px;">{{ $pending_publications_count }}</span>
                    </a>
                </div>
            @endif
        </div>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Publications</li>
            </ol>
        </div>
    </div>

    @php $pubStats = $publication_stats ?? ['approved' => 0, 'pending' => 0, 'featured' => 0, 'inactive' => 0]; @endphp
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card pub-stat-card h-100 shadow-sm">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Approved Publications</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['approved'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <a href="{{ url('admin/publications/pending') }}" class="card pub-stat-card pub-stat-card--pending h-100 shadow-sm text-decoration-none">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Pending Review</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['pending'] ?? 0)) }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card pub-stat-card pub-stat-card--featured h-100 shadow-sm">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Featured</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['featured'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
            <div class="card pub-stat-card pub-stat-card--inactive h-100 shadow-sm">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Inactive / Unpublished</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['inactive'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pub-filters-card">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Publications</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                    <button class="pub-filters-advanced-toggle" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                        <i class="fa fa-sliders mr-1"></i> Advanced Filters
                    </button>
                </div>
                <div class="pub-filters-card__body">
                    <form id="publicationFiltersForm" method="GET" action="{{ url('admin/publications') }}" class="mb-0">
                        @include('admin.publications.partials.primary_resource_filters')

                        <div id="advancedFilters" class="collapse pub-filters-advanced">
                            @include('admin.publications.partials.advanced_filters')
                        </div>

                        <div class="pub-filters-actions">
                            <a href="{{ url('admin/publications') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearFiltersBtn">
                                <i class="fa fa-rotate-left"></i> Clear
                            </a>
                            <button type="button" id="exportButton" class="pub-filters-btn pub-filters-btn--export">
                                <i class="fa fa-download"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card pub-list-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Manage Publications</h3>
                        <div>
                            <a href="{{ url('admin/publications/create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Publication
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="bulk-actions-form" method="POST" action="{{ url('admin/publications/bulk-action') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-type" value="">

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <span class="text-muted small">Row color key:</span>
                            <span class="pub-state-key">
                                <span class="pub-state-swatch pub-state-swatch-featured"></span>
                                Featured
                            </span>
                            <span class="pub-state-key">
                                <span class="pub-state-swatch pub-state-swatch-inactive"></span>
                                Inactive / Unpublished
                            </span>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <select id="bulkActionSelect" class="form-control" style="width: auto; max-width: 260px;">
                                <option value="">Bulk Actions</option>
                                <option value="inactive">Inactive/Unpublish Selected</option>
                                <option value="featured">Mark as Featured</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="bulkActionButton" disabled>Apply</button>
                            <small class="text-muted">Select rows and an action in any order, then click Apply.</small>
                        </div>

                        <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Rows are shown as cards on small screens.</p>
                        <div class="publication-table-wrap kh-table-mobile-scroll">
                            <table id="publicationTable" data-kh-datatable="custom" class="table table-striped table-bordered table-hover w-100 kh-table-mobile-cards kh-table-mobile-cards--wide">
                                <thead>
                                    <tr>
                                        <th class="pub-col-checkbox" data-mobile-label="Select">
                                            <input type="checkbox" id="selectAllPublications">
                                        </th>
                                        <th class="pub-col-index">#</th>
                                        <th class="pub-col-title">Title</th>
                                        <th class="pub-col-description">Description</th>
                                        <th class="pub-col-author">Author</th>
                                        <th class="pub-col-affiliation">Affiliation</th>
                                        <th class="pub-col-member-state">Member State</th>
                                        <th class="pub-col-status">Status</th>
                                        <th class="pub-col-date">Date Created</th>
                                        <th class="pub-col-moderator">Approved/Rejected By</th>
                                        <th class="pub-col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.publications.partials.edit-modal')

    <div class="modal fade" id="pubDescriptionPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pubDescriptionPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pubDescriptionPreviewTitle">Description</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pubDescriptionPreviewBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkActionConfirmModal" tabindex="-1" role="dialog" aria-labelledby="bulkActionConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionConfirmModalLabel">Confirm Bulk Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="bulkActionConfirmText">Are you sure you want to perform this action?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBulkActionBtn">Yes, Proceed</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@include('common.select2')
@include('admin.publications.partials.datatable_assets')
<script>
let pendingBulkAction = null;
let publicationTable = null;
let filterReloadTimer = null;

function updateBulkActionButton() {
    const anyChecked = $('#publicationTable .publication-checkbox:checked').length > 0;
    const actionSelected = $('#bulkActionSelect').val();
    $('#bulkActionButton').prop('disabled', !anyChecked || !actionSelected);
}

function collectFilterParams() {
    const params = {};
    $('#publicationFiltersForm').find('input, select, textarea').each(function () {
        const $el = $(this);
        const name = $el.attr('name');
        if (!name || $el.attr('type') === 'button' || $el.attr('type') === 'submit') {
            return;
        }
        const value = $el.val();
        if (value !== null && value !== '' && value !== 'all') {
            params[name] = value;
        }
    });
    return params;
}

function reloadPublicationTable() {
    if (publicationTable) {
        publicationTable.ajax.reload();
    }
}

function scheduleFilterReload() {
    clearTimeout(filterReloadTimer);
    filterReloadTimer = setTimeout(reloadPublicationTable, 350);
}

function submitBulkAction(action) {
    pendingBulkAction = action;
    let actionText = '';
    if (action === 'inactive') actionText = 'unpublish (set inactive)';
    else if (action === 'featured') actionText = 'mark as featured';
    document.getElementById('bulkActionConfirmText').textContent = `Are you sure you want to ${actionText} the selected publications?`;
    $('#bulkActionConfirmModal').modal('show');
}

$(function () {
    publicationTable = $('#publicationTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        scrollX: false,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        pageLength: 20,
        order: [[1, 'desc']],
        ajax: {
            url: '{{ url('admin/publications') }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectFilterParams());
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'index', orderable: true, searchable: false },
            { data: 'title', orderable: true },
            { data: 'description', orderable: true },
            { data: 'author', orderable: false },
            { data: 'affiliation', orderable: false },
            { data: 'member_state', orderable: false },
            { data: 'status', orderable: true, searchable: false },
            { data: 'date_created', orderable: true, searchable: false },
            { data: 'moderator', orderable: false, searchable: false },
            { data: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            { targets: [0, 10], orderable: false },
            { targets: 0, className: 'pub-col-checkbox kh-mcard-select text-center' },
            { targets: 1, className: 'pub-col-index kh-mcard-hide' },
            { targets: 2, className: 'pub-col-title kh-mcard-primary' },
            { targets: 3, className: 'pub-col-description' },
            { targets: 4, className: 'pub-col-author' },
            { targets: 5, className: 'pub-col-affiliation' },
            { targets: 6, className: 'pub-col-member-state' },
            { targets: 7, className: 'pub-col-status' },
            { targets: 8, className: 'pub-col-date' },
            { targets: 9, className: 'pub-col-moderator' },
            { targets: 10, className: 'pub-col-actions kh-mcard-actions text-nowrap' }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading publications...',
            emptyTable: 'No publications match your filters.',
            zeroRecords: 'No matching publications found.'
        },
        createdRow: function (row, data) {
            if (data.DT_RowClass) {
                $(row).addClass(data.DT_RowClass);
            }
        },
        drawCallback: function () {
            $('#selectAllPublications').prop('checked', false);
            updateBulkActionButton();
        }
    });

    $('#publicationFiltersForm').on('submit', function (e) {
        e.preventDefault();
        reloadPublicationTable();
    });

    $('#filterTitle').on('input', scheduleFilterReload);

    $('#publicationFiltersForm').on('change', 'select.pub-filter-input, #advancedFilters select, #advancedFilters input', function () {
        reloadPublicationTable();
    });

    if ($.fn.select2) {
        $('#publicationFiltersForm select.select2').on('change.select2', scheduleFilterReload);
    }

    $('#clearFiltersBtn').on('click', function (e) {
        e.preventDefault();
        window.location.href = '{{ url('admin/publications') }}';
    });

    $(document).on('change', '#selectAllPublications', function () {
        $('#publicationTable .publication-checkbox').prop('checked', this.checked);
        updateBulkActionButton();
    });

    $(document).on('change', '.publication-checkbox', updateBulkActionButton);
    $('#bulkActionSelect').on('change', updateBulkActionButton);

    $(document).on('click', '.pub-desc-preview', function (e) {
        e.preventDefault();
        var title = $(this).attr('data-title') || 'Description';
        var description = $(this).attr('data-description') || '';
        try {
            description = JSON.parse(description);
        } catch (err) {
            // keep raw string fallback
        }
        $('#pubDescriptionPreviewTitle').text(title);
        $('#pubDescriptionPreviewBody').text(description);
        $('#pubDescriptionPreviewModal').modal('show');
    });

    function postPublicationToggle(url, id, confirmText, $btn) {
        if (!confirm(confirmText)) {
            return;
        }
        $btn.prop('disabled', true);
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id
            },
            success: function (response) {
                reloadPublicationTable();
            },
            error: function (xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Action failed. Please try again.';
                alert(message);
                $btn.prop('disabled', false);
            }
        });
    }

    $(document).on('click', '.pub-toggle-featured', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const isFeatured = String($btn.data('featured')) === '1';
        const text = isFeatured
            ? 'Remove this publication from featured?'
            : 'Mark this publication as featured?';
        postPublicationToggle('{{ url('admin/publications/toggle-featured') }}', $btn.data('id'), text, $btn);
    });

    $(document).on('click', '.pub-toggle-active', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const isActive = String($btn.data('active')) === '1';
        const text = isActive
            ? 'Unpublish this publication?'
            : 'Publish this publication?';
        postPublicationToggle('{{ url('admin/publications/toggle-active') }}', $btn.data('id'), text, $btn);
    });

    $('#bulkActionButton').on('click', function (e) {
        e.preventDefault();
        const action = $('#bulkActionSelect').val();
        if (!$('#publicationTable .publication-checkbox:checked').length) {
            alert('Please select at least one publication.');
            return false;
        }
        if (!action) {
            alert('Please select a bulk action.');
            return false;
        }
        submitBulkAction(action);
    });

    $('#confirmBulkActionBtn').on('click', function () {
        if (!pendingBulkAction) {
            return;
        }
        $('#bulk-action-type').val(pendingBulkAction);
        $('#bulkActionConfirmModal').modal('hide');
        document.getElementById('bulk-actions-form').submit();
    });
});
</script>
@endsection
