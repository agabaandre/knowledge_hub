@extends('layouts.plain')

@section('styles')


 @include('common.table')
 <link href="{{ asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">

@endsection

@section('content')
<div class="row px-3">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">My Publications</h3>
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
// Simple page preview using modal to show resource page in iframe
if (!document.getElementById('previewModal')) {
  const modal = `
  <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95%">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="previewModalLabel">Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="previewModalBody" style="min-height:70vh;display:flex;align-items:center;justify-content:center;background:#f8fafc;">
          <div class="text-center w-100">Loading preview...</div>
        </div>
      </div>
    </div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', modal);
}

$(document).on('click', '.preview-attachment', function() {
    var pageUrl = $(this).data('file-url');
    $('#previewModalBody').html('<iframe src="'+pageUrl+'" style="width:100%;height:75vh;border:none;"></iframe>');
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
});
</script>
@endsection