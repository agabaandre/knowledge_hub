@extends(admin_layout())

@section('content')
    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ $dashboard->title }}</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $dashboard->title }}</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    @php
        // Get image with proper fallback logic like frontend
        $raw_cover = $dashboard->getRawOriginal('cover');
        $cover_is_external = $dashboard->cover_is_exteranl ?? false;
        
        // Determine image link
        if (!empty($raw_cover)) {
            if ($cover_is_external) {
                // External URL - use as is
                $image_link = $raw_cover;
            } else {
                // Local file - build storage path
                $image_link = storage_link('uploads/publications/' . $raw_cover);
            }
        } else {
            // No cover - use default
            $image_link = null;
        }
        
        // Default image
        $default_image = asset('assets/images/cover.png');
        
        // Final image to use
        $final_image = (!empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL)) 
            ? $image_link 
            : $default_image;
        
        // Get publication URL
        $publication_url = $dashboard->publication ?? '#';
        
        // Check if URL is external (doesn't match APP_URL)
        $app_url = config('app.url');
        $publication_url_parsed = parse_url($publication_url);
        $app_url_parsed = parse_url($app_url);
        $is_external = isset($publication_url_parsed['host']) && 
                       isset($app_url_parsed['host']) && 
                       $publication_url_parsed['host'] !== $app_url_parsed['host'];
        
        // Only embed if is_embedded == 1
        $should_embed = ($dashboard->is_embedded == 1) && !$is_external;
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Cover Image -->
                        <div class="mb-4 text-center">
                            <img src="{{ $final_image }}" 
                                 alt="{{ $dashboard->title }}" 
                                 class="img-fluid rounded shadow" 
                                 style="max-height: 300px; width: auto;"
                                 onerror="this.onerror=null; this.src='{{ $default_image }}';">
                        </div>
                        
                        <h5 class="card-title">{{ $dashboard->title }}</h5>
                        
                        <!-- Display the publication description -->
                        @if(!empty($dashboard->description))
                            <div class="mb-4">
                                <p>{!! $dashboard->description !!}</p>
                            </div>
                        @endif
                        
                        <!-- Embed or Link based on is_embedded flag -->
                        @if($should_embed)
                            <!-- Embed the publication link in an iframe -->
                            <div class="dashboard-embed">
                                <iframe src="{{ $publication_url }}"
                                        style="width: 100%; height: 800px; border: 1px solid #e2e8f0; border-radius: 8px;"
                                        frameborder="0"
                                        allowfullscreen></iframe>
                            </div>
                        @else
                            <!-- Link to publication (open in new tab if external) -->
                            <div class="text-center">
                                <a href="{{ $publication_url }}" 
                                   target="{{ $is_external ? '_blank' : '_self' }}"
                                   class="btn btn-primary btn-lg">
                                    <i class="fa fa-external-link-alt mr-2"></i>
                                    {{ $is_external ? 'Open Dashboard (External Link)' : 'Open Dashboard' }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
