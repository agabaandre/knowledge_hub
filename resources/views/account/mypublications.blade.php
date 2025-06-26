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
			<table class="table table-striped table table-bordered">
				<thead>
					<tr>
						<th></th>
						<th>Title</th>
						<th>Description</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				  @php
                  $i = 1;
                  @endphp
				@foreach ($publications as $row) 
					<tr>
						<td width="5%"><i class="fa {{@$row->file_type->icon}} text-muted"></i></td>
						<td>
							<a href="{{ $row->publication}}" target="_blank">{!! truncate($row->title, 50) !!} </a>
						</td>
						<td>{!!  truncate(html_to_text($row->description), 80) !!}</td>
						<td><small>{{  get_publication_state($row->is_approved,$row->is_rejected)}}</small></td>
						<td>
							<a href="{{ route('account.publications.edit') }}?ref={{ $row->id}}"><i class="fa fa-edit"></i> Edit
							<a href="javascript:void(0);" onclick="openDeleteModal({{$row->id}})" class="text-danger  ml-3">
							<i class="fa fa-trash"></i> Delete
						 </td>
					</tr>
				@endforeach
				@if(count($publications) == 0)

				@endif
			</table>

			{{ $publications->links() }}

			@include('account.partials.delete_pub')

		</div>
	</div>

</div>
<!-- /row -->
@endsection