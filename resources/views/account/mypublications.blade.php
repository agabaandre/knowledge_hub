@extends('layouts.plain')

@section('styles')
@include('common.table')
<link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">

<style>
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

	
		<div class="card-body text-left">
			<form method="GET" class="row mb-3">
				<div class="col-md-6">
					<input type="text" name="term" value="{{ request('term') }}" class="form-control" placeholder="Search title or description">
				</div>
				<div class="col-md-2">
					<select name="rows" class="form-control">
						@foreach([10,20,50,100] as $r)
							<option value="{{ $r }}" {{ request('rows')==$r ? 'selected' : '' }}>{{ $r }}/page</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-2">
					<button class="btn btn-dark" type="submit"><i class="fa fa-search"></i> Search</button>
				</div>
			</form>
            <table id="my-publications" class="table table-striped table-bordered align-middle">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Description</th>
                        <th>Status</th>
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
    searching: true,
    lengthChange: true,
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
      { data: 4, orderable: false, searchable: false, width: '240px' }
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