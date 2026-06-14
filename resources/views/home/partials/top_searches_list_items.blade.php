@php
    $listOffset = (int) ($listOffset ?? 0);
    $i = $listOffset;
@endphp

@foreach ($recent as $row)
    @php
        $i++;
        $likes = count($row->favourited);
    @endphp

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12" data-aos="zoom-in">
        <div class="jbr-wrap text-left border rounded pub-card-file-type-corner-wrap">
            @include('partials.publications.file_type_corner_badge', ['row' => $row])
            <div class="cats-box mlb-res rounded bg-white d-flex align-items-center px-2 py-2 top-searches-card">
                <div class="cats-box rounded bg-white d-flex align-items-center top-searches-card" style="min-width:100%;">
                    @php
                        $default_image = asset('assets/images/cover.png');
                        $image_link = resolve_publication_card_cover($row);
                    @endphp

                    <div class="cats-box-image top-searches-image" style="width: 180px; height: 180px; flex-shrink: 0; margin-right: 0.5rem; border: none; overflow: hidden; background: transparent; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <img src="{{ $image_link }}"
                             alt="{{ $row->title }} - {{ $row->author->name ?? 'Africa CDC' }}"
                             title="{{ $row->title }}"
                             loading="lazy"
                             style="width: 100%; height: 100%; object-fit: contain; background: transparent;"
                             onerror="this.onerror=null; this.src='{{ $default_image }}';">
                    </div>

                    <div class="cats-box-caption top-searches-content" style="flex: 1; overflow: hidden;">
                        <h4 class="fs-md mb-0 ft-medium" style="text-align: justify; overflow-wrap: break-word;"><a
                                href="{{ publication_url($row)}}"
                                title="{!! $row->title !!}">{!! truncate($row->title, 40) !!}</a></h4>
                        <div class="d-block mb-2 position-relative" style="text-align: justify; overflow-wrap: break-word;">
                            <span class="text-muted medium" style="display: block;">
                                Source: <i
                                    class="fa fa-bank mr-1"></i><span class="notranslate" translate="no">{{ truncate($row->author->name ?? '', 40) }}</span></span>

                            <span class="muted medium ml-2 theme-cl"><br>
                                <i class="lni lni-briefcase mr-1"></i>Theme:
                                {!! truncate($row->theme->description ?? '', 40) !!}</span>
                            <span class="muted medium ml-2 theme-cl"><br>
                                <i class="lni lni-archive mr-1"></i>Sub Theme:
                                {!! $row->sub_theme->description ?? '' !!}</span>

                            <span class="muted medium ml-2 text-muted mt-1 "><br>
                                <i class="lni lni-empty-file mr-1"></i>Category:
                                {{ @$row->data_category->category_name }}</span>
                            @if(!empty($row->associated_authors))
                            <span class="muted medium ml-2 theme-cl"><br>
                                <i class="fa fa-users mr-1"></i>Associated Authors: <span class="notranslate" translate="no">{{ truncate($row->associated_authors, 30) }}</span></span>
                            @endif
                            @if ($likes > 0)
                                <br><span><i class="lni lni-heart theme-text mr-1"></i>
                                    {{ $likes }} Like{{ $likes > 0 ? 's' : '' }} </span>
                            @endif

                            <span class="text-muted medium d-block mt-1">
                                @include('partials.publications.card_timestamps', ['row' => $row])
                                <span class=" mr-2"><i class="fa fa-eye mr-1"></i>{{ format_view_count($row->visits ?? 0) }}
                                    Views </span>

                                <span><span class="mr-1 ml-2 text-muted d-inline pop{{ $row->id }}"
                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                        data-bs-placement="bottom" aria-expanded="false"
                                        aria-controls="comments{{ $i }}"
                                        onclick="showComments('{{ $row->id }}')"><i
                                            class="fa fa-comments"></i> {{ count($row->comments) }}
                                    Comments</span></span>
                                <div class="d-flex align-items-center mt-2" style="flex-wrap: wrap; gap: 4px;">
                                    @auth
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger js-favourite-pub-btn"
                                            data-publication-id="{{ $row->id }}"
                                            data-favourited="{{ ($row->is_favourite ?? false) ? '1' : '0' }}"
                                            style="border-color: #ef4444; color: #ef4444; background-color: transparent; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; cursor: pointer;"
                                            onclick="if(window.handlePubFavourite){event.preventDefault();event.stopPropagation();window.handlePubFavourite(this);}">
                                            <i class="fa fa-heart{{ ($row->is_favourite ?? false) ? '' : '-o' }} mr-1" style="{{ ($row->is_favourite ?? false) ? 'color: #ef4444;' : 'color: inherit;' }}"></i>
                                            <span class="js-fav-label">Favorite</span>
                                        </button>
                                    @else
                                        <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                                           class="btn btn-sm btn-outline-danger"
                                           style="border-color: #ef4444; color: #ef4444; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; background-color: transparent;">
                                            <i class="fa fa-heart-o mr-1"></i> Favorite
                                        </a>
                                    @endauth
                                    <a href="{{ publication_url($row)}}"
                                       class="btn btn-sm btn-primary"
                                       style="background-color: var(--theme-color-primary, #119A48); border-color: var(--theme-color-primary, #119A48); color: white; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                                        <i class="fa fa-eye mr-1"></i> Browse
                                    </a>
                                    @include('common.khub_ai_publication_button', ['publication' => $row])
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @include('home.partials.comments')

        </div>
    </div>
@endforeach
