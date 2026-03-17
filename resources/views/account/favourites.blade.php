@extends('layouts.plain')

@section('styles')
<style>
    /* Sidebar Styles */
    .favorites-sidebar .card {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .favorites-sidebar .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid #e9ecef;
        padding: 1rem 1.25rem;
    }
    
    .favorites-sidebar .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .favorites-sidebar .list-group-item:hover {
        background-color: #f8f9fa;
        border-left-color: var(--theme-color-primary, #119A48);
        transform: translateX(5px);
    }
    
    .favorites-sidebar .list-group-item img {
        transition: transform 0.3s ease;
    }
    
    .favorites-sidebar .list-group-item:hover img {
        transform: scale(1.1);
    }
    
    .favorites-sidebar h6 {
        color: #2d3748;
        transition: color 0.3s ease;
    }
    
    .favorites-sidebar .list-group-item:hover h6 {
        color: var(--theme-color-primary, #119A48);
    }
    
    .favorites-sidebar .card-footer {
        padding: 0.75rem;
    }
    
    .favorites-sidebar .btn-outline-primary {
        border-radius: 20px;
        padding: 0.375rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .favorites-sidebar .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 991.98px) {
        .favorites-sidebar {
            margin-top: 2rem;
        }
        
        .favorites-sidebar .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
    }

    /* Dark mode: favourites sidebar – Recommended by Preferences & Related by Favorite Tags */
    html[data-bs-theme="dark"] .favorites-sidebar .card {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .card-header {
        background: #2d3136 !important;
        border-bottom-color: #3e4348 !important;
        color: #e4e6eb !important;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .card-header h5,
    html[data-bs-theme="dark"] .favorites-sidebar .card-header .mb-0 { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .favorites-sidebar .card-body {
        background: #242628 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .card-footer {
        background: #2d3136 !important;
        border-top-color: #3e4348 !important;
        color: #e4e6eb;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item {
        background: #242628 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb !important;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item:hover {
        background: #2d3136 !important;
        border-left-color: var(--theme-color-primary, #119A48) !important;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item h6 { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item:hover h6 { color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item .text-muted,
    html[data-bs-theme="dark"] .favorites-sidebar .list-group-item small { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .favorites-sidebar .btn-outline-primary {
        border-color: #3e4348 !important;
        color: #e4e6eb !important;
    }
    html[data-bs-theme="dark"] .favorites-sidebar .btn-outline-primary:hover {
        background: rgba(17, 154, 72, 0.2) !important;
        border-color: var(--theme-color-primary, #119A48) !important;
        color: var(--theme-color-primary, #119A48) !important;
    }
</style>
@endsection

@section('content')
<div class="gray">
    <!-- ======================= Publication Info ======================== -->
	<div class="bg-light  rounded py-5" style="background-image: url({{ asset('assets/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							<div class="jbd-01-thumb">
								
							</div>
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">
									<h3 class="mb-0 ft-medium fs-lg">
                                       My Favourites
									</h3>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= Publication Info ======================== -->
<div class="container">
	<div class="row">

     <div class="col-lg-8 pt-2 pb-5">
     	
     	
	     	@include('publications.partials.publications',['publications'=>$favourites])
       
	</div>

	<!-- Sidebar -->
	<div class="col-lg-4 pt-2 pb-5 favorites-sidebar">
		<div class="sticky-top" style="top: 20px;">
			
			<!-- Recommended by Preferences -->
			@if($recommendedByPreferences && $recommendedByPreferences->count() > 0)
			<div class="card mb-4 border-0 shadow-sm">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0 ft-medium">
						<i class="fa fa-star text-warning mr-2"></i>
						Recommended by Preferences
					</h5>
				</div>
				<div class="card-body p-0">
					<div class="list-group list-group-flush">
						@foreach($recommendedByPreferences->take(10) as $pub)
						<a href="{{ url('records/resource') }}?id={{ $pub->id }}" class="list-group-item list-group-item-action border-0 px-3 py-2" style="transition: all 0.3s ease;">
							<div class="d-flex align-items-start">
								@php
									$raw_cover = $pub->getRawOriginal('cover');
									$cover_is_external = $pub->cover_is_exteranl ?? false;
									$image_link = null;
									if (!empty($raw_cover)) {
										if ($cover_is_external) {
											$image_link = $raw_cover;
										} else {
											$image_link = storage_link('uploads/publications/' . $raw_cover);
										}
									}
									$default_image = asset('assets/images/cover.png');
									$final_image = (!empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL)) ? $image_link : $default_image;
								@endphp
								<img src="{{ $final_image }}" alt="{{ clean_unicode($pub->title) }}" 
									class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-right: 12px;" 
									onerror="this.onerror=null; this.src='{{ $default_image }}';">
								<div class="flex-grow-1">
									<h6 class="mb-1 ft-medium" style="font-size: 0.875rem; line-height: 1.3;">
										{!! truncate(clean_unicode($pub->title), 60) !!}
									</h6>
									<small class="text-muted">
										<i class="fa fa-eye mr-1"></i>{{ $pub->visits }} views
									</small>
								</div>
							</div>
						</a>
						@endforeach
					</div>
					@if($recommendedByPreferences->count() >= 10)
					<div class="card-footer bg-white border-top text-center">
						<a href="{{ url('records/search') }}?preferences=1" class="btn btn-sm btn-outline-primary">
							View More <i class="fa fa-arrow-right ml-1"></i>
						</a>
					</div>
					@endif
				</div>
			</div>
			@endif

			<!-- Related by Favorite Tags -->
			@if($relatedByFavoriteTags && $relatedByFavoriteTags->count() > 0)
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-bottom">
					<h5 class="mb-0 ft-medium">
						<i class="fa fa-tags text-primary mr-2"></i>
						Related by Favorite Tags
					</h5>
				</div>
				<div class="card-body p-0">
					<div class="list-group list-group-flush">
						@foreach($relatedByFavoriteTags->take(10) as $pub)
						<a href="{{ url('records/resource') }}?id={{ $pub->id }}" class="list-group-item list-group-item-action border-0 px-3 py-2" style="transition: all 0.3s ease;">
							<div class="d-flex align-items-start">
								@php
									$raw_cover = $pub->getRawOriginal('cover');
									$cover_is_external = $pub->cover_is_exteranl ?? false;
									$image_link = null;
									if (!empty($raw_cover)) {
										if ($cover_is_external) {
											$image_link = $raw_cover;
										} else {
											$image_link = storage_link('uploads/publications/' . $raw_cover);
										}
									}
									$default_image = asset('assets/images/cover.png');
									$final_image = (!empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL)) ? $image_link : $default_image;
								@endphp
								<img src="{{ $final_image }}" alt="{{ clean_unicode($pub->title) }}" 
									class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-right: 12px;" 
									onerror="this.onerror=null; this.src='{{ $default_image }}';">
								<div class="flex-grow-1">
									<h6 class="mb-1 ft-medium" style="font-size: 0.875rem; line-height: 1.3;">
										{!! truncate(clean_unicode($pub->title), 60) !!}
									</h6>
									<small class="text-muted">
										<i class="fa fa-eye mr-1"></i>{{ $pub->visits }} views
									</small>
								</div>
							</div>
						</a>
						@endforeach
					</div>
					@if($relatedByFavoriteTags->count() >= 10)
					<div class="card-footer bg-white border-top text-center">
						<a href="{{ url('records/search') }}?tags=favorites" class="btn btn-sm btn-outline-primary">
							View More <i class="fa fa-arrow-right ml-1"></i>
						</a>
					</div>
					@endif
				</div>
			</div>
			@endif

		</div>
	</div>
	<!-- End Sidebar -->

</div>
</div>
</div>
@endsection