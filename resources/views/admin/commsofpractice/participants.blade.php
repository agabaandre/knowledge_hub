@extends(admin_layout())

@php
    $green = settings()->au_corporate_green ?? '#1A5632';
    $gold = settings()->au_gold ?? '#B4A269';
    $pendingCount = (int) ($stats['pending_memberships'] ?? 0);
@endphp

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
    <style>
        .cop-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .cop-stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.15rem;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            position: relative;
            overflow: hidden;
        }
        .cop-stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, {{ $green }}, {{ $gold }});
        }
        .cop-stat-card__label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .cop-stat-card__value {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .cop-stat-card__meta {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 0.35rem;
        }
        .cop-pending-panel {
            border: 1px solid #f59e0b66;
            border-radius: 14px;
            background: linear-gradient(180deg, #fffbeb 0%, #fff 100%);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .cop-pending-panel__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 1.15rem;
            cursor: pointer;
            user-select: none;
        }
        .cop-pending-panel__head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #92400e;
        }
        .cop-pending-panel__body {
            padding: 0 1.15rem 1.15rem;
        }
        .cop-pending-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        @media (min-width: 768px) {
            #participants-table .part-col-index,
            #pending-participants-table .part-col-index { width: 3rem; min-width: 3rem; }
            #participants-table .part-col-name,
            #pending-participants-table .part-col-name { min-width: 140px; }
            #participants-table .part-col-contact,
            #pending-participants-table .part-col-contact { min-width: 140px; }
            #participants-table .part-col-title { min-width: 100px; }
            #participants-table .part-col-org { min-width: 120px; }
            #participants-table .part-col-geo,
            #pending-participants-table .part-col-geo { min-width: 100px; }
            #participants-table .part-col-pub,
            #participants-table .part-col-forum {
                width: 4.25rem;
                min-width: 4.25rem;
                max-width: 4.25rem;
                text-align: center;
                vertical-align: middle;
            }
            #participants-table thead th.part-col-pub,
            #participants-table thead th.part-col-forum {
                font-size: 0.65rem;
                letter-spacing: 0.02em;
                padding-left: 4px;
                padding-right: 4px;
            }
            #participants-table .part-col-badges { min-width: 100px; }
            #participants-table .part-col-communities,
            #pending-participants-table .part-col-community { min-width: 180px; }
            #pending-participants-table .part-col-actions { min-width: 7rem; text-align: center; }
        }
        .cop-community-list,
        .cop-community-list-modal {
            padding-left: 1.2rem;
            margin: 0;
            font-size: 0.82rem;
            line-height: 1.5;
            color: #334155;
        }
        .cop-community-list li,
        .cop-community-list-modal li {
            margin-bottom: 0.2rem;
        }
        .cop-communities-cell .js-view-all-communities {
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            color: {{ $green }};
        }
        .cop-communities-cell .js-view-all-communities:hover {
            text-decoration: underline;
        }
        #pending-participants-table .part-col-select {
            width: 2.75rem;
            min-width: 2.75rem;
            max-width: 2.75rem;
            text-align: center !important;
            vertical-align: middle !important;
            padding: 12px 8px !important;
        }
        #pending-participants-table .cop-pending-checkbox-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 18px;
            margin: 0;
            line-height: 1;
        }
        #pending-participants-table .cop-pending-checkbox {
            width: 18px;
            height: 18px;
            margin: 0 !important;
            padding: 0;
            position: static;
            float: none;
            vertical-align: middle;
            cursor: pointer;
            accent-color: {{ $green }};
        }
        #pending-participants-table thead th.part-col-select .cop-pending-checkbox-wrap {
            padding-top: 0;
        }
        #pending-participants-table_wrapper table.dataTable thead > tr > th.sorting:before,
        #pending-participants-table_wrapper table.dataTable thead > tr > th.sorting:after,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting:before,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting:after,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting_asc:before,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting_asc:after,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting_desc:before,
        #participants-table_wrapper table.dataTable thead > tr > th.sorting_desc:after {
            display: none !important;
            content: none !important;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="cop-stats-grid">
        <div class="cop-stat-card">
            <div class="cop-stat-card__label">Communities</div>
            <div class="cop-stat-card__value">{{ number_format((int) ($stats['total_communities'] ?? 0)) }}</div>
            <div class="cop-stat-card__meta">{{ number_format((int) ($stats['active_communities'] ?? 0)) }} active</div>
        </div>
        <div class="cop-stat-card">
            <div class="cop-stat-card__label">Approved Participants</div>
            <div class="cop-stat-card__value">{{ number_format((int) ($stats['unique_participants'] ?? 0)) }}</div>
            <div class="cop-stat-card__meta">Unique members across communities</div>
        </div>
        <div class="cop-stat-card">
            <div class="cop-stat-card__label">Approved Memberships</div>
            <div class="cop-stat-card__value">{{ number_format((int) ($stats['approved_memberships'] ?? 0)) }}</div>
            <div class="cop-stat-card__meta">Total community subscriptions</div>
        </div>
        <div class="cop-stat-card">
            <div class="cop-stat-card__label">Pending Approvals</div>
            <div class="cop-stat-card__value" style="color: {{ $pendingCount > 0 ? '#b45309' : '#0f172a' }}">{{ number_format($pendingCount) }}</div>
            <div class="cop-stat-card__meta">Awaiting moderator action</div>
        </div>
    </div>

    @if(($stats['participants_by_geography'] ?? collect())->isNotEmpty())
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body py-3">
            <div class="small text-muted text-uppercase fw-bold mb-2">Top participants by {{ \Illuminate\Support\Str::lower($geoLabel) }}</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($stats['participants_by_geography'] as $geoRow)
                    <span class="badge rounded-pill" style="background:#f0f7f4;color:{{ $green }};font-weight:600;padding:0.5rem 0.75rem;">
                        {{ $geoRow->geography_name }}: {{ number_format((int) $geoRow->total) }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="pub-filters-card mb-3">
        <div class="pub-filters-card__header">
            <div class="pub-filters-card__heading">
                <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                <div>
                    <h3 class="pub-filters-card__title">Filter Participants</h3>
                    <p class="pub-filters-card__subtitle">Filters apply to both pending approvals and the approved directory</p>
                </div>
            </div>
            <button class="pub-filters-advanced-toggle" type="button" data-toggle="collapse" data-target="#participantsAdvancedFilters" aria-expanded="{{ !empty($search->community_id) || !empty($search->badge_type_id) ? 'true' : 'false' }}" aria-controls="participantsAdvancedFilters">
                <i class="fa fa-sliders mr-1"></i> Advanced Filters
            </button>
        </div>
        <div class="pub-filters-card__body">
            <form id="participantsFiltersForm" method="GET" action="{{ route('admin.commsofpractice.participants') }}" class="mb-0">
                <div class="pub-filters-grid pub-filters-grid--single" style="margin-bottom: 1rem;">
                    <div class="pub-filter-field">
                        <label class="pub-filter-label" for="filterQ">Search</label>
                        <input type="text" class="form-control pub-filter-input" id="filterQ" name="q" value="{{ $search->q ?? '' }}" placeholder="Name, email, phone, community, organisation, {{ \Illuminate\Support\Str::lower($geoLabel) }}">
                    </div>
                </div>
                <div class="pub-filters-grid pub-filters-grid--primary">
                    <div class="pub-filter-field">
                        <label class="pub-filter-label" for="filterTitle">Title</label>
                        <input type="text" class="form-control pub-filter-input" id="filterTitle" name="title" value="{{ $search->title ?? '' }}" placeholder="Job title">
                    </div>
                    <div class="pub-filter-field">
                        <label class="pub-filter-label" for="filterOrganisation">Organisation</label>
                        <input type="text" class="form-control pub-filter-input" id="filterOrganisation" name="organisation" value="{{ $search->organisation ?? '' }}" placeholder="Organisation">
                    </div>
                    <div class="pub-filter-field">
                        <label class="pub-filter-label" for="filterGeography">{{ $geoLabel }}</label>
                        <select class="form-control pub-filter-input" id="filterGeography" name="geography_id">
                            <option value="">All {{ \Illuminate\Support\Str::lower($geoLabel) }}s</option>
                            @foreach($geographies as $c)
                                @php $selectedGeo = (string)($search->geography_id ?? ($search->country_id ?? '')); @endphp
                                <option value="{{ $c->id }}" {{ $selectedGeo === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="participantsAdvancedFilters" class="collapse pub-filters-advanced {{ !empty($search->community_id) || !empty($search->badge_type_id) ? 'show' : '' }}">
                    <span class="pub-filters-advanced__label"><i class="fa fa-sliders"></i> Advanced Filters</span>
                    <div class="pub-filters-grid pub-filters-grid--primary">
                        <div class="pub-filter-field">
                            <label class="pub-filter-label" for="filterCommunity">Community</label>
                            <select class="form-control pub-filter-input" id="filterCommunity" name="community_id">
                                <option value="">All communities</option>
                                @foreach($communities as $c)
                                    <option value="{{ $c->id }}" {{ (string)($search->community_id ?? '') === (string)$c->id ? 'selected' : '' }}>{{ $c->community_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="pub-filter-field">
                            <label class="pub-filter-label" for="filterBadge">Badge</label>
                            <select class="form-control pub-filter-input" id="filterBadge" name="badge_type_id">
                                <option value="">Any badge</option>
                                @foreach($badgeTypes as $b)
                                    <option value="{{ $b->id }}" {{ (string)($search->badge_type_id ?? '') === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="pub-filters-actions">
                    <a href="{{ route('admin.commsofpractice.participants') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearParticipantsFilters">
                        <i class="fa fa-rotate-left"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="cop-pending-panel">
        <div class="cop-pending-panel__head" data-toggle="collapse" data-target="#pendingApprovalsCollapse" aria-expanded="{{ $pendingCount > 0 ? 'true' : 'false' }}">
            <h3><i class="fa fa-hourglass-half mr-2"></i>Pending membership approvals ({{ number_format($pendingCount) }})</h3>
            <i class="fa fa-chevron-down text-muted"></i>
        </div>
        <div id="pendingApprovalsCollapse" class="collapse {{ $pendingCount > 0 ? 'show' : '' }}">
            <div class="cop-pending-panel__body">
                <p class="text-muted small mb-2">Review join requests below. Each row is one subscription awaiting approval. Use bulk actions or approve individually.</p>
                <div class="cop-pending-actions">
                    <button type="button" class="btn btn-success btn-sm" id="bulkApprovePending" disabled>
                        <i class="fa fa-check mr-1"></i> Approve selected
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="bulkRejectPending" disabled>
                        <i class="fa fa-times mr-1"></i> Reject selected
                    </button>
                </div>
                <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Rows are shown as cards on small screens.</p>
                <div class="publication-table-wrap kh-table-mobile-scroll">
                    <table id="pending-participants-table" data-kh-datatable="custom" class="table table-bordered table-striped table-hover w-100 kh-table-mobile-cards kh-table-mobile-cards--medium">
                        <thead>
                            <tr>
                                <th class="part-col-select" data-mobile-label="Select">
                                    <span class="cop-pending-checkbox-wrap">
                                        <input type="checkbox" id="pendingSelectAll" class="cop-pending-checkbox" aria-label="Select all pending">
                                    </span>
                                </th>
                                <th class="part-col-index">#</th>
                                <th class="part-col-name">Applicant</th>
                                <th class="part-col-contact">Email / Phone</th>
                                <th class="part-col-community">Community</th>
                                <th class="part-col-geo">{{ $geoLabel }}</th>
                                <th>Requested</th>
                                <th class="part-col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card pub-list-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title mb-0">
                <i class="fa fa-users mr-2"></i>Approved participants directory
            </h3>
            <span class="badge" style="background:#f0f7f4;color:{{ $green }};">One row per member</span>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Approved members appear once. Up to five communities are shown per member; click <strong>+ N more</strong> to preview the full numbered list.</p>
            <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Rows are shown as cards on small screens.</p>
            <div class="publication-table-wrap kh-table-mobile-scroll">
                <table id="participants-table" data-kh-datatable="custom" class="table table-bordered table-striped table-hover w-100 kh-table-mobile-cards kh-table-mobile-cards--wide">
                    <thead>
                        <tr>
                            <th class="part-col-index">#</th>
                            <th class="part-col-name">Participant Name</th>
                            <th class="part-col-contact">Email / Phone</th>
                            <th class="part-col-title">Title</th>
                            <th class="part-col-org">Organisation</th>
                            <th class="part-col-geo">{{ $geoLabel }}</th>
                            <th class="part-col-pub kh-th-wrap" data-mobile-label="Publication Contributions">Publication<br>Contributions</th>
                            <th class="part-col-forum kh-th-wrap" data-mobile-label="Forum Contributions">Forum<br>Contributions</th>
                            <th class="part-col-badges">Badge(s)</th>
                            <th class="part-col-communities">Communities Subscribed</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="participantCommunitiesModal" tabindex="-1" role="dialog" aria-labelledby="participantCommunitiesModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f0f7f4 0%, #fff 100%);">
                <h5 class="modal-title" id="participantCommunitiesModalTitle">Communities subscribed</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-2" id="participantCommunitiesModalMember"></p>
                <ol id="participantCommunitiesModalList" class="cop-community-list-modal mb-0"></ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.publications.partials.datatable_assets')
<script>
let participantsTable = null;
let pendingTable = null;
let participantsFilterTimer = null;
const memberActionUrl = @json(route('admin.commsofpractice.memberAction'));

function collectParticipantsFilterParams() {
    const params = {};
    $('#participantsFiltersForm').find('input, select').each(function () {
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

function reloadParticipantTables() {
    if (pendingTable) pendingTable.ajax.reload();
    if (participantsTable) participantsTable.ajax.reload();
}

function scheduleParticipantsFilterReload() {
    clearTimeout(participantsFilterTimer);
    participantsFilterTimer = setTimeout(reloadParticipantTables, 350);
}

function selectedPendingRows() {
    const rows = [];
    $('.js-pending-select:checked').each(function () {
        rows.push({
            member_id: parseInt(this.value, 10),
            community_id: parseInt($(this).data('community-id'), 10)
        });
    });
    return rows;
}

function updatePendingBulkButtons() {
    const hasSelection = $('.js-pending-select:checked').length > 0;
    $('#bulkApprovePending, #bulkRejectPending').prop('disabled', !hasSelection);
}

function runMemberAction(action, memberIds, communityId) {
    return $.ajax({
        url: memberActionUrl,
        method: 'POST',
        data: {
            _token: @json(csrf_token()),
            action: action,
            community_id: communityId,
            member_ids: memberIds
        }
    });
}

function handlePendingAction(action, rows) {
    if (!rows.length) return;
    const grouped = {};
    rows.forEach(function (row) {
        if (!grouped[row.community_id]) grouped[row.community_id] = [];
        grouped[row.community_id].push(row.member_id);
    });
    const requests = Object.keys(grouped).map(function (communityId) {
        return runMemberAction(action, grouped[communityId], communityId);
    });
    $.when.apply($, requests).done(function () {
        reloadParticipantTables();
        $('#pendingSelectAll').prop('checked', false);
        updatePendingBulkButtons();
    }).fail(function (xhr) {
        alert((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Action failed.');
    });
}

$(function () {
    pendingTable = $('#pending-participants-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: false,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ajax: {
            url: '{{ route('admin.commsofpractice.participants') }}',
            data: function (d) {
                d.datatable = 1;
                d.scope = 'pending';
                return Object.assign(d, collectParticipantsFilterParams());
            }
        },
        columns: [
            { data: 'select', orderable: false, searchable: false, className: 'part-col-select kh-mcard-select' },
            { data: 'index', orderable: false, searchable: false, className: 'part-col-index kh-mcard-hide text-center' },
            { data: 'name', orderable: false, className: 'part-col-name kh-mcard-primary' },
            { data: 'contact', orderable: false, className: 'part-col-contact' },
            { data: 'community', orderable: false, className: 'part-col-community' },
            { data: 'geography', orderable: false, className: 'part-col-geo' },
            { data: 'requested', orderable: false },
            { data: 'actions', orderable: false, searchable: false, className: 'part-col-actions kh-mcard-actions text-center' }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading pending requests...',
            emptyTable: 'No pending membership requests match your filters.',
            zeroRecords: 'No matching pending requests found.'
        }
    });

    participantsTable = $('#participants-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        order: [[1, 'asc']],
        ajax: {
            url: '{{ route('admin.commsofpractice.participants') }}',
            data: function (d) {
                d.datatable = 1;
                d.scope = 'approved';
                return Object.assign(d, collectParticipantsFilterParams());
            }
        },
        columns: [
            { data: 'index', orderable: false, searchable: false, className: 'part-col-index kh-mcard-hide text-center' },
            { data: 'name', orderable: true, className: 'part-col-name kh-mcard-primary' },
            { data: 'contact', orderable: true, className: 'part-col-contact' },
            { data: 'title', orderable: true, className: 'part-col-title' },
            { data: 'organisation', orderable: true, className: 'part-col-org' },
            { data: 'geography', orderable: true, className: 'part-col-geo' },
            { data: 'publications', orderable: false, searchable: false, className: 'part-col-pub text-center' },
            { data: 'forums', orderable: false, searchable: false, className: 'part-col-forum text-center' },
            { data: 'badges', orderable: false, searchable: false, className: 'part-col-badges' },
            { data: 'communities', orderable: false, searchable: false, className: 'part-col-communities' }
        ],
        columnDefs: [
            { targets: [0, 6, 7, 8, 9], orderable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading participants...',
            emptyTable: 'No approved participants match your filters.',
            zeroRecords: 'No matching participants found.'
        }
    });

    $(document).on('change', '.js-pending-select, #pendingSelectAll', function () {
        if (this.id === 'pendingSelectAll') {
            $('.js-pending-select').prop('checked', this.checked);
        }
        updatePendingBulkButtons();
    });

    pendingTable.on('draw', function () {
        $('#pendingSelectAll').prop('checked', false);
        updatePendingBulkButtons();
    });

    $('#bulkApprovePending').on('click', function () {
        handlePendingAction('approve', selectedPendingRows());
    });
    $('#bulkRejectPending').on('click', function () {
        if (!confirm('Reject the selected membership requests?')) return;
        handlePendingAction('reject', selectedPendingRows());
    });

    $(document).on('click', '.js-pending-approve', function () {
        const memberId = parseInt($(this).data('member-id'), 10);
        const communityId = parseInt($(this).data('community-id'), 10);
        handlePendingAction('approve', [{ member_id: memberId, community_id: communityId }]);
    });
    $(document).on('click', '.js-pending-reject', function () {
        if (!confirm('Reject this membership request?')) return;
        const memberId = parseInt($(this).data('member-id'), 10);
        const communityId = parseInt($(this).data('community-id'), 10);
        handlePendingAction('reject', [{ member_id: memberId, community_id: communityId }]);
    });

    $('#filterQ, #filterTitle, #filterOrganisation').on('input', scheduleParticipantsFilterReload);
    $('#filterGeography, #filterCommunity, #filterBadge').on('change', scheduleParticipantsFilterReload);

    $('#participantsFiltersForm').on('submit', function (e) {
        e.preventDefault();
        reloadParticipantTables();
    });

    $('#clearParticipantsFilters').on('click', function (e) {
        e.preventDefault();
        window.location.href = '{{ route('admin.commsofpractice.participants') }}';
    });

    $(document).on('click', '.js-view-all-communities', function () {
        const memberName = $(this).attr('data-member-name') || 'Participant';
        let communities = [];
        try {
            communities = JSON.parse($(this).attr('data-communities') || '[]');
        } catch (e) {
            communities = [];
        }
        const $list = $('#participantCommunitiesModalList').empty();
        if (!communities.length) {
            $list.append('<li class="text-muted">No communities found.</li>');
        } else {
            communities.forEach(function (name) {
                $list.append($('<li>').text(name));
            });
        }
        $('#participantCommunitiesModalMember').text(memberName);
        $('#participantCommunitiesModalTitle').text('Communities subscribed (' + communities.length + ')');
        $('#participantCommunitiesModal').modal('show');
    });
});
</script>
@endsection
