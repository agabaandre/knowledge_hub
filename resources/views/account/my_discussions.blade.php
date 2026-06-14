@extends('layouts.plain')

@section('title', 'My forum posts')

@section('content')
<div class="row px-3">
	<div class="card col-lg-12">
		<div class="card-header text-left d-flex flex-wrap justify-content-between align-items-center gap-2">
			<h3 class="card-title mb-0">My forum posts</h3>
			<div class="d-flex flex-wrap" style="gap: 0.5rem;">
				<a href="{{ route('forums.create') }}" class="btn btn-success btn-sm">
					<i class="fa fa-plus mr-1"></i> Start new discussion
				</a>
				<a href="{{ route('account.publications') }}" class="btn btn-outline-secondary btn-sm">
					<i class="fa fa-file-alt mr-1"></i> My publications
				</a>
			</div>
		</div>
		<div class="card-body">
			<form method="get" action="{{ route('account.my-discussions') }}" class="mb-4">
				<div class="input-group">
					<input type="text" name="term" class="form-control" placeholder="Search title or content…" value="{{ request('term') }}">
					<button class="btn btn-outline-secondary" type="submit">Search</button>
				</div>
			</form>

			<div class="table-responsive">
				<table class="table table-striped table-bordered align-middle">
					<thead>
						<tr>
							<th>Title</th>
							<th>Status</th>
							<th>Submitted</th>
							<th style="min-width: 220px;">Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($threads as $thread)
							@php
								$isRejected = (int) ($thread->is_rejected ?? 0) === 1;
								$isLive = (int) ($thread->is_approved ?? 0) === 1 && (int) ($thread->status ?? 0) === 1;
								$isPending = ! $isRejected && ! $isLive;
							@endphp
							<tr>
								<td>
									<strong>{{ $thread->forum_title }}</strong>
									@if($isRejected && !empty($thread->rejected_reason))
										<div class="small text-danger mt-1">
											<strong>Moderator note:</strong> {{ \Illuminate\Support\Str::limit(strip_tags($thread->rejected_reason), 200) }}
										</div>
									@endif
								</td>
								<td>
									@if($isLive)
										<span class="badge bg-success">Published</span>
									@elseif($isRejected)
										<span class="badge bg-danger">Rejected</span>
									@else
										<span class="badge bg-warning text-dark">Pending approval</span>
									@endif
								</td>
								<td>{{ $thread->created_at ? \Carbon\Carbon::parse($thread->created_at)->format('M j, Y g:i A') : '—' }}</td>
								<td>
									<a href="{{ forum_thread_url($thread) }}" class="btn btn-sm btn-outline-primary">View</a>
									<a href="{{ route('account.my-discussions.edit', $thread) }}" class="btn btn-sm btn-primary">
										{{ $isRejected ? 'Edit & resubmit' : 'Edit' }}
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="text-center text-muted py-4">
									You have not started any discussions yet.
									<a href="{{ route('forums.create') }}">Create your first post</a>.
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if(method_exists($threads, 'hasPages') && $threads->hasPages())
				<div class="d-flex justify-content-center mt-3">
					{{ $threads->links() }}
				</div>
			@endif
		</div>
	</div>
</div>
@endsection
