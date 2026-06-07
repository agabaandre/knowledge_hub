@extends(admin_layout())

@section('styles')

@endsection

@section('content')

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Courses</h4>
						<button type="button" class="btn btn-primary float-right ml-2" data-toggle="modal" data-target="#create-course-modal">
							<i class="fa fa-plus"></i> Add Course
						</button>
						<button type="button" class="btn btn-success float-right ml-2" data-toggle="modal" data-target="#import-courses-modal">
							<i class="fa fa-file-excel-o"></i> Import Courses
						</button>
						
						
						<form method="GET" class="container-fluid">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="country">Category</label>
										<select class="form-control" id="category" name="category">
											<option value="">All</option>
											@foreach($categories as $category)
												<option value="{{ $category->id }}" {{ ($search->category ?? null && $search->category ?? null == $category->id)?"selected":""}}>{{ $category->name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label for="country">Search</label>
										<input class="form-control" id="term" name="term" value="{{$search->term ?? ''}}">
									</div>
								</div>

								<div class="col-md-3 mt-4 text-right">
									<!-- Export Button -->
								<button type="submit" id="filterButton" class="btn btn-primary">Filter Data</button>
								<button type="button" id="reset" class="btn btn-secondary">Reset</button>
							
								</div>
							</div>

							
							
						</form>

						<hr />
					</div>

					<div class="card-body">
						<div class="container-fluid">
							<div class="row">
								@include('layouts.partials.alerts')

								<div class="col-xl-12 col-lg-12 col-md-12 col-12">
									<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
										<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
											<h6 class="mb-0 ft-medium fs-sm">{{ count($courses) }} {{ (count($courses) > 0 && isset($_GET['slug']) && !empty($_GET['slug'])) ? $courses[0]->type->type_name : 'courses' }} {{ (count($courses) > 1) ? '' : '' }} Available</h6>
										</div>
										
									</div>
								</div>

							</div>

							<div class="row">
								<!-- Single job -->
								@foreach($courses as $course)
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
										<div class="job_grid d-block border rounded px-3 pt-3 pb-2 bg-white mt-2">
											<div class="jb-list01">
												<div class="jb-list-01-title">
													<h6 class="text-dark">{{ $course->fullname }}</h6>
													@if($course->provider)
														<div class="text-muted small mb-1">Provider: {{ $course->provider }}</div>
													@endif
													<p class="text-dark">{!! truncate($course->summary,200) !!}</p>
												</div>
												<div class="jb-list-01-info d-block mb-3">
													<span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>Added: {{ time_ago($course->created_at) }}</span>
													@if($course->isExternalCourse() && $course->external_course_url)
														<a href="{{ $course->external_course_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark float-right ml-2"><i class="fa fa-external-link"></i> View on {{ $course->provider ?: 'eLearning Platform' }}</a>
													@elseif($course->course_url)
														<a href="{{ $course->course_url }}" target="_blank" class="btn btn-dark float-right ml-2"><i class="fa fa-external-link"></i> View Course</a>
													@endif
													<button type="button" class="btn btn-danger float-right ml-2" onclick="openDeleteCourseModal({{ $course->id }}, '{{ addslashes($course->fullname) }}')">
														<i class="fa fa-trash"></i> Delete
													</button>
													<button type="button" class="btn btn-primary float-right edit-course-btn ml-2" data-toggle="modal" data-target="#edit-course-modal" data-course='@json($course)'><i class="fa fa-pencil"></i> Edit</button>
												</div>
											</div>
										</div>
									</div>

								@endforeach
							</div>

							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									{{ $courses->links() }}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('admin.courses.partials.create-modal')
	@include('admin.courses.partials.edit-modal')
	@include('admin.courses.partials.delete-modal')

	<!-- Import Courses Modal -->
	<div class="modal" id="import-courses-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Import Courses from Excel</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="alert alert-info p-2 small mb-3">
					<strong>Excel columns required:</strong><br>
					<code>fullname, shortname, cover_image, category_id, summary, provider, content, course_url, is_moodle, is_active</code>
					<br>All columns are optional except <code>fullname</code>, <code>shortname</code>, and <code>course_url</code>.
				</div>
				<form action="{{ url('admin/courses/import') }}" method="post" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="form-group">
							<label for="import_file">Select Excel File (.xlsx, .xls, .csv)</label>
							<input type="file" class="form-control" id="import_file" name="file" accept=".xlsx,.xls,.csv" required>
						</div>
						<div class="form-group form-check">
							<input type="checkbox" class="form-check-input" id="has_header" name="has_header" value="1" checked>
							<label class="form-check-label" for="has_header">File has header row</label>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-danger" data-dismiss="modal" type="button">Cancel</button>
						<button class="btn btn-success" type="submit">Import</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('scripts')

@include('common.select2')

@endsection