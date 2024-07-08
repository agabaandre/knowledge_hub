@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="page-header">
	<h1 class="page-title">{{ $description ?? 'Sub-Thematic Area' }}</h1>
	<div>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Dropdown Lists</a></li>
			<li class="breadcrumb-item active" aria-current="page">Security Sub-Themes</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Sub Thematic Areas' }}</h3>
			 <hr>
		</div>
		<!-- Card Header With Form Filters -->
		<div class="card-header">
			<form  class="container-fluid">
				  <div class="row">

				    <div class="col-md-12 text-right">
					 <a href="#create-subtheme-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Sub Thematic Area</a>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<label for="title">Search</label>
							<input type="text" name="term" id="filterTitle" class="form-control"
								placeholder="Filter by name"
                                value="{{ @$search->term ?? ''}}">
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
                        <th style="width:3px;">#</th>
                        <th>Sub Thematic Area</th>
						<th>Icon</th>
						<th>Thematic Area</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php
$i = 1;
                    @endphp

					@foreach($subthemes as $row)
						<tr>
							<td>{{ $i++ }}</td>
							<td>{{ $row->description }}</td>
							<td>{{ $row->icon }}</td>
							<td>{{ $row->theme->description }}</td>
							<td>
								<a href="#edit-subtheme-modal " data-toggle="modal" data-id="{{ $row->id }}" data-description="{{ $row->description }}" data-icon="{{ $row->icon }}" data-thematic_area_id="{{ $row->thematic_area_id }}" class="btn btn-sm btn-primary ml-1">Edit</a>
								<a class="btn btn-sm btn-danger ml-1" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')" class="text-danger"> Delete</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$subthemes->links() }}</div>

		</div>

	</div>

	@include('admin.subthemes.partials.create-modal', [
	'themes' => $themes
])
	<!-- Include edit-modal.php -->
	@include('admin.subthemes.partials.edit-modal', [
	'themes' => $themes
])
	<!-- Include delete-modal.php -->
	@include('admin.subthemes.partials.delete-modal')

    @endsection
