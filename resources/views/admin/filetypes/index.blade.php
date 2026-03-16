@extends(admin_layout())

@section('styles')
 @include('common.table')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card-body{padding:16px}
 </style>
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12 af-card">
		<div class="af-card-header d-flex align-items-center justify-content-between">
			<strong>{{ $title ?? 'File Types' }}</strong>
			<a href="#create-modal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add File Type</a>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="af-card-body">
			@include('layouts.partials.alerts')
			<form  class="container-fluid">
				  <div class="row">

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
		<div class="af-card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width:60px;">#</th>
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
							<td>{{ $filetypes->firstItem() + $loop->index }}</td>
							<td>{{ $row->name }}</td>
							<td>{{ $row->icon }}</td>
							<td>{{ $row->is_downloadable ? 'YES' : 'NO' }}</td>
							<td>
							<a href="#edit-filetype-modal" data-toggle="modal" data-id="{{ $row->id }}" data-name="{{ $row->name }}" data-icon="{{ $row->icon }}" data-downloadable="{{ $row->is_downloadable }}" class="btn btn-sm btn-outline-dark ml-1"><i class="fa fa-edit"></i></a>
								@can('delete_publication_metadata')
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
								@endcan
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