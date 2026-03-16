@extends(admin_layout())

@section('styles')
 @include('common.table')
 <style>
    .af-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px}
    .af-card-header{padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
    .af-card-body{padding:16px}
 </style>
@endsection

@section('content')
<div class="row">
	<div class="card col-lg-12 af-card">
		<div class="af-card-header d-flex align-items-center justify-content-between">
			<strong>{{ $title ?? 'Licenses' }}</strong>
			<a href="{{ route('admin.licenses.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add License</a>
		</div>
		<div class="af-card-body">
			@include('layouts.partials.alerts')
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ session('error') }}
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			@endif
		</div>
		<div class="af-card-body text-left">
			<!-- Datatable -->
			<table id="licensesTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th style="width:60px;">#</th>
						<th>License Name</th>
						<th>Short Name</th>
						<th>Description</th>
						<th>URL</th>
						<th>Status</th>
						<th>Sort Order</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($licenses as $index => $license)
						<tr>
							<td>{{ $index + 1 }}</td>
							<td>{{ $license->name }}</td>
							<td>{{ $license->short_name ?? '—' }}</td>
							<td style="max-width:300px;">{{ Str::limit($license->description ?? '', 100) ?: '—' }}</td>
							<td>
								@if($license->url)
									<a href="{{ $license->url }}" target="_blank" rel="noopener noreferrer">
										<i class="fa fa-external-link"></i> View
									</a>
								@else
									—
								@endif
							</td>
							<td>
								@if($license->is_active)
									<span class="badge badge-success">Active</span>
								@else
									<span class="badge badge-secondary">Inactive</span>
								@endif
							</td>
							<td>{{ $license->sort_order }}</td>
							<td>
								<a href="{{ route('admin.licenses.edit', $license->id) }}" class="btn btn-sm btn-outline-primary ml-1">
									<i class="fa fa-edit"></i> Edit
								</a>
								<form action="{{ route('admin.licenses.destroy', $license->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this license?');">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-sm btn-danger ml-1">
										<i class="fa fa-trash"></i> Delete
									</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection

