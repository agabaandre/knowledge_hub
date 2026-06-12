@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
    <style>
        .pub-stat-card--rejected { border-top-color: #dc3545; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Rejected Public Health Resources</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item"><a href="{{ url('admin/publications') }}">Manage Publications</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rejected Resources</li>
            </ol>
        </div>
    </div>

    @php $pubStats = $publication_stats ?? ['approved' => 0, 'pending' => 0, 'rejected' => 0]; @endphp
    <div class="row mb-3">
        <div class="col-md-4 col-sm-6 mb-2">
            <a href="{{ url('admin/publications') }}" class="card pub-stat-card h-100 shadow-sm text-decoration-none">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Approved Publications</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['approved'] ?? 0)) }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 mb-2">
            <a href="{{ url('admin/publications/pending') }}" class="card pub-stat-card pub-stat-card--pending h-100 shadow-sm text-decoration-none">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Pending Review</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['pending'] ?? 0)) }}</div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6 mb-2">
            <div class="card pub-stat-card pub-stat-card--rejected h-100 shadow-sm">
                <div class="card-body py-3">
                    <div class="pub-stat-card__label">Rejected</div>
                    <div class="pub-stat-card__value">{{ number_format((int) ($pubStats['rejected'] ?? 0)) }}</div>
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
                            <h3 class="pub-filters-card__title">Filter Rejected Resources</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                    <button class="pub-filters-advanced-toggle" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                        <i class="fa fa-sliders mr-1"></i> Advanced Filters
                    </button>
                </div>
                <div class="pub-filters-card__body">
                    <form id="publicationFiltersForm" method="GET" action="{{ url('admin/publications/rejected') }}">
                        @include('admin.publications.partials.primary_resource_filters')

                        <div id="advancedFilters" class="collapse pub-filters-advanced">
                            @include('admin.publications.partials.advanced_filters')
                        </div>

                        <div class="pub-filters-actions">
                            <a href="{{ url('admin/publications/rejected') }}" class="pub-filters-btn pub-filters-btn--clear">
                                <i class="fa fa-rotate-left"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card pub-list-card">
                <div class="card-header"><h3 class="card-title mb-0">Rejected Publications</h3></div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

                    <form id="bulk-approval-form" method="POST" action="{{ route('admin.publications.bulk-approval') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-approval-action" value="">

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-success" id="bulkReconsiderBtn" disabled>
                                <i class="fa fa-check-circle mr-1"></i> Reconsider selected
                            </button>
                            <span class="text-muted small" id="bulkSelectionCount"></span>
                        </div>

                        <div class="table-responsive">
                            <table id="publicationTable" class="table table-striped table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="selectAllRejected"></th>
                                        <th width="60">#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Author</th>
                                        <th>Affiliation</th>
                                        <th>Member State</th>
                                        <th>Status</th>
                                        <th>Rejection reason</th>
                                        <th>Rejected by</th>
                                        <th>Rejected on</th>
                                        <th>Date Created</th>
                                        <th>Actions</th>
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
@endsection

@section('scripts')
@include('common.select2')
@include('admin.publications.partials.datatable_assets')
<script>
let publicationTable = null;
let filterReloadTimer = null;

function collectFilterParams() {
    const params = {};
    $('#publicationFiltersForm').find('input, select, textarea').each(function () {
        const $el = $(this);
        const name = $el.attr('name');
        if (!name) return;
        const value = $el.val();
        if (value !== null && value !== '' && value !== 'all') params[name] = value;
    });
    return params;
}

function reloadPublicationTable() {
    if (publicationTable) publicationTable.ajax.reload();
}

function scheduleFilterReload() {
    clearTimeout(filterReloadTimer);
    filterReloadTimer = setTimeout(reloadPublicationTable, 350);
}

function updateBulkState() {
    const n = $('#publicationTable .rejected-pub-cb:checked').length;
    $('#bulkReconsiderBtn').prop('disabled', n === 0);
    $('#bulkSelectionCount').text(n > 0 ? n + ' selected' : '');
    const total = $('#publicationTable .rejected-pub-cb').length;
    $('#selectAllRejected').prop('checked', n > 0 && n === total).prop('indeterminate', n > 0 && n < total);
}

$(function () {
    publicationTable = $('#publicationTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 20,
        order: [[10, 'desc']],
        ajax: {
            url: '{{ url('admin/publications/rejected') }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectFilterParams());
            }
        },
        columns: [
            { data: 'checkbox', orderable: false },
            { data: 'index', orderable: true },
            { data: 'title' },
            { data: 'description' },
            { data: 'author', orderable: false },
            { data: 'affiliation', orderable: false },
            { data: 'member_state', orderable: false },
            { data: 'status', orderable: false },
            { data: 'rejected_reason', orderable: false },
            { data: 'rejected_by', orderable: false },
            { data: 'rejected_at', orderable: true },
            { data: 'date_created', orderable: true },
            { data: 'actions', orderable: false }
        ],
        drawCallback: updateBulkState
    });

    $('#filterTitle').on('input', scheduleFilterReload);
    $('#publicationFiltersForm').on('change', 'select, #advancedFilters select, #advancedFilters input', scheduleFilterReload);
    if ($.fn.select2) $('#publicationFiltersForm select.select2').on('change.select2', scheduleFilterReload);

    $(document).on('change', '#selectAllRejected', function () {
        $('#publicationTable .rejected-pub-cb').prop('checked', this.checked);
        updateBulkState();
    });
    $(document).on('change', '.rejected-pub-cb', updateBulkState);

    $('#bulkReconsiderBtn').on('click', function () {
        if (!$('#publicationTable .rejected-pub-cb:checked').length) return;
        if (!confirm('Reconsider the selected publication(s)? They will be approved and published.')) return;
        $('#bulk-approval-action').val('approve');
        $('#bulk-approval-form').submit();
    });
});
</script>
@endsection
