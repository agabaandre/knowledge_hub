
@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Geographical Areas' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">

                    <div class="col-md-12 text-right">
                        <a href="#create-area-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add New Area</a>
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
					<div class="col-md-12 text-left">
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
						<th>Area Name</th>
						<th>ISO Code</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php
                    $i = 1;
                    @endphp

					@foreach($areas as $row)
						<tr>
							<td><img src="{{asset('assets/img/flags/' . @$row->flag)}}" width="30px"></td>
							<td>{{ $row->name }}</td>
							<td>{{ $row->iso_code }}</td>
							<td>
                                <a href="#edit-area-modal" data-toggle="modal" 
									data-id="{{ $row->id }}" 
									data-name="{{ $row->name }}" 
									data-flag="{{asset('assets/img/flags/' . @$row->flag)}}"
									dat-iso_code="{{ $row->iso_code }}" class="btn btn-sm btn-success ml-1">Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$areas->links() }}</div>

		</div>

	</div>

    <!-- Include create-modal.php -->
    @include('admin.areas.partials.create-modal')
	<!-- Include edit-modal.php -->
	@include('admin.areas.partials.edit-modal')
	<!-- Include delete-modal.php -->
	@include('admin.areas.partials.delete-modal')

    @endsection
