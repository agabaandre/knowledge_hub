@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
    <style>
        .card { border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem; }
        .card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; }
        .card-body { padding: 1.5rem; }
        .kpi-row-published { background-color: #ecfdf3 !important; }
        .kpi-row-recalled { background-color: #fffbeb !important; }
        #kpiIndicatorsTable_wrapper table.dataTable { table-layout: fixed !important; }
        #kpiIndicatorsTable .kpi-col-checkbox { width: 2.5rem; min-width: 2.5rem; }
        #kpiIndicatorsTable .kpi-col-index { width: 3rem; min-width: 3rem; }
        #kpiIndicatorsTable .kpi-col-title { width: 18%; }
        #kpiIndicatorsTable .kpi-col-description { width: 22%; }
        #kpiIndicatorsTable .kpi-col-subject { width: 12%; }
        #kpiIndicatorsTable .kpi-col-frequency { width: 8%; }
        #kpiIndicatorsTable .kpi-col-status { width: 10%; }
        #kpiIndicatorsTable .kpi-col-values { width: 6%; }
        #kpiIndicatorsTable .kpi-col-actions { width: 10rem; min-width: 10rem; }
        .kpi-actions-group { display: inline-flex; flex-wrap: wrap; gap: 4px; justify-content: center; }
        .kpi-actions-group .btn { padding: 0.25rem 0.45rem; }
        .kpi-cell-wrap { word-break: break-word; overflow-wrap: anywhere; }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">KPI Indicators</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">KPI Indicators</li>
            </ol>
        </div>
    </div>

    @include('admin.kpi.partials.data_management_panel', ['show_create' => true])

    <div class="row">
        <div class="col-md-12">
            <div class="pub-filters-card mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter indicators</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                </div>
                <div class="pub-filters-card__body">
                    <form id="kpiIndicatorsFiltersForm" method="GET" action="{{ url('admin/kpi') }}" class="mb-0">
                        <div class="pub-filters-grid">
                            <div class="pub-filter-field pub-filter-field--wide">
                                <label class="pub-filter-label" for="filterKpiTerm">Search</label>
                                <input type="text" name="term" id="filterKpiTerm" class="form-control pub-filter-input" placeholder="Name or description" value="{{ @$search->term ?? '' }}">
                            </div>
                            <div class="pub-filter-field">
                                <label class="pub-filter-label" for="filterKpiStatus">Status</label>
                                <select name="status" id="filterKpiStatus" class="form-control pub-filter-input">
                                    <option value="">All statuses</option>
                                    <option value="published" {{ (@$search->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ (@$search->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="recalled" {{ (@$search->status ?? '') === 'recalled' ? 'selected' : '' }}>Recalled</option>
                                </select>
                            </div>
                            <div class="pub-filter-field">
                                <label class="pub-filter-label" for="filterKpiSource">Source</label>
                                <select name="source" id="filterKpiSource" class="form-control pub-filter-input">
                                    <option value="">All sources</option>
                                    <option value="owid" {{ (@$search->source ?? '') === 'owid' ? 'selected' : '' }}>Our World in Data</option>
                                    <option value="manual" {{ (@$search->source ?? '') === 'manual' ? 'selected' : '' }}>Manual</option>
                                </select>
                            </div>
                        </div>
                        <div class="pub-filters-actions">
                            <a href="{{ url('admin/kpi') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearKpiFiltersBtn">
                                <i class="fa fa-rotate-left"></i> Clear
                            </a>
                            @if(($kpi_stats['indicator_duplicate_groups'] ?? 0) > 0)
                                <a href="{{ url('admin/kpi/duplicates') }}" class="pub-filters-btn pub-filters-btn--clear">
                                    <i class="fa fa-clone"></i> Review duplicates
                                </a>
                            @endif
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
                    <h3 class="card-title mb-0">Indicators</h3>
                    <small class="text-muted d-block mt-1">
                        <strong>Publish</strong> indicators to show them on member state country pages.
                        <strong>Recall</strong> hides them from the public without deleting data.
                    </small>
                </div>
                <div class="card-body">
                    @if(session('alert-success'))
                        <div class="alert alert-success">{{ session('alert-success') }}</div>
                    @endif
                    @if(session('alert-info'))
                        <div class="alert alert-info">{{ session('alert-info') }}</div>
                    @endif
                    @if(session('alert-warning'))
                        <div class="alert alert-warning">{{ session('alert-warning') }}</div>
                    @endif
                    @if(session('alert-danger'))
                        <div class="alert alert-danger">{{ session('alert-danger') }}</div>
                    @endif
                    @if(($kpi_stats['indicator_duplicate_groups'] ?? 0) > 0)
                        <div class="alert alert-warning">
                            {{ $kpi_stats['indicator_duplicate_groups'] }} duplicate indicator group(s) detected.
                            <a href="{{ url('admin/kpi/duplicates') }}" class="alert-link">Review and merge duplicates</a>.
                        </div>
                    @endif

                    <form id="kpi-bulk-actions-form" method="POST" action="{{ url('admin/kpi/bulk-action') }}">
                        @csrf
                        <input type="hidden" name="action" id="kpi-bulk-action-type" value="">
                        <input type="hidden" name="narrations" id="kpi-bulk-narrations" value="1">

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <span class="text-muted small">Row color key:</span>
                            <span class="badge badge-light border">Published</span>
                            <span class="badge badge-warning">Recalled</span>
                        </div>

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            <select id="kpiBulkActionSelect" class="form-control" style="width: auto; max-width: 280px;">
                                <option value="">Bulk actions</option>
                                <option value="publish">Publish selected</option>
                                <option value="recall">Recall selected</option>
                                <option value="delete">Delete selected</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="kpiBulkActionButton" disabled>Apply</button>
                            <small class="text-muted">Select rows and an action, then click Apply.</small>
                        </div>

                        <div class="kpi-table-wrap">
                            <table id="kpiIndicatorsTable" data-kh-datatable="custom" class="table table-striped table-bordered table-hover w-100 kh-table-wrap-cells">
                                <thead>
                                    <tr>
                                        <th class="kpi-col-checkbox"><input type="checkbox" id="selectAllKpiIndicators"></th>
                                        <th class="kpi-col-index">#</th>
                                        <th class="kpi-col-title">Indicator</th>
                                        <th class="kpi-col-description">Description</th>
                                        <th class="kpi-col-subject">Subject area</th>
                                        <th class="kpi-col-frequency">Frequency</th>
                                        <th class="kpi-col-status">Status</th>
                                        <th class="kpi-col-values">Values</th>
                                        <th class="kpi-col-actions">Actions</th>
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

    @include('admin.kpi.partials.delete-modal')
    @include('admin.kpi.partials.create-modal', ['subject_areas' => $subject_areas])
    @include('admin.kpi.partials.edit-modal', ['subject_areas' => $subject_areas])

    <div class="modal fade" id="kpiBulkActionConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm bulk action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p id="kpiBulkActionConfirmText" class="mb-2">Are you sure?</p>
                    <div id="kpiBulkPublishNarrationsWrap" class="form-check d-none">
                        <input type="checkbox" class="form-check-input" id="kpiBulkPublishNarrations" checked>
                        <label class="form-check-label" for="kpiBulkPublishNarrations">Also queue AI country summaries after publishing</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmKpiBulkActionBtn">Yes, proceed</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@include('common.select2')
@include('admin.publications.partials.datatable_assets')
<script>
let kpiIndicatorsTable = null;
let kpiFilterTimer = null;
let pendingKpiBulkAction = null;
const kpiManualOnly = @json(kpi_manual_data_only());

function updateKpiBulkActionButton() {
    const anyChecked = $('#kpiIndicatorsTable .kpi-indicator-checkbox:checked').length > 0;
    const actionSelected = $('#kpiBulkActionSelect').val();
    $('#kpiBulkActionButton').prop('disabled', !anyChecked || !actionSelected);
}

function collectKpiFilterParams() {
    const params = {};
    $('#kpiIndicatorsFiltersForm').find('input, select').each(function () {
        const $el = $(this);
        const name = $el.attr('name');
        if (!name) return;
        const value = $el.val();
        if (value !== null && value !== '') {
            params[name] = value;
        }
    });
    return params;
}

function reloadKpiIndicatorsTable() {
    if (kpiIndicatorsTable) {
        kpiIndicatorsTable.ajax.reload();
    }
}

function scheduleKpiFilterReload() {
    clearTimeout(kpiFilterTimer);
    kpiFilterTimer = setTimeout(reloadKpiIndicatorsTable, 350);
}

function submitKpiBulkAction(action) {
    pendingKpiBulkAction = action;
    let text = 'Are you sure you want to perform this action on the selected indicators?';
    if (action === 'publish') text = 'Publish the selected indicators on member state pages?';
    if (action === 'recall') text = 'Recall the selected indicators from public member state pages?';
    if (action === 'delete') text = 'Permanently delete the selected indicators and their country values? This cannot be undone.';
    $('#kpiBulkActionConfirmText').text(text);
    $('#kpiBulkPublishNarrationsWrap').toggleClass('d-none', action !== 'publish' || kpiManualOnly);
    $('#kpiBulkActionConfirmModal').modal('show');
}

function queueKpiTask(url, data, confirmText, $btn, onSuccess) {
    if (confirmText && !window.confirm(confirmText)) return;
    if ($btn) $btn.prop('disabled', true);
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        success: function (response) {
            if (response && response.run_id && typeof window.startKpiTaskPoll === 'function') {
                window.startKpiTaskPoll(response.run_id);
            } else if (response && response.message && !response.run_id) {
                if (typeof window.startKpiTaskPoll === 'function') {
                    // no-op; table reload handles immediate actions
                }
            }
            if (onSuccess) onSuccess();
            else reloadKpiIndicatorsTable();
        },
        error: function (xhr) {
            const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Action failed.';
            alert(message);
        },
        complete: function () {
            if ($btn) $btn.prop('disabled', false);
        }
    });
}

$(function () {
    kpiIndicatorsTable = $('#kpiIndicatorsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        scrollX: false,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        order: [[1, 'desc']],
        ajax: {
            url: '{{ url('admin/kpi') }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectKpiFilterParams());
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false, className: 'kpi-col-checkbox text-center' },
            { data: 'index', orderable: true, searchable: false, className: 'kpi-col-index text-center' },
            { data: 'title', orderable: true, className: 'kpi-col-title' },
            { data: 'description', orderable: true, className: 'kpi-col-description' },
            { data: 'subject_area', orderable: true, className: 'kpi-col-subject' },
            { data: 'frequency', orderable: true, searchable: false, className: 'kpi-col-frequency' },
            { data: 'status', orderable: true, searchable: false, className: 'kpi-col-status' },
            { data: 'values_count', orderable: false, searchable: false, className: 'kpi-col-values text-center' },
            { data: 'actions', orderable: false, searchable: false, className: 'kpi-col-actions text-center' }
        ],
        columnDefs: [
            { targets: [0, 8], orderable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading indicators...',
            emptyTable: 'No indicators match your filters.',
            zeroRecords: 'No matching indicators found.'
        },
        createdRow: function (row, data) {
            if (data.DT_RowClass) $(row).addClass(data.DT_RowClass);
        },
        drawCallback: function () {
            $('#selectAllKpiIndicators').prop('checked', false);
            updateKpiBulkActionButton();
        }
    });

    $('#filterKpiTerm').on('input', scheduleKpiFilterReload);
    $('#filterKpiStatus, #filterKpiSource').on('change', scheduleKpiFilterReload);
    $('#kpiIndicatorsFiltersForm').on('submit', function (e) { e.preventDefault(); reloadKpiIndicatorsTable(); });
    $('#clearKpiFiltersBtn').on('click', function (e) { e.preventDefault(); window.location.href = '{{ url('admin/kpi') }}'; });

    $(document).on('change', '#selectAllKpiIndicators', function () {
        $('#kpiIndicatorsTable .kpi-indicator-checkbox').prop('checked', this.checked);
        updateKpiBulkActionButton();
    });
    $(document).on('change', '.kpi-indicator-checkbox', updateKpiBulkActionButton);
    $('#kpiBulkActionSelect').on('change', updateKpiBulkActionButton);

    $('#kpiBulkActionButton').on('click', function (e) {
        e.preventDefault();
        const action = $('#kpiBulkActionSelect').val();
        if (!$('#kpiIndicatorsTable .kpi-indicator-checkbox:checked').length) {
            alert('Please select at least one indicator.');
            return;
        }
        if (!action) {
            alert('Please select a bulk action.');
            return;
        }
        submitKpiBulkAction(action);
    });

    $('#confirmKpiBulkActionBtn').on('click', function () {
        if (!pendingKpiBulkAction) return;
        $('#kpi-bulk-action-type').val(pendingKpiBulkAction);
        $('#kpi-bulk-narrations').val($('#kpiBulkPublishNarrations').is(':checked') ? '1' : '0');
        $('#kpiBulkActionConfirmModal').modal('hide');
        document.getElementById('kpi-bulk-actions-form').submit();
    });

    $(document).on('click', '.kpi-publish-one', function () {
        const $btn = $(this);
        const id = $btn.data('id');
        if (kpiManualOnly) {
            queueKpiTask('{{ url('admin/kpi/bulk-action') }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: 'publish',
                'selected_ids[]': id
            }, 'Publish this indicator on member state pages?', $btn);
            return;
        }
        queueKpiTask('{{ url('admin/kpi/approve') }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: id
        }, 'Publish this indicator on member state pages?', $btn);
    });

    $(document).on('click', '.kpi-recall-one', function () {
        const $btn = $(this);
        queueKpiTask('{{ url('admin/kpi/recall') }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: $btn.data('id')
        }, 'Recall this indicator from public pages?', $btn);
    });

    $(document).on('click', '.kpi-sync-one', function () {
        const $btn = $(this);
        queueKpiTask('{{ url('admin/kpi/owid/sync-one') }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: $btn.data('id')
        }, 'Refresh country values for this indicator?', $btn);
    });

    $(document).on('click', '.kpi-edit-one', function () {
        openEditModal($(this).data('id'));
    });

    $(document).on('click', '.kpi-delete-one', function () {
        openDeleteModal($(this).data('id'));
    });

    $('#create-modal').on('shown.bs.modal', function () {
        $('#subject_area, #frequency').select2({ dropdownParent: $('#create-modal') });
    });
    $('#edit-modal').on('shown.bs.modal', function () {
        $('#edit_subject_area, #edit_frequency').select2({ dropdownParent: $('#edit-modal') });
    });
});
</script>
@endsection
