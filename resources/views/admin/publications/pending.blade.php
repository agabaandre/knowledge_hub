@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ ui_t('admin_publications.pending_title', 'Pending Public Health Resources') }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('admin_nav.publish') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ui_t('admin_publications.pending_breadcrumb', 'Pending Public Health Resource') }}</li>
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
                            <h3 class="pub-filters-card__title">{{ ui_t('admin_publications.filter_pending', 'Filter Pending Resources') }}</h3>
                            <p class="pub-filters-card__subtitle">{{ ui_t('admin_publications.filter_subtitle', 'Filters apply automatically as you type or change selections') }}</p>
                        </div>
                    </div>
                    <button class="pub-filters-advanced-toggle" type="button" data-toggle="collapse" data-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                        <i class="fa fa-sliders mr-1"></i> {{ ui_t('admin_publications.advanced_filters', 'Advanced Filters') }}
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
                                <i class="fa fa-rotate-left"></i> {{ ui_t('general.clear', 'Clear') }}
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
                <div class="card-header"><h3 class="card-title mb-0">{{ ui_t('admin_publications.pending_publications', 'Pending Publications') }}</h3></div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

                    @if(!empty($allowContentWipe))
                        <div class="alert alert-danger border">
                            <strong>{{ ui_t('admin_publications.wipe_title', 'Danger zone — wipe hub content') }}</strong>
                            <p class="mb-2 small">
                                {{ ui_t('admin_publications.wipe_help', 'Permanently deletes all publications, forums, comments, access/search logs, authors, and related pivot data (and communities if selected), then resets table auto-increment. Taxonomy/users/settings are kept. Enable only via HUB_ALLOW_CONTENT_WIPE=true.') }}
                            </p>
                            <form method="post" action="{{ route('admin.publications.wipe-content') }}" class="form-inline flex-wrap"
                                  onsubmit="return confirm(@json(ui_t('admin_publications.wipe_confirm_prompt', 'This cannot be undone. Wipe all content on this hub?')));">
                                @csrf
                                <div class="form-group mr-2 mb-2">
                                    <label class="sr-only" for="wipeConfirmation">{{ ui_t('admin_publications.wipe_type_placeholder', 'Type :phrase', ['phrase' => $wipeConfirmationPhrase ?? 'WIPE']) }}</label>
                                    <input type="text" name="confirmation" id="wipeConfirmation" class="form-control form-control-sm"
                                           placeholder="{{ ui_t('admin_publications.wipe_type_placeholder', 'Type :phrase', ['phrase' => $wipeConfirmationPhrase ?? 'WIPE']) }}" required autocomplete="off">
                                </div>
                                <div class="form-check mr-2 mb-2">
                                    <input class="form-check-input" type="checkbox" name="include_communities" value="1" id="wipeCommunities" checked>
                                    <label class="form-check-label" for="wipeCommunities">{{ ui_t('admin_publications.wipe_communities', 'Also wipe communities') }}</label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm mb-2">{{ ui_t('admin_publications.wipe_button', 'Wipe content') }}</button>
                            </form>
                        </div>
                    @endif

                    <form id="bulk-approval-form" method="POST" action="{{ route('admin.publications.bulk-approval') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-approval-action" value="">
                        <input type="hidden" name="rejected_reason" id="bulkRejectReasonHidden" value="">

                        <div class="mb-3 d-flex align-items-center flex-wrap" style="gap:8px;">
                            @can('moderate_publication')
                            <button type="button" class="btn btn-sm btn-success" id="bulkApproveBtn" disabled>
                                <i class="fa fa-check-circle mr-1"></i> {{ ui_t('general.approve_selected', 'Approve selected') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkRejectBtn" disabled>
                                <i class="fa fa-times-circle mr-1"></i> {{ ui_t('general.reject_selected', 'Reject selected') }}
                            </button>
                            @endcan
                            <span class="text-muted small" id="bulkSelectionCount"></span>
                        </div>

                        <div class="table-responsive">
                            <table id="publicationTable" class="table table-striped table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th width="40">@can('moderate_publication')<input type="checkbox" id="selectAllPending">@endcan</th>
                                        <th width="60">#</th>
                                        <th>{{ ui_t('admin_publications.col_title', 'Title') }}</th>
                                        <th>{{ ui_t('admin_publications.col_description', 'Description') }}</th>
                                        <th>{{ ui_t('admin_publications.col_author', 'Author') }}</th>
                                        <th>{{ ui_t('admin_publications.col_affiliation', 'Affiliation') }}</th>
                                        <th>{{ ui_t('admin_publications.col_member_state', 'Member State') }}</th>
                                        <th>{{ ui_t('admin_publications.col_status', 'Status') }}</th>
                                        <th>{{ ui_t('admin_publications.col_date_created', 'Date Created') }}</th>
                                        <th>{{ ui_t('admin_publications.col_actions', 'Actions') }}</th>
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
                    <h5 class="modal-title">{{ ui_t('admin_publications.reject_selected_title', 'Reject selected publications') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <textarea id="bulkRejectReason" class="form-control" rows="3" placeholder="{{ ui_t('admin_publications.reject_reason_placeholder', 'Reason for rejection (optional)') }}"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ ui_t('general.cancel', 'Cancel') }}</button>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkRejectConfirmBtn">{{ ui_t('general.reject_selected', 'Reject selected') }}</button>
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
const pubI18n = {
    selectedCount: @json(ui_t('admin_publications.selected_count', ':count selected')),
    confirmApprove: @json(ui_t('admin_publications.confirm_approve_selected', 'Approve the selected publication(s)?'))
};
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
    $('#bulkSelectionCount').text(n > 0 ? pubI18n.selectedCount.replace(':count', String(n)) : '');
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
        if (!confirm(pubI18n.confirmApprove)) return;
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
