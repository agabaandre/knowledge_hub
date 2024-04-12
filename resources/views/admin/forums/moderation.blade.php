@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left mt-2 mb-2">{{ $title ?? 'Pending Forums' }}</h3>
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
						<button type="submit" id="filterButton" class="btn btn-primary btn-md">Filter Data</button>
						<button type="button" id="reset" class="btn btn-secondary btn-md">Reset</button>
                       
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
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($forums as $row)
						<tr>
							<td>{{ $row->forum_title }}</td>
							<td>{!! truncate(strip_tags($row->forum_description), 100) !!}</td>
							<td>{!! truncate($row->user->name,100) !!}</td>
							<td>
								<a class="btn btn-sm btn-default" href="#details{{$row->id}}" data-toggle="modal" >Details</a>
								<a class=" ml-1" target="_blank" href="{{ url('forums/thread')}}?id={{$row->id}}"  class="text-danger"> Preview</a>
							</td>
						</tr>

                        @include('admin.forums.partials.details-modal',['forum'=>$row])

					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$forums->links() }}</div>

		</div>

	</div>

	<!-- Include delete-modal.php -->
	@include('admin.forums.partials.delete-modal')

    @endsection