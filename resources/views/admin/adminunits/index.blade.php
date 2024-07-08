@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Administrative Units' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">
				   
				    <div class="col-md-12 text-right">
					 <a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Admin Unit</a>
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

					@include('admin.adminunits.partials.create-modal',['row'=>null])
	
					
				</div>
            </form>
		</div>
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						
					    <th>Logo</th>
						<th>Unit Name</th>
						<th>Description</th>
						<th>Parent</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($adminunits as $row)
						<tr>
						    <td><img src="{{ ($row->logo)?storage_link("uploads/adminunits/".$row->logo) : asset("assets/images/placeholder.pg") }}" width="50px" class="img img-thumbnail"/></td>
							<td>{{ $row->name }}</td>
							<td>{{ $row->description }}</td>
							<td>{{ ($row->parent)?$row->parent->name:"N/A" }}</td>
							<td>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
								<a class="btn btn-sm btn-primary ml-1" href="#edit{{$row->id}}" data-toggle="modal"  class="text-danger"> Edit</a>
							</td>
						</tr>
						@include('admin.adminunits.partials.edit-modal',['row'=>$row])
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$adminunits->links() }}</div>

		</div>

	</div>

	
	<!-- Include delete-modal.php -->
	@include('admin.adminunits.partials.delete-modal')

    @endsection

	@section('scripts')

   	 @include('common.attachment_js')

	@endsection