@extends('layouts.plain')

@section('styles')
@include('common.table')
@include('admin.publications.partials.filter_styles')
<style>
    /* Stat card styles with colored backgrounds */
    .stat-card {
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none;
        color: white !important;
        min-height: auto;
    }
    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .stat-card .stat-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom: 0.4rem;
    }
    .stat-card .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: white !important;
        margin-bottom: 0.15rem;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.9) !important;
        text-transform: uppercase;
        font-weight: 500;
        line-height: 1.2;
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

    #my-publications_wrapper table.dataTable { table-layout: fixed !important; }
    #my-publications .my-pub-col-index { width: 3rem; min-width: 3rem; }
    #my-publications .my-pub-col-title { width: 18%; }
    #my-publications .my-pub-col-description { width: 26%; }
    #my-publications .my-pub-col-status { width: 8%; }
    #my-publications .my-pub-col-views { width: 6rem; text-align: center; }
    #my-publications .my-pub-col-created { width: 9%; }
    #my-publications .my-pub-col-actions { width: 11rem; white-space: nowrap; vertical-align: middle; }
    #my-publications .pub-title-link {
        word-break: break-word;
        overflow-wrap: anywhere;
    }
    .pub-desc-preview {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .pub-desc-preview:hover { text-decoration: underline !important; }
    #pubDescriptionPreviewBody {
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.6;
        color: #334155;
    }
</style>
@endsection

@section('content')
<div class="row px-3">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">My Publications</h3>
			<div class="float-right">
				<a href="{{ route('account.publish') }}" class="btn btn-success">
					<i class="fa fa-plus mr-1"></i> Create Publication
				</a>
			</div>
			<div class="clearfix"></div>
		</div>

		<!-- Statistics Cards -->
		<div class="card-body">
			<!-- Action Buttons -->
			<div class="row mb-4">
				<div class="col-12">
					<div class="d-flex flex-wrap" style="gap: 0.5rem;">
						<a href="{{ route('account.my-discussions') }}" class="btn btn-au btn-sm">
							<i class="fa fa-comment-dots mr-1"></i> My forum posts
						</a>
						<a href="{{ route('account.my-forums') }}" class="btn btn-au btn-sm">
							<i class="fa fa-comments mr-1"></i> My Forums
						</a>
						<a href="{{ route('account.my-communities') }}" class="btn btn-au btn-sm">
							<i class="fa fa-users mr-1"></i> My Communities
						</a>
					</div>
				</div>
			</div>
			
			<h5 class="mb-3"><i class="fa fa-file-alt mr-2"></i>Publications Statistics</h5>
			<div class="row mb-3">
				<div class="col-md-3 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_corporate_green ?? '#1A5632' }} 0%, {{ settings()->au_green ?? '#1A5632' }} 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-file-alt"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
						<div class="stat-label">Total Publications</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_green ?? '#1A5632' }} 0%, #0d7a3a 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-check-circle"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['approved'] ?? 0) }}</div>
						<div class="stat-label">Approved</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_gold ?? '#B4A269' }} 0%, #9a884f 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-clock"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['pending'] ?? 0) }}</div>
						<div class="stat-label">Pending</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_red ?? '#9F2241' }} 0%, #7a1a33 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-eye"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['total_views'] ?? 0) }}</div>
						<div class="stat-label">Total Views</div>
					</div>
				</div>
			</div>
			
			<h5 class="mb-3 mt-3"><i class="fa fa-comments mr-2"></i>Forum Engagement Statistics</h5>
			<div class="row mb-3">
				<div class="col-md-4 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_grey_text ?? '#58595B' }} 0%, #464749 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-comment-dots"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['forum_posts'] ?? 0) }}</div>
						<div class="stat-label">Forum Posts</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_plum ?? '#522B39' }} 0%, #3d1f2a 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-reply"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['forum_comments'] ?? 0) }}</div>
						<div class="stat-label">Forum Comments</div>
					</div>
				</div>
				<div class="col-md-4 col-sm-6 mb-2">
					<div class="stat-card" style="background: linear-gradient(135deg, {{ settings()->au_corporate_green ?? '#1A5632' }} 0%, {{ settings()->au_green ?? '#1A5632' }} 100%);">
						<div class="stat-icon-wrapper">
							<div class="stat-icon">
								<i class="fa fa-chart-line"></i>
							</div>
						</div>
						<div class="stat-value">{{ number_format($stats['forum_engagements'] ?? 0) }}</div>
						<div class="stat-label">Total Engagements</div>
					</div>
				</div>
			</div>
			
			@if(!empty($stats['communities']) && count($stats['communities']) > 0)
			<h5 class="mb-3 mt-3"><i class="fa fa-users mr-2"></i>Communities of Practice</h5>
			<div class="row mb-3">
				<div class="col-12">
					<div class="card" style="border-radius: 0.25rem; border: 1px solid #e0e0e0;">
						<div class="card-body p-3">
							<div class="d-flex align-items-center mb-2">
								<i class="fa fa-users fa-lg text-primary mr-2"></i>
								<div>
									<h6 class="mb-0">You belong to <strong>{{ count($stats['communities']) }}</strong> {{ count($stats['communities']) == 1 ? 'Community' : 'Communities' }}</h6>
									<small class="text-muted">Active member of the following Communities of Practice</small>
								</div>
							</div>
							<div class="mt-2">
								@foreach($stats['communities'] as $community)
									<span class="badge badge-primary mr-1 mb-1" style="font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.25rem;">
										<i class="fa fa-circle mr-1" style="font-size: 0.6rem;"></i>{{ $community }}
									</span>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	
		<div class="card-body text-left">
            <div class="pub-filters-card mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter Publications</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                </div>
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

            <div class="publication-table-wrap">
                <table id="my-publications" data-kh-datatable="custom" class="table table-striped table-bordered align-middle w-100 kh-table-wrap-cells">
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
      { data: 'index', orderable: false, searchable: false, className: 'my-pub-col-index text-center' },
      { data: 'title', orderable: true, className: 'my-pub-col-title' },
      { data: 'description', orderable: true, className: 'my-pub-col-description' },
      { data: 'status', orderable: true, searchable: false, className: 'my-pub-col-status' },
      { data: 'views', orderable: true, searchable: false, className: 'my-pub-col-views text-center' },
      { data: 'created_at', orderable: true, searchable: false, className: 'my-pub-col-created' },
      { data: 'actions', orderable: false, searchable: false, className: 'my-pub-col-actions text-center' }
    ],
    columnDefs: [
      { targets: [0, 6], orderable: false }
    ],
    language: {
      processing: '<i class="fa fa-spinner fa-spin"></i> Loading publications...',
      emptyTable: 'No publications match your filters.',
      zeroRecords: 'No matching publications found.'
    }
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