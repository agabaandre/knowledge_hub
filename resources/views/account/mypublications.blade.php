@extends('layouts.plain')

@section('styles')

@endsection

@section('content')
<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">My Publications</h3>
		</div>


		<div class="card-body text-left">
			<table class="table table-striped table table-bordered">
				<thead>
					<tr>

						<th>#</th>
						<th>Title</th>
						<th>Description</th>
						<th>Edit/Delete</th>
					</tr>
				</thead>
				  @php
                  $i = 1;
                  @endphp
				@foreach ($publications as $row) 
					<tr>
						<td width="5%">{{$i++}} <i class="fa {{$row->file_type->icon}} fa-2x text-muted"></i></td>
						<td>
							{{ truncate($row->title, 50) }} 
							<p><a href="{{ $row->publication}}" target="_blank">View Publication</a>
							<p>
						</td>

						<td>
							{{ truncate($row->description, 20)}}
						</td>


						<td><a href="#edit{{ $row->id}}"><i class="fa fa-edit"></i> Edit
								<a href="javascript:void(0);" onclick="openDeleteModal({{$row->id}})" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
					</tr>
				@endforeach
			</table>

			@include('account.partials.delete_pub')

		</div>
	</div>

</div>
<!-- /row -->
@endsection