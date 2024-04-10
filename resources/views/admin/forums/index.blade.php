@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left mt-2 mb-2">{{ $title ?? 'Forums' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
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
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Forum Title</th>
						<th>Description</th>
						<th>Author</th>
						<th width="10%">Published</th>
						<th width="20%">Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($forums as $row)
						<tr>
							<td>{{ $row->forum_title }}</td>
							<td>{!! truncate($row->forum_description,100) !!}</td>
							<td>{!! truncate($row->user->name,100) !!}</td>
							<td>{!! time_ago($row->created_at) !!}</td>
							<td>
							    <a class="btn btn-primary text-white btn-sm mr-1"  href="{{ url('admin/forums/details')}}?id={{$row->id}}"  class="text-danger"> Details</a>
								<a class="btn btn-danger btn-sm" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
								<a class=" btn btn-dark btn-sm ml-1" target="_blank" href="{{ url('forums/thread')}}?id={{$row->id}}"  class="text-danger"> Preview</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$forums->links() }}</div>

		</div>

	</div>

	<!-- Include delete-modal.php -->
	@include('admin.forums.partials.delete-modal')

    @endsection