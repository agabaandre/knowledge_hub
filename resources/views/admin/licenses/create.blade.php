@extends('admin.layouts.main')

@section('content')
<div class="row">
	<div class="card col-lg-12">
		<div class="card-header">
			<h3 class="card-title">Add New License</h3>
		</div>
		<div class="card-body">
			@include('layouts.partials.alerts')
			
			<form action="{{ route('admin.licenses.store') }}" method="POST">
				@csrf
				
				<div class="form-group">
					<label for="name">License Name <span class="text-danger">*</span></label>
					<input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
					@error('name')
						<small class="text-danger">{{ $message }}</small>
					@enderror
				</div>
				
				<div class="form-group">
					<label for="short_name">Short Name</label>
					<input type="text" class="form-control" id="short_name" name="short_name" value="{{ old('short_name') }}" placeholder="e.g., CC BY">
					@error('short_name')
						<small class="text-danger">{{ $message }}</small>
					@enderror
				</div>
				
				<div class="form-group">
					<label for="description">Description</label>
					<textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
					@error('description')
						<small class="text-danger">{{ $message }}</small>
					@enderror
				</div>
				
				<div class="form-group">
					<label for="url">URL</label>
					<input type="url" class="form-control" id="url" name="url" value="{{ old('url') }}" placeholder="https://...">
					@error('url')
						<small class="text-danger">{{ $message }}</small>
					@enderror
				</div>
				
				<div class="form-group">
					<label for="sort_order">Sort Order</label>
					<input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
					<small class="text-muted">Lower numbers appear first in dropdowns</small>
				</div>
				
				<div class="form-group">
					<div class="form-check">
						<input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
						<label class="form-check-label" for="is_active">Active</label>
					</div>
				</div>
				
				<div class="form-group">
					<a href="{{ route('admin.licenses.index') }}" class="btn btn-secondary">Cancel</a>
					<button type="submit" class="btn btn-primary">Save License</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

