
@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection


@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Public Health Resources</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Public Health Resource</li>
        </ol>
    </div>
</div>

<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">

		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="title">Title</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by Title"
                                value="{{ @$search->term ?? ''}}"
							>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="source">Description</label>
							<input type="text" name="description" id="filterDesc" class="form-control" 
                              value="{{ @$search->description ?? ''}}"
                              placeholder="Filter by Description">
						</div>
					</div>
                    
					<div class="col-md-3">
						<div class="form-group">
							<label for="source">Source / Author</label>
							@include('partials.authors.dropdown', ['field' => 'author', 'selected' => @$search->author])
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="file_type">File Type</label>
							@include('partials.publications.filetype_dropdown',['field'=>'file_type','selected'=>@$search->file_type])
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 text-right">
						<!-- Export Button -->
						<button type="submit" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
						<button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                        <button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>
						
					</div>
				</div>
            </form>
		</div>
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th></th>
						<th>Title</th>
						<th>Description</th>
						<th>Author</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($publications as $publication)
						<tr>
							<td width="5%"><i class="fa {{ $publication->file_type->icon }} fa-2x text-muted"></i></td>
							<td>
                              <a href="{{ $publication->publication }}" target="_blank">
								{!!truncate($publication->title, 30) !!}
                              </a>
							</td>
							<td>
								{!! truncate(html_to_text($publication->description), 50) !!}
							</td>
							<td>
								{{ $publication->author->name ?? '' }}
							</td>
							<td>
								{{ $publication->is_active }}
							</td>
							<td>
								<a href="{{ url('admin/publications/details') }}?id={{$publication->id}}"  class="btn btn-primary btn-sm" 
									>Details</a>

								@if($publication->user_id == current_user()->id)
									<a href="{{ url('admin/publications/edit') }}?id={{$publication->id}}"  class="btn btn-primary btn-sm" 
									>Edit</a>
								@endif

								@can('delete_publications')
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $publication->id }}')" class="text-danger"> Delete</a>
								@endcan
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$publications->links() }}</div>

		</div>

	</div>

	<!-- Include edit-modal.php -->
	@include('admin.publications.partials.edit-modal')
	<!-- Include delete-modal.php -->
	@include('admin.publications.partials.delete-modal')

    @endsection