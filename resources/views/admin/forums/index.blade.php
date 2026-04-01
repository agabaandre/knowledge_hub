@extends(admin_layout())

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
<div class="row">
	<div class="card col-lg-12">
		<div class="filter-card">
			<div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
				<div class="flex-grow-1">
					<strong>{{ $title ?? 'Forums' }}</strong>
					<small class="text-muted d-block">{{ $forum_list_subtitle ?? 'Search and manage discussion threads' }}</small>
				</div>
				<div class="btn-group btn-group-sm" role="group" aria-label="Forum queues">
					<a href="{{ url('admin/forums') }}" class="btn {{ ($forum_admin_queue ?? '') === 'pending' ? 'btn-dark' : 'btn-outline-secondary' }}">Pending</a>
					<a href="{{ url('admin/forums/approved') }}" class="btn {{ ($forum_admin_queue ?? '') === 'approved' ? 'btn-dark' : 'btn-outline-secondary' }}">Approved</a>
					<a href="{{ url('admin/forums/rejected') }}" class="btn {{ ($forum_admin_queue ?? '') === 'rejected' ? 'btn-dark' : 'btn-outline-secondary' }}">Rejected</a>
				</div>
				@if(isset($pending_forums_count) && $pending_forums_count > 0)
					<div class="dropdown nav-item">
						<a class="nav-link position-relative" href="{{ url('admin/forums/moderate') }}" title="Pending Forums">
							<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
								<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
								<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
							</svg>
							<span class="badge badge-danger badge-pill" style="position:absolute;top:-4px;right:-6px;min-width:20px;">{{ $pending_forums_count }}</span>
						</a>
					</div>
				@endif
			</div>
			<div class="card-body">
				<form class="container-fluid">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label class="form-label-sm" for="title">Keyword</label>
								<input type="text" name="term" id="filterTitle" class="form-control" placeholder="Filter by title or description" value="{{ @$search->term ?? ''}}">
							</div>
						</div>
						<div class="col-md-4 d-flex align-items-end justify-content-end" style="gap:8px;">
							<button type="submit" id="filterButton" class="btn btn-dark btn-sm"><i class="fa fa-filter mr-1"></i> Apply</button>
							<button type="button" id="reset" class="btn btn-soft btn-sm"><i class="fa fa-rotate-left mr-1"></i> Reset</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="card-body text-left">
			<div class="table-responsive">
			<table id="publicationTable" class="table table-striped table-hover table-bordered">
				<thead class="thead-light">
					<tr>
						<th style="width:60px;">#</th>
						<th>Forum Title</th>
						<th>Description</th>
						<th>Author</th>
						<th width="12%">Created</th>
						<th width="18%">Approved/Rejected By</th>
						<th width="24%">Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($forums as $idx => $row)
						<tr>
							<td><span class="text-muted">{{ $forums->firstItem() + $idx }}</span></td>
							<td>
								{{ $row->forum_title }}
								@include('admin.forums.partials.resubmission-badge', ['forum' => $row, 'class' => 'ml-1 align-middle'])
							</td>
							<td>{!! truncate(strip_tags($row->forum_description), 100) !!}</td>
							<td>{!! truncate(strip_tags($row->user->name), 100) !!}</td>
							<td>{!! time_ago($row->created_at) !!}</td>
							<td>
								@php
									$name = '-';
									if (!empty($row->approved_by)) { $u=\App\Models\User::find($row->approved_by); $name=$u->name ?? '-'; }
									elseif (!empty($row->rejected_by)) { $u=\App\Models\User::find($row->rejected_by); $name=($u->name??'Rejected'); }
								@endphp
								<span class="text-muted">{{ $name }}</span>
							</td>
							<td>
							    <a class="btn btn-sm btn-outline-dark mr-1" href="{{ url('admin/forums/details')}}?id={{$row->id}}"><i class="fa fa-info-circle mr-1"></i> Details</a>
								<a class="btn btn-sm btn-outline-danger" href="javascript:void(0);" onclick="openDeleteModal('{{ $row->id }}')"><i class="fa fa-trash mr-1"></i> Delete</a>
								<a class="btn btn-sm btn-outline-secondary ml-1" target="_blank" href="{{ url('forums/thread')}}?id={{$row->id}}"><i class="fa fa-external-link mr-1"></i> View Website</a>
							</td>
						</tr>
						@include('admin.forums.partials.details-modal',['forum'=>$row])
					@endforeach
				</tbody>
			</table>
			</div>
			<div class="py-2"> {{$forums->links() }}</div>

		</div>

	</div>

	<!-- Include delete-modal.php -->
	@include('admin.forums.partials.delete-modal')

    @endsection