@extends('layouts.plain')

@section('styles')
@include('common.table')
@include('admin.publications.partials.filter_styles')
<style>
    .my-pub-page {
        --mp-green: {{ settings()->au_corporate_green ?? '#1A5632' }};
        --mp-gold: {{ settings()->au_gold ?? '#B4A269' }};
        --mp-red: {{ settings()->au_red ?? '#9F2241' }};
        --mp-plum: {{ settings()->au_plum ?? '#522B39' }};
        --mp-grey: {{ settings()->au_grey_text ?? '#58595B' }};
    }
    .my-pub-shell {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .my-pub-shell > .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .my-pub-shell .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .my-pub-shell__subtitle {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0.25rem 0 0;
    }
    .my-pub-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .my-pub-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .my-pub-quick-links a:hover {
        border-color: var(--mp-green);
        color: var(--mp-green);
        box-shadow: 0 2px 8px rgba(26, 86, 50, 0.08);
        text-decoration: none;
    }
    .my-pub-section {
        margin-bottom: 1.25rem;
    }
    .my-pub-section__title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        margin: 0 0 0.65rem;
    }
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(132px, 1fr));
        gap: 0.65rem;
    }
    .metric-card {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.7rem 0.85rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        min-height: 0;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .metric-card__icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .metric-card__icon--green { background: #ecfdf3; color: var(--mp-green); }
    .metric-card__icon--gold { background: #faf6eb; color: var(--mp-gold); }
    .metric-card__icon--red { background: #fdf2f6; color: var(--mp-red); }
    .metric-card__icon--grey { background: #f1f5f9; color: var(--mp-grey); }
    .metric-card__icon--plum { background: #f6f0f3; color: var(--mp-plum); }
    .metric-card__value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }
    .metric-card__label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
        line-height: 1.2;
        margin-top: 0.1rem;
    }
    .community-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.1rem 1.1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .community-panel__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
        flex-wrap: wrap;
    }
    .community-panel__head h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }
    .community-panel__head p {
        margin: 0.2rem 0 0;
        font-size: 0.8rem;
        color: #64748b;
    }
    .community-panel__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: #ecfdf3;
        color: var(--mp-green);
        font-size: 0.75rem;
        font-weight: 700;
    }
    .community-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.65rem;
    }
    .community-card {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.75rem 0.9rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }
    .community-card:hover {
        border-color: var(--mp-green);
        background: #fff;
        transform: translateY(-1px);
        text-decoration: none;
        color: var(--mp-green);
    }
    .community-card__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mp-green);
        flex-shrink: 0;
    }
    .community-card__name {
        font-size: 0.84rem;
        font-weight: 600;
        line-height: 1.35;
        word-break: break-word;
    }
    .community-card__meta {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.15rem;
    }
    
    /* A4-like PDF Preview Modal Styles */
    #previewModal .modal-dialog {
        max-width: 90vw;
        margin: 1.75rem auto;
    }
    
    @media (min-width: 1200px) {
        #previewModal .modal-dialog {
            max-width: 900px; /* A4 width equivalent at reasonable scale */
        }
    }
    
    #previewModal .modal-content {
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        background: #ffffff;
    }
    
    #previewModal .modal-header {
        background: linear-gradient(135deg, #911C39 0%, #6b1429 100%);
        color: white;
        border-radius: 0.75rem 0.75rem 0 0;
        border: none;
        padding: 1rem 1.5rem;
    }
    
    #previewModal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    #previewModal .btn-close {
        filter: invert(1);
        opacity: 0.9;
    }
    
    #previewModal .btn-close:hover {
        opacity: 1;
    }
    
    #previewModal .modal-body {
        min-height: 500px;
        max-height: 80vh;
        overflow: hidden;
        padding: 0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    /* A4-like paper container */
    #previewModalBody {
        width: 100%;
        height: 100%;
        min-height: 500px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    /* PDF iframe - A4 aspect ratio */
    #previewModalBody iframe {
        width: 100%;
        height: calc(100vh * 0.75); /* 75vh for A4-like height */
        min-height: 500px;
        border: none;
        border-radius: 0;
        background: #ffffff;
    }
    
    /* Loading indicator */
    #previewModalBody .text-center {
        padding: 3rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        #previewModal .modal-dialog {
            max-width: 95vw;
            margin: 0.5rem auto;
        }
        
        #previewModal .modal-body {
            max-height: 85vh;
        }
        
        #previewModalBody iframe {
            height: calc(100vh * 0.70);
        }
    }

    @media (min-width: 768px) {
        #my-publications .my-pub-col-index { width: 3rem; min-width: 3rem; }
        #my-publications .my-pub-col-title { min-width: 160px; }
        #my-publications .my-pub-col-description { min-width: 180px; }
        #my-publications .my-pub-col-status { min-width: 88px; }
        #my-publications .my-pub-col-views { min-width: 88px; text-align: center; }
        #my-publications .my-pub-col-created { min-width: 108px; white-space: nowrap; }
        #my-publications .my-pub-col-actions { min-width: 200px; white-space: nowrap; vertical-align: middle; }
    }
    .pub-desc-preview {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: none !important;
        white-space: nowrap;
    }
    @media (max-width: 767.98px) {
        .my-pub-page.row {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .my-pub-shell > .card-body {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
    }
    .pub-desc-preview:hover { text-decoration: underline !important; }
    #pubDescriptionPreviewBody {
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.6;
        color: #334155;
    }
    .pub-filters-card--collapsible:not(.is-expanded) .pub-filters-card__header {
        border-bottom: none;
    }
    .pub-filters-panel-toggle .fa-chevron-down {
        transition: transform 0.2s ease;
    }
    .pub-filters-panel-toggle[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
</style>
@endsection

@section('content')
<div class="row px-3 my-pub-page">

	<div class="card col-lg-12 my-pub-shell">
		<div class="card-header text-left d-flex flex-wrap align-items-center justify-content-between">
			<div>
				<h3 class="card-title">My Publications</h3>
				<p class="my-pub-shell__subtitle">Track your contributions, engagement, and community memberships.</p>
			</div>
			<a href="{{ route('account.publish') }}" class="btn btn-success mt-2 mt-md-0">
				<i class="fa fa-plus mr-1"></i> Create Publication
			</a>
		</div>

		<div class="card-body pb-2">
			<div class="my-pub-quick-links">
				<a href="{{ route('account.my-discussions') }}"><i class="fa fa-comment-dots"></i> My forum posts</a>
				<a href="{{ route('account.my-forums') }}"><i class="fa fa-comments"></i> My Forums</a>
				<a href="{{ route('account.my-communities') }}"><i class="fa fa-users"></i> My Communities</a>
			</div>

			<div class="my-pub-section">
				<h4 class="my-pub-section__title">Overview</h4>
				<div class="metric-grid">
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--green"><i class="fa fa-file-alt"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['total'] ?? 0) }}</div>
							<div class="metric-card__label">Publications</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--green"><i class="fa fa-check-circle"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['approved'] ?? 0) }}</div>
							<div class="metric-card__label">Approved</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--gold"><i class="fa fa-clock"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['pending'] ?? 0) }}</div>
							<div class="metric-card__label">Pending</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--red"><i class="fa fa-eye"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['total_views'] ?? 0) }}</div>
							<div class="metric-card__label">Total views</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--grey"><i class="fa fa-comment-dots"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['forum_posts'] ?? 0) }}</div>
							<div class="metric-card__label">Forum posts</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--plum"><i class="fa fa-reply"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['forum_comments'] ?? 0) }}</div>
							<div class="metric-card__label">Comments</div>
						</div>
					</div>
					<div class="metric-card">
						<div class="metric-card__icon metric-card__icon--green"><i class="fa fa-chart-line"></i></div>
						<div>
							<div class="metric-card__value">{{ number_format($stats['forum_engagements'] ?? 0) }}</div>
							<div class="metric-card__label">Engagements</div>
						</div>
					</div>
				</div>
			</div>

			@if(!empty($stats['communities']) && count($stats['communities']) > 0)
			<div class="my-pub-section">
				<h4 class="my-pub-section__title">Communities of Practice</h4>
				<div class="community-panel">
					<div class="community-panel__head">
						<div>
							<h6>Your memberships</h6>
							<p>Communities where you are an approved member.</p>
						</div>
						<span class="community-panel__count">{{ count($stats['communities']) }}</span>
					</div>
					<div class="community-grid">
						@foreach($stats['communities'] as $community)
							@php
								$communityName = is_array($community) ? ($community['name'] ?? '') : (string) $community;
								$communityUrl = is_array($community) ? ($community['url'] ?? route('account.my-communities')) : route('account.my-communities');
							@endphp
							<a href="{{ $communityUrl }}" class="community-card">
								<span class="community-card__icon"><i class="fa fa-users"></i></span>
								<span>
									<span class="community-card__name">{{ $communityName }}</span>
									<span class="community-card__meta">View community</span>
								</span>
							</a>
						@endforeach
					</div>
				</div>
			</div>
			@endif
		</div>

		<div class="card-body text-left pt-3 border-top">
            @php
                $hasActiveMyPubFilters = request()->filled('search.title')
                    || request()->filled('search.description')
                    || request()->filled('status');
            @endphp
            <div class="pub-filters-card pub-filters-card--collapsible {{ $hasActiveMyPubFilters ? 'is-expanded' : '' }} mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Publications</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                    <button
                        class="pub-filters-advanced-toggle pub-filters-panel-toggle"
                        type="button"
                        data-toggle="collapse"
                        data-target="#myPublicationsFiltersPanel"
                        aria-expanded="{{ $hasActiveMyPubFilters ? 'true' : 'false' }}"
                        aria-controls="myPublicationsFiltersPanel"
                        id="myPublicationsFiltersToggle"
                    >
                        <i class="fa fa-chevron-down mr-1"></i>
                        <span class="pub-filters-panel-toggle__label">{{ $hasActiveMyPubFilters ? 'Hide Filters' : 'Show Filters' }}</span>
                    </button>
                </div>
                <div id="myPublicationsFiltersPanel" class="collapse {{ $hasActiveMyPubFilters ? 'show' : '' }}">
                    <div class="pub-filters-card__body">
                        <form id="myPublicationsFiltersForm" method="GET" action="{{ route('account.publications') }}" class="mb-0">
                            <div class="pub-filters-grid">
                                <div class="pub-filter-field">
                                    <label class="pub-filter-label" for="filterMyPubTitle">Title</label>
                                    <input type="text" name="search[title]" id="filterMyPubTitle" class="form-control pub-filter-input" value="{{ request('search.title') }}" placeholder="Filter by title">
                                </div>
                                <div class="pub-filter-field">
                                    <label class="pub-filter-label" for="filterMyPubDescription">Description</label>
                                    <input type="text" name="search[description]" id="filterMyPubDescription" class="form-control pub-filter-input" value="{{ request('search.description') }}" placeholder="Filter by description">
                                </div>
                                <div class="pub-filter-field">
                                    <label class="pub-filter-label" for="filterMyPubStatus">Status</label>
                                    <select name="status" id="filterMyPubStatus" class="form-control pub-filter-input">
                                        <option value="">All statuses</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pub-filters-actions">
                                <a href="{{ route('account.publications') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearMyPublicationsFilters">
                                    <i class="fa fa-rotate-left"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Publication rows are shown as cards on small screens.</p>
            <div class="publication-table-wrap kh-table-mobile-scroll">
                <table id="my-publications" data-kh-datatable="custom" class="table table-striped table-bordered align-middle w-100 kh-table-mobile-cards kh-table-mobile-cards--medium">
                    <thead>
                        <tr>
                            <th class="my-pub-col-index">#</th>
                            <th class="my-pub-col-title">Title</th>
                            <th class="my-pub-col-description">Description</th>
                            <th class="my-pub-col-status">Status</th>
                            <th class="my-pub-col-views">Total Views</th>
                            <th class="my-pub-col-created">Created At</th>
                            <th class="my-pub-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @include('account.partials.delete_pub')
        </div>
    </div>

</div>

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
<!-- /row -->
@endsection

@section('scripts')
<script>
if (typeof $.fn.DataTable === 'undefined') {
  document.write('\x3Cscript src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"\x3E\x3C/script\x3E');
}
</script>
@include('common.datatable_defaults')
<script>
let myPublicationsTable = null;
let myPublicationsFilterTimer = null;

function collectMyPublicationsFilterParams() {
    const params = {};
    $('#myPublicationsFiltersForm').find('input, select').each(function () {
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

function reloadMyPublicationsTable() {
    if (myPublicationsTable) myPublicationsTable.ajax.reload();
}

function scheduleMyPublicationsFilterReload() {
    clearTimeout(myPublicationsFilterTimer);
    myPublicationsFilterTimer = setTimeout(reloadMyPublicationsTable, 350);
}

$(function(){
  myPublicationsTable = $('#my-publications').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    autoWidth: false,
    scrollX: false,
    pageLength: 10,
    lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
    order: [[5, 'desc']],
    ajax: {
      url: '{{ route('account.publications') }}',
      data: function (d) {
        d.datatable = 1;
        return Object.assign(d, collectMyPublicationsFilterParams());
      }
    },
    columns: [
      { data: 'index', orderable: false, searchable: false, className: 'my-pub-col-index kh-mcard-hide text-center' },
      { data: 'title', orderable: true, className: 'my-pub-col-title kh-mcard-primary' },
      { data: 'description', orderable: true, className: 'my-pub-col-description' },
      { data: 'status', orderable: true, searchable: false, className: 'my-pub-col-status' },
      { data: 'views', orderable: true, searchable: false, className: 'my-pub-col-views text-center' },
      { data: 'created_at', orderable: true, searchable: false, className: 'my-pub-col-created' },
      { data: 'actions', orderable: false, searchable: false, className: 'my-pub-col-actions kh-mcard-actions text-center' }
    ],
    columnDefs: [
      { targets: [0, 6], orderable: false }
    ],
    language: {
      processing: '<i class="fa fa-spinner fa-spin"></i> Loading publications...',
      emptyTable: 'No publications match your filters.',
      zeroRecords: 'No matching publications found.'
    },
  });

  $('#filterMyPubTitle, #filterMyPubDescription').on('input', scheduleMyPublicationsFilterReload);
  $('#filterMyPubStatus').on('change', reloadMyPublicationsTable);

  $('#myPublicationsFiltersForm').on('submit', function (e) {
    e.preventDefault();
    reloadMyPublicationsTable();
  });

  $('#clearMyPublicationsFilters').on('click', function (e) {
    e.preventDefault();
    window.location.href = '{{ route('account.publications') }}';
  });

  $('#myPublicationsFiltersPanel')
    .on('show.bs.collapse', function () {
      $('#myPublicationsFiltersToggle').attr('aria-expanded', 'true');
      $('#myPublicationsFiltersToggle .pub-filters-panel-toggle__label').text('Hide Filters');
      $('.pub-filters-card--collapsible').addClass('is-expanded');
    })
    .on('hide.bs.collapse', function () {
      $('#myPublicationsFiltersToggle').attr('aria-expanded', 'false');
      $('#myPublicationsFiltersToggle .pub-filters-panel-toggle__label').text('Show Filters');
      $('.pub-filters-card--collapsible').removeClass('is-expanded');
    });

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
});
// A4-like page preview modal - ensure it exists
(function() {
    function initPreviewModal() {
        if (!document.getElementById('previewModal')) {
            const modal = `
            <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">
                      <i class="fa fa-eye mr-2"></i>Publication Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" id="previewModalBody">
                    <div class="text-center w-100" style="padding: 3rem;">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading preview...</span>
                      </div>
                      <p class="mt-3 text-muted">Loading preview...</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', modal);
        }
    }
    
    // Initialize modal when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPreviewModal);
    } else {
        initPreviewModal();
    }
    
    // Handle preview button clicks
    $(document).on('click', '.preview-attachment', function() {
        var pageUrl = $(this).data('file-url');
        var modalBody = $('#previewModalBody');
        var modalTitle = $('#previewModalLabel');
        
        // Ensure modal exists
        if (!document.getElementById('previewModal')) {
            initPreviewModal();
        }
        
        // Show loading state
        modalBody.html('<div class="text-center w-100" style="padding: 3rem;"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading preview...</span></div><p class="mt-3 text-muted">Loading preview...</p></div>');
        modalTitle.html('<i class="fa fa-eye mr-2"></i>Publication Preview');
        
        // Small delay to ensure modal is visible before rendering content
        setTimeout(function() {
            modalBody.html('<iframe src="'+pageUrl+'" style="width:100%;height:calc(100vh * 0.75);min-height:500px;border:none;background:#ffffff;" onload="this.style.display=\'block\'"></iframe>');
        }, 100);
        
        // Show modal using Bootstrap 5 API
        var modalElement = document.getElementById('previewModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            // Fallback for Bootstrap 4 or if Bootstrap isn't loaded yet
            $(modalElement).modal('show');
        }
    });
})();
</script>
@endsection