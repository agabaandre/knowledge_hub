@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Pending Public Health Resources</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pending Public Health Resource</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pub-filters-card">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Pending Resources</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                    <button class="pub-filters-advanced-toggle" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                        <i class="fa fa-sliders mr-1"></i> Advanced Filters
                    </button>
                </div>
                <div class="pub-filters-card__body">
                    <form id="publicationFiltersForm" method="GET" action="{{ url('admin/publications/pending') }}">
                        @include('admin.publications.partials.primary_resource_filters')

                        <div id="advancedFilters" class="collapse pub-filters-advanced">
                            @include('admin.publications.partials.advanced_filters')
                        </div>

                        <div class="pub-filters-actions">
                            <a href="{{ url('admin/publications/pending') }}" class="pub-filters-btn pub-filters-btn--clear">
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
                <div class="card-header"><h3 class="card-title mb-0">Pending Publications</h3></div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

                    <form id="bulk-approval-form" method="POST" action="{{ route('admin.publications.bulk-approval') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-approval-action" value="">
                        <input type="hidden" name="rejected_reason" id="bulkRejectReasonHidden" value="">

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-success" id="bulkApproveBtn" disabled>
                                <i class="fa fa-check-circle mr-1"></i> Approve selected
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkRejectBtn" disabled>
                                <i class="fa fa-times-circle mr-1"></i> Reject selected
                            </button>
                            <span class="text-muted small" id="bulkSelectionCount"></span>
                        </div>

                        <div class="table-responsive">
                            <table id="publicationTable" class="table table-striped table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="selectAllPending"></th>
                                        <th width="60">#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Author</th>
                                        <th>Affiliation</th>
                                        <th>Member State</th>
                                        <th>Status</th>
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

    <div class="modal fade" id="bulkRejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject selected publications</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <textarea id="bulkRejectReason" class="form-control" rows="3" placeholder="Reason for rejection (optional)"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkRejectConfirmBtn">Reject selected</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.publications.partials.edit-modal')
    @include('admin.publications.partials.delete-modal')
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
    const n = $('#publicationTable .pending-pub-cb:checked').length;
    $('#bulkApproveBtn, #bulkRejectBtn').prop('disabled', n === 0);
    $('#bulkSelectionCount').text(n > 0 ? n + ' selected' : '');
    const total = $('#publicationTable .pending-pub-cb').length;
    $('#selectAllPending').prop('checked', n > 0 && n === total).prop('indeterminate', n > 0 && n < total);
}

$(function () {
    publicationTable = $('#publicationTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 20,
        order: [[1, 'desc']],
        ajax: {
            url: '{{ url('admin/publications/pending') }}',
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
            { data: 'date_created' },
            { data: 'actions', orderable: false }
        ],
        drawCallback: updateBulkState
    });

    $('#filterTitle').on('input', scheduleFilterReload);
    $('#publicationFiltersForm').on('change', 'select, #advancedFilters select, #advancedFilters input', scheduleFilterReload);
    if ($.fn.select2) $('#publicationFiltersForm select.select2').on('change.select2', scheduleFilterReload);

    $(document).on('change', '#selectAllPending', function () {
        $('#publicationTable .pending-pub-cb').prop('checked', this.checked);
        updateBulkState();
    });
    $(document).on('change', '.pending-pub-cb', updateBulkState);

    $('#bulkApproveBtn').on('click', function () {
        if (!$('#publicationTable .pending-pub-cb:checked').length) return;
        if (!confirm('Approve the selected publication(s)?')) return;
        $('#bulk-approval-action').val('approve');
        $('#bulk-approval-form').submit();
    });

    $('#bulkRejectBtn').on('click', function () {
        if (!$('#publicationTable .pending-pub-cb:checked').length) return;
        $('#bulkRejectReason').val('');
        $('#bulkRejectModal').modal('show');
    });

    $('#bulkRejectConfirmBtn').on('click', function () {
        $('#bulk-approval-action').val('reject');
        $('#bulkRejectReasonHidden').val($('#bulkRejectReason').val() || '');
        $('#bulkRejectModal').modal('hide');
        $('#bulk-approval-form').submit();
    });
});
</script>
@endsection
