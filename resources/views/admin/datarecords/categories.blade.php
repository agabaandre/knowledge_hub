
@extends(admin_layout())

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? '' }}</h3>
				<a href="#add_category" data-toggle="modal" class="btn btn-info">Add Category</a>
			    <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
		
		</div>
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Category</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($categories as $record)
						<tr>
							<td>
								{!!truncate(strip_tags($record->category_name), 100) !!}
							</td>
							<td>
							
								<!-- Edit Modal Action -->
								<a class="btn btn-primary btn-sm" 
								>Edit</a>
								<!-- Delete Modal Action -->
                                @can('delete_publication_metadata')
                                <a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick='openDeleteModal({{ (int) $record->id }}, @json((string) $record->category_name))' class="text-danger"> Delete</a>
                                @endcan
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$categories->links() }}</div>

		</div>

	</div>

	<!-- Include delete-modal.php -->
	@include('admin.datarecords.partials.delete-category-modal')
	@include('admin.datarecords.partials.add-category-modal')

    @endsection