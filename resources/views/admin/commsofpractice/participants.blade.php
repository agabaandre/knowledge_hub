@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
    <style>
        .stat-card-mini {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0;
        }
        #participants-table_wrapper table.dataTable { table-layout: fixed !important; }
        #participants-table .part-col-index { width: 3rem; min-width: 3rem; }
        #participants-table .part-col-name { width: 12%; }
        #participants-table .part-col-email { width: 14%; }
        #participants-table .part-col-title { width: 10%; }
        #participants-table .part-col-org { width: 12%; }
        #participants-table .part-col-geo { width: 9%; }
        #participants-table .part-col-pub,
        #participants-table .part-col-forum { width: 5rem; text-align: center; }
        #participants-table .part-col-badges,
        #participants-table .part-col-communities { width: 14%; }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-left-primary shadow-sm h-100 stat-card-mini">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Communities</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format((int) $totalCommunities) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm h-100 stat-card-mini">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Unique Memberships</div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format((int) $totalUniqueMemberships) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow-sm h-100 stat-card-mini">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Memberships by {{ $geoLabel }}</div>
                    <div class="h4 mb-2 font-weight-bold text-gray-800">{{ number_format((int) $totalMembershipsByGeography) }}</div>
                    <div class="small text-muted">
                        @foreach($membershipsByGeography->take(5) as $g)
                            <div>{{ $g->geography_name ?: 'Unspecified' }}: {{ (int) $g->total }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pub-filters-card mb-3">
        <div class="pub-filters-card__header">
            <div class="pub-filters-card__heading">
                <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                <div>
                    <h3 class="pub-filters-card__title">Filter Participants</h3>
                    <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
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
                        <input type="text" class="form-control pub-filter-input" id="filterQ" name="q" value="{{ $search->q ?? '' }}" placeholder="Name, email, title, organisation, {{ \Illuminate\Support\Str::lower($geoLabel) }}">
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

    <div class="card pub-list-card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fa fa-users mr-2"></i>Community Participants Directory
            </h3>
        </div>
        <div class="card-body">
            <div class="publication-table-wrap">
                <table id="participants-table" data-kh-datatable="custom" class="table table-bordered table-striped table-hover w-100 kh-table-wrap-cells">
                    <thead>
                        <tr>
                            <th class="part-col-index">#</th>
                            <th class="part-col-name">Participant Name</th>
                            <th class="part-col-email">Email Address</th>
                            <th class="part-col-title">Title</th>
                            <th class="part-col-org">Organisation</th>
                            <th class="part-col-geo">{{ $geoLabel }}</th>
                            <th class="part-col-pub">Publication Contributions</th>
                            <th class="part-col-forum">Forum Contributions</th>
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
@endsection

@section('scripts')
@include('admin.publications.partials.datatable_assets')
<script>
let participantsTable = null;
let participantsFilterTimer = null;

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

function reloadParticipantsTable() {
    if (participantsTable) participantsTable.ajax.reload();
}

function scheduleParticipantsFilterReload() {
    clearTimeout(participantsFilterTimer);
    participantsFilterTimer = setTimeout(reloadParticipantsTable, 350);
}

$(function () {
    participantsTable = $('#participants-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        scrollX: false,
        pageLength: 20,
        lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        order: [[1, 'asc']],
        ajax: {
            url: '{{ route('admin.commsofpractice.participants') }}',
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectParticipantsFilterParams());
            }
        },
        columns: [
            { data: 'index', orderable: false, searchable: false, className: 'part-col-index text-center' },
            { data: 'name', orderable: true, className: 'part-col-name' },
            { data: 'email', orderable: true, className: 'part-col-email' },
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
            emptyTable: 'No participants match your filters.',
            zeroRecords: 'No matching participants found.'
        }
    });

    $('#filterQ, #filterTitle, #filterOrganisation').on('input', scheduleParticipantsFilterReload);
    $('#filterGeography, #filterCommunity, #filterBadge').on('change', scheduleParticipantsFilterReload);

    $('#participantsFiltersForm').on('submit', function (e) {
        e.preventDefault();
        reloadParticipantsTable();
    });

    $('#clearParticipantsFilters').on('click', function (e) {
        e.preventDefault();
        window.location.href = '{{ route('admin.commsofpractice.participants') }}';
    });
});
</script>
@endsection
