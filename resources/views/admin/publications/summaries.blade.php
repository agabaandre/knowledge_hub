
@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left">{{ $title ?? 'Summaries and Abstracts' }}</h3>
			 <hr>
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
							@include('partials.authors.dropdown',['field'=>'author','selected'=>@$search->author])
						</div>
					</div>
				</div>
            </form>
		</div>
		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Title</th>
						<th>Content</th>
						<th>Author</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>

					@php 
                    $i = 1;
                    @endphp

					@foreach($summaries as $row)
						<tr>
                            <td>{!! truncate($row->title, 30) !!}</td>
							<td>{!! truncate(html_to_text($row->description), 50) !!}</td>
							<td>{{ $row->author->name ?? '' }}</td>
							<td>
                            {{ ($row->approved ==0 && $row->is_rejected==0)?'Pending Approval':(($row->approved ==0)?'Rejected':'Approved') }}
                            </td>
                            <td>
                            <a href="{{ url('admin/publications/summary') }}?id={{$row->id}}"  class="btn btn-primary btn-sm" 
									>Details</a>
                            <a href="{{ url('admin/publications/details') }}?id={{$row->resource_id}}"  class="btn btn-primary btn-sm" 
									>View Original</a>
                            </td>
						</tr>
					@endforeach
				</tbody>
			</table>

            <div class="py-2"> {{$summaries->links() }}</div>

		</div>

	</div>

    @endsection