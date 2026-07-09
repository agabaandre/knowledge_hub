@extends(admin_layout('tabular'))

@section('styles')
 @include('common.table')
 @include('admin.publications.partials.filter_styles')
 @include('partials.general.summernote')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0}
    .af-card .card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card .card-body{padding:16px}
    #communities-table .badge-danger,
    .badge.badge-danger.badge-pill { background-color: #dc3545 !important; color: #fff !important; }
    @media (min-width: 768px) {
        #communities-table .cop-col-index { width: 3rem; min-width: 3rem; }
        #communities-table .cop-col-community { min-width: 200px; }
        #communities-table .cop-col-description { min-width: 280px; }
        #communities-table .cop-col-actions { min-width: 11rem; white-space: nowrap; vertical-align: middle; }
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

            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="small text-muted text-uppercase">Total Communities</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format((int) ($total_communities_count ?? 0)) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="small text-muted text-uppercase">Active Communities</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format((int) ($active_communities_count ?? 0)) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="small text-muted text-uppercase">Approved Memberships</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format((int) ($approved_memberships_count ?? 0)) }}</div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="p-3 border rounded bg-light h-100">
                        <div class="small text-muted text-uppercase">Pending Approvals</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format((int) ($pending_member_approvals_count ?? 0)) }}</div>
                        <div class="small text-muted">Public communities: {{ number_format((int) ($public_communities_count ?? 0)) }}</div>
                    </div>
                </div>
            </div>

            <div class="pub-filters-card mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Communities</h3>
                            <p class="pub-filters-card__subtitle">Search by community name, description, or creator email</p>
                        </div>
                    </div>
                </div>
                <div class="pub-filters-card__body">
                    <form id="communitiesFiltersForm" method="GET" action="{{ request()->url() }}">
                        <div class="pub-filters-grid pub-filters-grid--single">
                            <div class="pub-filter-field">
                                <label class="pub-filter-label" for="communityTerm">Keyword</label>
                                <input type="text" name="term" id="communityTerm" class="form-control pub-filter-input" value="{{ request('term') }}" placeholder="Community name, description, or creator email">
                            </div>
                        </div>
                        <div class="pub-filters-actions">
                            <a href="{{ request()->url() }}" class="pub-filters-btn pub-filters-btn--clear" id="clearCommunitiesFilters">
                                <i class="fa fa-rotate-left"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Rows are shown as cards on small screens.</p>
            <div class="publication-table-wrap kh-table-mobile-scroll">
                <table id="communities-table" data-kh-datatable="custom" class="table table-striped table-hover table-bordered w-100 kh-table-mobile-cards kh-table-mobile-cards--compact">
                    <thead>
                        <tr>
                            <th class="cop-col-index">#</th>
                            <th class="cop-col-community">Community</th>
                            <th class="cop-col-description">Description</th>
                            <th class="cop-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.commsofpractice.partials.create-modal')
    @include('admin.commsofpractice.partials.delete-modal')

    @endsection

@section('scripts')
@include('partials.general.summernote')
@include('common.select2')
@include('admin.publications.partials.datatable_assets')
<script>
let communitiesTable = null;
let communitiesFilterTimer = null;
let copAllCountries = [];

function collectCommunitiesFilterParams() {
    const params = {};
    const term = $('#communityTerm').val();
    if (term) params.term = term;
    return params;
}

function reloadCommunitiesTable() {
    if (communitiesTable) communitiesTable.ajax.reload();
}

function initCopModalSelect($el) {
    if (!$el.length || typeof $.fn.select2 === 'undefined') {
        return;
    }

    if ($el.hasClass('select2-hidden-accessible')) {
        $el.select2('destroy');
    }

    $el.select2({
        width: '100%',
        dropdownParent: $('#create-modal'),
        minimumResultsForSearch: 0,
        placeholder: $el.data('placeholder') || 'Select an option',
        allowClear: $el.attr('id') === 'country_id'
    });
}

function rebuildCopCountryOptions(selectedRegion, preserveCountryId) {
    const countrySelect = $('#country_id');
    if (!countrySelect.length) {
        return;
    }

    const firstOption = countrySelect.find('option:first').clone();
    countrySelect.empty().append(firstOption);

    let list = copAllCountries;
    if (selectedRegion && selectedRegion !== 'all' && selectedRegion !== '') {
        const regionId = parseInt(selectedRegion, 10);
        list = copAllCountries.filter(function (country) {
            return country.region_id === regionId;
        });
    }

    list.forEach(function (country) {
        countrySelect.append(
            $('<option></option>').attr('value', country.id).text(country.name)
        );
    });

    if (preserveCountryId && countrySelect.find('option[value="' + preserveCountryId + '"]').length) {
        countrySelect.val(String(preserveCountryId));
    } else {
        countrySelect.val('');
    }

    if (countrySelect.hasClass('select2-hidden-accessible')) {
        countrySelect.trigger('change');
    }
}

$(function(){
    communitiesTable = $('#communities-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        autoWidth: false,
        scrollX: false,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        order: [[1, 'asc']],
        ajax: {
            url: '{{ request()->url() }}',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            data: function (d) {
                d.datatable = 1;
                return Object.assign(d, collectCommunitiesFilterParams());
            }
        },
        columns: [
            { data: 'index', orderable: false, searchable: false, className: 'cop-col-index kh-mcard-hide text-center' },
            { data: 'community_name', orderable: true, className: 'cop-col-community kh-mcard-primary' },
            { data: 'description', orderable: true, className: 'cop-col-description' },
            { data: 'actions', orderable: false, searchable: false, className: 'cop-col-actions kh-mcard-actions text-center' }
        ],
        columnDefs: [
            { targets: [0, 3], orderable: false }
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading communities...',
            emptyTable: 'No communities match your filters.',
            zeroRecords: 'No matching communities found.'
        }
    });

    $('#communityTerm').on('input', function () {
        clearTimeout(communitiesFilterTimer);
        communitiesFilterTimer = setTimeout(reloadCommunitiesTable, 350);
    });

    function initSN(){
        var $el = $('#description');
        if ($el.length && !$el.hasClass('summernote-sm')) { $el.addClass('summernote-sm'); }
    }
    initSN();
    $(document).on('shown.bs.modal', '#create-modal', function(){
        initSN();
        initCopModalSelect($('#region_id'));
        initCopModalSelect($('#country_id'));
        rebuildCopCountryOptions($('#region_id').val(), $('#country_id').val());

        var $tagsSelect = $('#tags\\[\\]');
        if ($tagsSelect.length && typeof $.fn.select2 !== 'undefined' && !$tagsSelect.hasClass('select2-hidden-accessible')) {
            $tagsSelect.select2({
                theme: 'bootstrap4',
                placeholder: 'Select Tags',
                allowClear: true,
                dropdownParent: $('#create-modal')
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
            
            var regionVal = item.region_id ? String(item.region_id) : 'all';
            $('#region_id').val(regionVal).trigger('change');
            rebuildCopCountryOptions(regionVal, item.country_id || null);
            
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
    var regionsData = @json($regions ?? []);

    if (regionsData && Array.isArray(regionsData)) {
        regionsData.forEach(function(region) {
            if (region.countries && Array.isArray(region.countries)) {
                region.countries.forEach(function(country) {
                    copAllCountries.push({
                        id: country.id,
                        name: country.name,
                        region_id: region.id
                    });
                });
            }
        });
    }

    $(document).on('change', '#region_id', function() {
        rebuildCopCountryOptions($(this).val(), null);
    });
});

var copDeleteCommunityId = null;

function openDeleteCommunityModal(communityId, communityName) {
    copDeleteCommunityId = communityId;
    $('#copDeleteCommunityName').text(communityName || ('Community #' + communityId));
    $('#copDeleteError').addClass('d-none').text('');
    $('#copConfirmDeleteBtn').prop('disabled', false);
    $('.cop-delete-btn-label').removeClass('d-none');
    $('.cop-delete-btn-loading').addClass('d-none');
    $('#cop-delete-modal').modal('show');
}

$(document).on('click', '.js-delete-community', function () {
    var btn = $(this);
    openDeleteCommunityModal(btn.data('community-id'), btn.data('community-name'));
});

$(document).on('click', '#copConfirmDeleteBtn', function () {
    if (!copDeleteCommunityId) {
        return;
    }

    var $btn = $(this);
    var $error = $('#copDeleteError');
    $error.addClass('d-none').text('');
    $btn.prop('disabled', true);
    $('.cop-delete-btn-label').addClass('d-none');
    $('.cop-delete-btn-loading').removeClass('d-none');

    var url = @json(url('admin/commsofpractice/delete')) + '?id=' + encodeURIComponent(copDeleteCommunityId);

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(function (res) {
        return res.json().then(function (body) {
            return { ok: res.ok, status: res.status, body: body };
        }).catch(function () {
            return { ok: res.ok, status: res.status, body: {} };
        });
    })
    .then(function (result) {
        if (result.ok && result.body.status === 'success') {
            $('#cop-delete-modal').modal('hide');
            if (typeof communitiesTable !== 'undefined' && communitiesTable) {
                communitiesTable.ajax.reload(null, false);
            } else {
                window.location.reload();
            }
            return;
        }

        var message = result.body.message || 'Failed to delete community. Please try again.';
        $error.removeClass('d-none').text(message);
        $btn.prop('disabled', false);
        $('.cop-delete-btn-label').removeClass('d-none');
        $('.cop-delete-btn-loading').addClass('d-none');
    })
    .catch(function () {
        $error.removeClass('d-none').text('Failed to delete community. Please try again.');
        $btn.prop('disabled', false);
        $('.cop-delete-btn-label').removeClass('d-none');
        $('.cop-delete-btn-loading').addClass('d-none');
    });
});
</script>
@endsection