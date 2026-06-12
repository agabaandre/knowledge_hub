@php
    $listOffset = (int) ($listOffset ?? 0);
    $i = $listOffset;
@endphp

@foreach ($publications as $row)
    @php
        $i++;
        $likes = count($row->favourited);
    @endphp

    <div class="card col-lg-12 single-border mb-2 publication-list-card pub-card-file-type-corner-wrap" data-aos="{{ $i > ($listOffset + 2) ? 'zoom-in' : '' }}" data-aos-delay="100">
        <div class="card-body text-left">
            @include('partials.publications.file_type_corner_badge', ['row' => $row])
            <h5 class="text-bold text-lg publication-title-mobile" style="display: none;">
                <a href="{{ publication_url($row)}}">
                    {!! truncate(clean_unicode($row->title), 500) !!}
                </a>
            </h5>

            <div class="row publication-card-row" style="display: flex; flex-wrap: nowrap; align-items: stretch;">
                @php
                    $raw_cover = $row->getRawOriginal('cover');
                    $cover_is_external = $row->cover_is_exteranl ?? false;

                    if (!empty($raw_cover)) {
                        if ($cover_is_external) {
                            $image_link = $raw_cover;
                        } else {
                            $image_link = storage_link('uploads/publications/' . $raw_cover);
                        }
                    } else {
                        $image_link = null;
                    }

                    $default_image = asset('assets/images/cover.png');
                    $final_image = (!empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL))
                        ? $image_link
                        : $default_image;
                @endphp
                <div class="col-md-3 publication-image-col" style="min-height: 150px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: transparent; width: 35%; flex: 0 0 35%; max-width: 35%; padding-right: 0; position: relative; border: none;">
                    <a href="{{ publication_url($row)}}" class="publication-image-link" style="display: block; width: 100%; height: 100%; cursor: pointer;">
                        <img src="{{ $final_image }}"
                             alt="{{ clean_unicode($row->title) }}"
                             class="publication-image"
                             style="width: 100%; height: 100%; min-height: 150px; object-fit: contain; transition: transform 0.3s ease; background-color: transparent;"
                             onerror="this.onerror=null; this.src='{{ $default_image }}';"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'">
                    </a>
                </div>
                <div class="col-md-9 publication-content-col" style="width: 65%; flex: 1 1 65%; max-width: 65%; padding-left: 1rem;">
                    <h5 class="text-bold text-lg publication-title-desktop">
                        <a href="{{ publication_url($row)}}">
                            {!! truncate(clean_unicode($row->title), 500) !!}
                        </a>
                    </h5>

                    <a href="{{ publication_url($row)}}" style="text-align: justify; overflow-wrap: break-word; white-space: normal !important; justify-content: center; margin-bottom: 10px;">
                        {!! Str::words(strip_tags(clean_unicode(publication_description_for_list($row->description ?? ''))), 40, '...') !!}
                    </a>

                    <span class="muted medium ml-2 theme-cl"><br>
                        <i class="lni lni-briefcase mr-1"></i>Theme: {!! clean_unicode($row->theme->description ?? '') !!}</span>
                    <span class="muted medium ml-1 theme-cl"><br>
                        <i class="lni lni-archive mr-1"></i>Sub Theme: {!! clean_unicode($row->sub_theme->description ?? '') !!}</span>
                    @if(!empty($row->associated_authors))
                    <span class="muted medium ml-1 theme-cl"><br>
                        <i class="fa fa-users mr-1"></i>Associated Authors: <span class="notranslate" translate="no">{{ clean_unicode($row->associated_authors) }}</span></span>
                    @endif
                    @if ($likes > 0)
                        <span><i class="lni lni-heart mr-1"></i> {{ $likes }} Like{{ $likes > 1 ? 's' : '' }}
                        </span>
                    @endif
                    <span class="muted medium ml-1 text-muted mt-1 "><br>
                        <i class="lni lni-empty-file mr-1"></i>Category:
                        {{ @$row->data_category->category_name }}</span>

                    <span class="text-muted medium d-block mt-1">
                        @include('partials.publications.card_timestamps', ['row' => $row])
                        <a href="{{ publication_url($row)}}">
                            <span class=" mr-2"><i class="fa fa-eye mr-1"></i>{{ $row->visits ?? 0 }} Visits</span>
                            <span class=" mr-1 ml-2 comments{{ $i }}" data-bs-toggle="popover"
                                data-bs-placement="bottom"><i class="fa fa-comments"></i>
                                {{ count($row->comments) }} Comments</span>
                        </a>
                        @include ('home.partials.comments')
                    </span>

                    <div class="d-flex align-items-center mt-2 publication-card-actions" style="flex-wrap: wrap; gap: 4px;" onclick="event.stopPropagation();">
                        @auth
                            <button type="button"
                                class="btn btn-sm btn-outline-danger js-favourite-pub-btn"
                                data-publication-id="{{ $row->id }}"
                                data-favourited="{{ $row->is_favourite ? '1' : '0' }}"
                                style="border-color: #ef4444; color: #ef4444; background-color: transparent; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; cursor: pointer;"
                                onclick="if(window.handlePubFavourite){event.preventDefault();event.stopPropagation();window.handlePubFavourite(this);}">
                                <i class="fa fa-heart{{ $row->is_favourite ? '' : '-o' }} mr-1" style="{{ $row->is_favourite ? 'color: #ef4444;' : 'color: inherit;' }}"></i>
                                <span class="js-fav-label">{{ $row->is_favourite ? 'Favorite' : 'Add favorite' }}</span>
                            </button>
                        @else
                            <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                               class="btn btn-sm btn-outline-danger"
                               style="border-color: #ef4444; color: #ef4444; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; background-color: transparent;">
                                <i class="fa fa-heart-o mr-1"></i> Add favorite
                            </a>
                        @endauth
                        <a href="{{ publication_url($row)}}"
                           class="btn btn-sm btn-primary"
                           style="background-color: var(--theme-color-primary, #119A48); border-color: var(--theme-color-primary, #119A48); color: white; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                            <i class="fa fa-eye mr-1"></i> Read more
                        </a>
                        @include('common.khub_ai_publication_button', ['publication' => $row])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
