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
                    <span class="forum-author-name notranslate" translate="no">{{ $row->author->name ?? '—' }}</span>
                    <span class="forum-post-time">
                        <i class="fa fa-clock me-1"></i>Updated {{ publication_content_updated_ago($row) }}
                        @if(publication_last_visited_at($row))
                            · Last visit {{ time_ago(publication_last_visited_at($row)) }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="forum-content">
                <a href="{{ publication_url($row)}}">
                    <img src="{{ $image_link }}" alt="" class="forum-thread-image" loading="lazy" onerror="this.src='{{ $default_image }}';">
                </a>
                <h3 class="forum-title">
                    <a href="{{ publication_url($row)}}">{{ $row->title }}</a>
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
                        <span class="js-fav-label">Favorite</span>
                    </button>
                    @else
                    <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="forum-action-btn me-2" style="color: #ef4444;"><i class="fa fa-heart-o me-1"></i> Favorite</a>
                    @endauth
                    <a href="{{ publication_url($row)}}" class="forum-action-btn">
                        Browse <i class="fa fa-arrow-right"></i>
                    </a>
                    @include('common.khub_ai_publication_button', [
                        'publication' => $row,
                        'btnClass' => 'forum-action-btn',
                        'btnStyle' => 'cursor: pointer; border: none; background: none; padding: 0;',
                    ])
                    <span class="text-muted small"><i class="fa fa-eye me-1"></i>{{ $row->visits ?? 0 }} Visits</span>
                    @if(method_exists($row, 'comments') && $row->relationLoaded('comments'))
                    <span class="text-muted small"><i class="fa fa-comments me-1"></i>{{ $row->comments->count() }} Comments</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
