@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'File Types' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add File Type</a>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="title">Search</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by name"
                                value="{{ @$search->term ?? ''}}"
							>
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
						<th>#</th>
						<th>Filetype Name</th>
						<th>Filetype Icon</th>
						<th>Is Downloadable</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($filetypes as $row)
						<tr>
							<td>{{ $row->id }}</td>
							<td>{{ $row->name }}</td>
							<td>{{ $row->icon }}</td>
							<td>{{ $row->is_downloadable ? 'YES' : 'NO' }}</td>
							<td>
							<a href="#edit-filetype-modal" data-toggle="modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-icon="{{ $row->icon }}" data-downloadable="{{ $row->is_downloadable }}" class="btn btn-sm btn-primary ml-1">Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$filetypes->links() }}</div>

		</div>

	</div>

	@include('admin.filetypes.partials.create-modal')
	<!-- Include edit-modal.php -->
	@include('admin.filetypes.partials.edit-modal')
	<!-- Include delete-modal.php -->
	@include('admin.filetypes.partials.delete-modal')

    @endsection