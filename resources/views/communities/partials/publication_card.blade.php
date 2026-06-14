@php
    $i = (int) ($i ?? 1);
    $likes = count($row->favourited);
    $author = $row->author;
    $authorPhoto = $author->photo ?? null;
    if ($authorPhoto && ! preg_match('/^https?:\/\//', $authorPhoto)) {
        if (strpos($authorPhoto, '/storage/') === 0) {
            $authorPhoto = url($authorPhoto);
        } elseif (strpos($authorPhoto, 'storage/') === 0) {
            $authorPhoto = url('/' . $authorPhoto);
        }
    }
    $authorName = $author->name ?? ($row->associated_authors ? clean_unicode($row->associated_authors) : 'Unknown author');
    $authorSubtitle = trim((string) ($row->author_affiliation ?? ''));
    $commentCount = count($row->comments);
    $viewCount = (int) ($row->visits ?? 0);

    $raw_cover = $row->getRawOriginal('cover');
    $cover_is_external = $row->cover_is_exteranl ?? false;
    if (! empty($raw_cover)) {
        $image_link = $cover_is_external ? $raw_cover : storage_link('uploads/publications/' . $raw_cover);
    } else {
        $image_link = null;
    }
    $default_image = asset('assets/images/cover.png');
    $final_image = (! empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL)) ? $image_link : $default_image;
@endphp

<div class="card col-lg-12 community-pub-card pub-card-file-type-corner-wrap mb-2" data-aos="{{ $i > 2 ? 'zoom-in' : '' }}" data-aos-delay="100">
    <div class="card-body text-left p-0">
        @include('partials.publications.file_type_corner_badge', ['row' => $row])

        <div class="community-pub-intro">
            <div class="community-pub-intro__media">
                <a href="{{ publication_url($row) }}" class="community-pub-intro__image-link" aria-label="{{ clean_unicode($row->title) }}">
                    <img src="{{ $final_image }}"
                         alt="{{ clean_unicode($row->title) }}"
                         class="community-pub-intro__image"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='{{ $default_image }}';">
                </a>
            </div>

            <div class="community-pub-intro__text">
                <h5 class="community-pub-title">
                    <a href="{{ publication_url($row) }}">{!! truncate(clean_unicode($row->title), 500) !!}</a>
                </h5>
                <p class="community-pub-description">
                    <a href="{{ publication_url($row) }}">
                        {!! Str::words(strip_tags(clean_unicode(publication_description_for_list($row->description ?? ''))), 40, '...') !!}
                    </a>
                </p>
            </div>
        </div>

        <div class="community-pub-body">
            <div class="community-pub-meta-list">
                @if(! empty($row->theme->description ?? ''))
                    <div class="community-pub-meta-row">
                        <i class="lni lni-briefcase" aria-hidden="true"></i>
                        <span><strong>Theme:</strong> {!! clean_unicode($row->theme->description) !!}</span>
                    </div>
                @endif
                @if(! empty($row->sub_theme->description ?? ''))
                    <div class="community-pub-meta-row">
                        <i class="lni lni-archive" aria-hidden="true"></i>
                        <span><strong>Sub Theme:</strong> {!! clean_unicode($row->sub_theme->description) !!}</span>
                    </div>
                @endif
                @if(! empty($row->associated_authors))
                    <div class="community-pub-meta-row">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span><strong>Associated Authors:</strong> <span class="notranslate" translate="no">{{ clean_unicode($row->associated_authors) }}</span></span>
                    </div>
                @endif
                @if(! empty($row->data_category->category_name ?? ''))
                    <div class="community-pub-meta-row">
                        <i class="lni lni-empty-file" aria-hidden="true"></i>
                        <span><strong>Category:</strong> {{ $row->data_category->category_name }}</span>
                    </div>
                @endif
            </div>

            <div class="community-pub-stats">
                <span class="community-pub-stat community-pub-stat--views">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    {{ format_view_count($viewCount) }} {{ $viewCount === 1 ? 'view' : 'views' }}
                </span>
                <a href="{{ publication_url($row) }}" class="community-pub-stat community-pub-stat--comments comments{{ $i }}" data-bs-toggle="popover" data-bs-placement="bottom">
                    <i class="fa fa-comments" aria-hidden="true"></i>
                    {{ $commentCount }} {{ $commentCount === 1 ? 'Comment' : 'Comments' }}
                </a>
                @if ($likes > 0)
                    <span class="community-pub-stat community-pub-stat--likes">
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        {{ $likes }} {{ $likes === 1 ? 'Like' : 'Likes' }}
                    </span>
                @endif
                @include('home.partials.comments')
            </div>

            <div class="community-pub-footer">
                <div class="community-pub-footer__avatar">
                    @if($author && $authorPhoto)
                        <img src="{{ $authorPhoto }}" alt="{{ $authorName }}"
                             onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <i class="fa fa-user" style="display:none;" aria-hidden="true"></i>
                    @else
                        <i class="fa fa-user" aria-hidden="true"></i>
                    @endif
                </div>

                <div class="community-pub-footer__body">
                    <div class="community-pub-footer__meta">
                        <div class="community-pub-footer__author-text">
                            <span class="community-pub-footer__name notranslate" translate="no">{{ $authorName }}</span>
                            @if($authorSubtitle !== '')
                                <span class="community-pub-footer__subtitle notranslate" translate="no">{{ clean_unicode($authorSubtitle) }}</span>
                            @endif
                        </div>
                        <span class="community-pub-stat community-pub-stat--time">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            {{ publication_content_updated_ago($row) }}
                        </span>
                    </div>

                    <div class="community-pub-footer__actions publication-card-actions" onclick="event.stopPropagation();">
                        @auth
                            <button type="button"
                                class="btn btn-sm btn-outline-danger js-favourite-pub-btn"
                                data-publication-id="{{ $row->id }}"
                                data-favourited="{{ $row->is_favourite ? '1' : '0' }}"
                                onclick="if(window.handlePubFavourite){event.preventDefault();event.stopPropagation();window.handlePubFavourite(this);}">
                                <i class="fa fa-heart{{ $row->is_favourite ? '' : '-o' }} mr-1"></i>
                                <span class="js-fav-label">{{ $row->is_favourite ? 'Favorite' : 'Add favorite' }}</span>
                            </button>
                        @else
                            <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                               class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-heart-o mr-1"></i> Add favorite
                            </a>
                        @endauth
                        <a href="{{ publication_url($row) }}" class="btn btn-sm btn-primary community-pub-action-btn--primary">
                            <i class="fa fa-eye mr-1"></i> Read more
                        </a>
                        @include('common.khub_ai_publication_button', ['publication' => $row])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
