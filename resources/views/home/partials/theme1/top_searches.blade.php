@php
    $primary = settings()->primary_color ?? '#119A48';
@endphp
<section class="py-5" style="background-color: #f8fafc;">
    <style>
        .theme1-resource-card.forum-post-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: box-shadow 0.2s ease;
            position: relative;
        }
        .theme1-resource-card.forum-post-card:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        .theme1-resource-card .forum-header {
            margin-bottom: 0.75rem;
        }
        .theme1-resource-card .forum-author-name-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        .theme1-resource-card .forum-author-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9375rem;
        }
        .theme1-resource-card .forum-post-time {
            color: #64748b;
            font-size: 0.875rem;
        }
        .theme1-resource-card .forum-content {
            color: #4a5568;
            line-height: 1.6;
            overflow: hidden;
        }
        .theme1-resource-card .forum-thread-image {
            float: left;
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .theme1-resource-card .forum-thread-image:hover {
            transform: scale(1.03);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .theme1-resource-card .forum-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        .theme1-resource-card .forum-title a {
            color: inherit;
            text-decoration: none;
        }
        .theme1-resource-card .forum-title a:hover {
            color: {{ $primary }};
        }
        .theme1-resource-card .forum-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .theme1-resource-card .forum-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: {{ $primary }};
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .theme1-resource-card .forum-action-btn:hover {
            color: #0d5034;
            text-decoration: underline;
        }
        @media (max-width: 575.98px) {
            .theme1-resource-card .forum-thread-image {
                width: 100px;
                height: 100px;
                margin-right: 0.75rem;
            }
        }
    </style>
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <div class="sec_title position-relative text-center">
                    <h2 class="ft-bold mb-0" style="color: #1e293b;">{{ settings()->section_title_top_searches ?? 'Top Searches' }}</h2>
                </div>
            </div>
        </div>
        <div class="row g-0" id="top_searches">
            @foreach ($recent as $row)
                @php
                    $default_image = asset('assets/images/cover.png');
                    $image_link = resolve_publication_card_cover($row);
                @endphp
                <div class="col-12 col-md-6 mb-3 px-2 px-md-3 d-flex">
                    <div class="theme1-resource-card forum-post-card w-100 pub-card-file-type-corner-wrap">
                        @include('partials.publications.file_type_corner_badge', ['row' => $row])
                        <div class="forum-header">
                            <div class="forum-author-name-container">
                                <span class="forum-author-name">{{ $row->author->name ?? '—' }}</span>
                                <span class="forum-post-time"><i class="fa fa-clock me-1"></i>{{ time_ago($row->updated_at) }}</span>
                            </div>
                        </div>
                        <div class="forum-content">
                            <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                                <img src="{{ $image_link }}" alt="" class="forum-thread-image" loading="lazy" onerror="this.src='{{ $default_image }}';">
                            </a>
                            <h3 class="forum-title">
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}">{{ $row->title }}</a>
                            </h3>
                            <p class="mb-0" style="text-align: justify;">{{ Str::words(strip_tags($row->description ?? 'No description.'), 40) }}</p>
                            <div class="forum-actions">
                                @auth
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger js-favourite-pub-btn me-2"
                                    data-publication-id="{{ $row->id }}"
                                    data-favourited="{{ ($row->is_favourite ?? false) ? '1' : '0' }}"
                                    style="border-color: #ef4444; color: #ef4444; background: transparent; padding: 0.25rem 0.5rem; font-size: 0.875rem; cursor: pointer;"
                                    onclick="if(window.handlePubFavourite){event.preventDefault();event.stopPropagation();window.handlePubFavourite(this);}">
                                    <i class="fa fa-heart{{ ($row->is_favourite ?? false) ? '' : '-o' }} me-1" style="{{ ($row->is_favourite ?? false) ? 'color: #ef4444;' : 'color: inherit;' }}"></i>
                                    <span class="js-fav-label">{{ ($row->is_favourite ?? false) ? 'Favorite' : 'Add favorite' }}</span>
                                </button>
                                @else
                                <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="forum-action-btn me-2" style="color: #ef4444;"><i class="fa fa-heart-o me-1"></i> Add favorite</a>
                                @endauth
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="forum-action-btn">
                                    Read More <i class="fa fa-arrow-right"></i>
                                </a>
                                @if($row->has_any_pdf ?? false)
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="forum-action-btn">
                                    <i class="fa-solid fa-microchip me-1"></i> Chat with PDF
                                </a>
                                @else
                                <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="forum-action-btn">
                                    <i class="fa-solid fa-microchip me-1"></i> Summarise
                                </a>
                                @endif
                                <span class="text-muted small"><i class="fa fa-eye me-1"></i>{{ $row->visits ?? 0 }} Visits</span>
                                @if(method_exists($row, 'comments') && $row->relationLoaded('comments'))
                                <span class="text-muted small"><i class="fa fa-comments me-1"></i>{{ $row->comments->count() }} Comments</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <a href="{{ url('records/search') }}" class="btn btn-outline-primary px-4 py-2 rounded" style="border-color: {{ $primary }}; color: {{ $primary }};">View more</a>
            </div>
        </div>
    </div>
</section>
