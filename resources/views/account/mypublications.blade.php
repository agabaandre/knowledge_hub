@extends('layouts.plain')

@section('styles')
@include('common.table')
<link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">

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
            <table id="my-publications" class="table table-striped table-bordered align-middle">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Description</th>
                        <th>Status</th>
                        <th>Total Views</th>
                        <th>Created At</th>
                        <th width="240">Actions</th>
					</tr>
				</thead>
                <tbody></tbody>
            </table>


            @include('account.partials.delete_pub')

        </div>
    </div>

</div>
<!-- /row -->
@endsection

@section('scripts')
<script>
// Ensure DataTables library is loaded for non-admin layout
if (typeof $.fn.DataTable === 'undefined') {
  document.write('\x3Cscript src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"\x3E\x3C/script\x3E');
}
$(function(){
  $('#my-publications').DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    lengthChange: true,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"p>>rtip',
    ajax: {
      url: '{{ route('account.publications') }}',
      data: function(d){ d.datatable = 1; }
    },
    order: [[0,'desc']],
    columns: [
      { data: 0, orderable: true, searchable: false, width: '5%' },
      { data: 1, orderable: true },
      { data: 2, orderable: false },
      { data: 3, orderable: true, searchable: false },
      { data: 4, orderable: true, searchable: false },
      { data: 5, orderable: true, searchable: false },
      { data: 6, orderable: false, searchable: false, width: '240px' }
    ],
    drawCallback: function(){
      // enable bootstrap tooltips/popovers if needed later
    }
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