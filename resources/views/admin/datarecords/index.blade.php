
@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? '' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				
				  <div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="title">Title</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by Title"
                                value="{{ @$search->term ?? ''}}"
							>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="source">Description</label>
							<input type="text" name="description" id="filterDesc" class="form-control" 
                              value="{{ @$search->description ?? ''}}"
                              placeholder="Filter by Description">
						</div>
					</div>
					<div class="col-lg-4">
						
					   <a href="{{ url('admin/datarecords/create') }}" class="btn btn-dark mt-4">Add Categories Data</a>
                    
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
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($records as $record)
						<tr>
							<td width="5%"><i class="fa {{ $record->file_type->icon }} fa-2x text-muted"></i></td>
							<td>
                              <a href="{{ $record->publication }}" target="_blank">
								{!!truncate($record->title, 30) !!}
                              </a>
							</td>
							<td>
								{!! truncate(strip_tags($record->description), 100) !!}
							</td>
							<td>
								{{ $record->country->name ?? '' }}
							</td>
							<td>
							
								<!-- Edit Modal Action -->
								<a href="{{ url('admin/datarecords/edit') }}?id={{$record->id}}" type="button" class="btn btn-primary btn-sm" 
								>Edit</a>
								<!-- Delete Modal Action -->
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $record->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$records->links() }}</div>

		</div>

	</div>

	
	<!-- Include delete-modal.php -->
	@include('admin.datarecords.partials.delete-modal')

    @endsection