
@extends('admin.layouts.main')

@section('styles')
 @include('common.table')
 <style>
    .filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }
    .filter-card .card-header { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    .btn-soft { border:1px solid #cbd5e1; background:#ffffff; }
    .btn-soft:hover { background:#f8fafc; }
    .form-label-sm { font-size:.875rem; font-weight:600; color:#334155; }
 </style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Publication Summaries</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Publish</a></li>
            <li class="breadcrumb-item active" aria-current="page">Publication Summaries</li>
        </ol>
    </div>
</div>
<div class="row">
	<div class="card col-lg-12">
    <div class="filter-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <strong>Filter Summaries</strong>
                <small class="text-muted d-block">Quick keyword and author filters</small>
            </div>
        </div>
        <div class="card-body">
            <form  class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-sm" for="title">Keyword</label>
                            <input type="text" name="term" id="filterTitle" class="form-control" placeholder="Title or keyword" value="{{ @$search->term ?? ''}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-sm">Source / Author</label>
                            @include('partials.authors.dropdown',['field'=>'author','selected'=>@$search->author])
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end" style="gap:8px;">
                    <button type="submit" class="btn btn-dark btn-sm"><i class="fa fa-filter mr-1"></i> Apply</button>
                    <button type="button" id="reset" class="btn btn-soft btn-sm"><i class="fa fa-rotate-left mr-1"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>
		<div class="card-body text-left">
			<!-- Datatable -->
            <div class="table-responsive mt-3">
            <table id="publicationTable" class="table table-striped table-hover table-bordered">
                <thead class="thead-light">
					<tr>
                        <th style="width:60px;">#</th>
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

                @foreach($summaries as $idx => $row)
                        <tr>
                            <td><span class="text-muted">{{ $summaries->firstItem() + $idx }}</span></td>
                            <td>{!! truncate($row->title, 30) !!}</td>
							<td>{!! truncate(html_to_text($row->description), 50) !!}</td>
							<td>{{ $row->author->name ?? '' }}</td>
							<td>
                            {{ ($row->approved ==0 && $row->is_rejected==0)?'Pending Approval':(($row->approved ==0)?'Rejected':'Approved') }}
                            </td>
                            <td>
                            <a href="{{ url('admin/publications/summary') }}?id={{$row->id}}"  class="btn btn-sm btn-outline-primary mr-1"><i class="fa fa-eye mr-1"></i> Details</a>
                            <a href="{{ url('admin/publications/details') }}?id={{$row->resource_id}}"  class="btn btn-sm btn-outline-dark"><i class="fa fa-external-link mr-1"></i> Original</a>
                            </td>
						</tr>
					@endforeach
				</tbody>
			</table>
            </div>

            <div class="py-2"> {{$summaries->links() }}</div>

		</div>

	</div>

    @endsection

@section('scripts')
    @parent
    @include('common.select2')
@endsection