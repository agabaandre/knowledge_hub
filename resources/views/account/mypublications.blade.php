@extends('layouts.plain')

@section('styles')


 @include('common.table')

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
            <table class="table table-striped table-bordered align-middle">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Description</th>
                        <th>Status</th>
                        <th width="240">Actions</th>
					</tr>
				</thead>
				@foreach ($publications as $idx => $row) 
					<tr>
						<td width="5%">{{ $publications->firstItem() + $idx }}</td>
						<td>
							<a href="{{ $row->publication}}" target="_blank">{!! truncate($row->title, 50) !!} </a>
						</td>
                        <td>{!!  truncate(html_to_text($row->description), 80) !!}
                            @if(($row->is_rejected ?? 0) == 1 && !empty($row->rejected_reason))
                                <div class="mt-2 p-2" style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;">
                                    <small class="text-danger"><strong>Rejection reason:</strong> {{ $row->rejected_reason }}</small>
                                </div>
                            @endif
                        </td>
                        <td>
                            @php $state = get_publication_state($row->is_approved,$row->is_rejected); @endphp
                            <span class="badge {{ $row->is_approved? 'badge-success':'badge-secondary' }}">{{ $state }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                <button type="button" class="btn btn-outline-secondary preview-attachment" data-file-url="{{ url('records/resource') }}?id={{ $row->id }}" data-file-ext="html" data-file-office="0"><i class="fa fa-eye"></i> Preview</button>
                                <a href="{{ route('account.publications.edit') }}?ref={{ $row->id}}" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Edit</a>
                                <a href="javascript:void(0);" onclick="openDeleteModal({{$row->id}})" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</a>
                            </div>
                        </td>
					</tr>
				@endforeach
				@if(count($publications) == 0)

				@endif
            </table>

            {{ $publications->appends(request()->all())->links() }}

            @include('account.partials.delete_pub')

        </div>
    </div>

</div>
<!-- /row -->
@endsection

@section('scripts')
<script>
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