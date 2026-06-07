<style>
    /* Ensure card and all links are clickable (override any parent pointer-events) */
    .publication-list-card,
    .publication-list-card a,
    .publication-list-card .publication-image-link { pointer-events: auto !important; }
    .publication-list-card a { cursor: pointer; }
    /* Publication listing: links look like content, not default blue/underlined */
    .publication-list-card .publication-content-col a:not(.btn) {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-content-col a:not(.btn):hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    .publication-list-card .publication-title-desktop a,
    .publication-list-card .publication-title-mobile a {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-title-desktop a:hover,
    .publication-list-card .publication-title-mobile a:hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    /* Mobile Styles (phones) - Dropcap images */
    @media (max-width: 767.98px) {
        .publication-card-row {
            display: block !important;
            flex-direction: unset !important;
            flex-wrap: unset !important;
        }
        
        .publication-title-mobile {
            display: none !important;
        }
        
        .publication-image-col {
            width: 120px !important;
            height: 120px !important;
            flex: none !important;
            max-width: 120px !important;
            padding-right: 12px !important;
            padding-left: 0 !important;
            padding-bottom: 0 !important;
            margin-right: 12px !important;
            margin-bottom: 8px !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            float: left !important;
            border: none !important;
            overflow: hidden !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            order: unset !important;
            shape-outside: margin-box !important;
        }
        
        .publication-image-link {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
        }
        
        .publication-image {
            min-height: 120px !important;
            height: 120px !important;
            width: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            background: transparent !important;
        }
        
        .publication-content-col {
            width: auto !important;
            flex: none !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            display: block !important;
            overflow: visible !important;
            text-align: justify !important;
            order: unset !important;
        }
        
        .publication-title-desktop {
            display: block !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            hyphens: auto !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            font-size: 1rem !important;
            margin-bottom: 0.5rem !important;
        }
        
        .publication-title-desktop a {
            display: block !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            hyphens: auto !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            text-decoration: none !important;
        }
        
        .publication-content-col h5,
        .publication-content-col p,
        .publication-content-col span {
            text-overflow: unset !important;
            white-space: normal !important;
            overflow: visible !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
        }
        
        /* Extra small devices (phones in portrait, less than 360px) */
        @media (max-width: 359.98px) {
            .publication-title-desktop {
                font-size: 0.9rem !important;
                line-height: 1.3 !important;
            }
            
            .publication-image-col {
                width: 100px !important;
                height: 100px !important;
                max-width: 100px !important;
            }
            
            .publication-image {
                min-height: 100px !important;
                height: 100px !important;
            }
        }
        
        /* Keep buttons side-by-side on mobile */
        .publication-card-row .d-flex.align-items-center {
            flex-wrap: wrap;
            gap: 4px;
            clear: left;
            margin-top: 1rem;
        }
        
        /* Clear float after content */
        .publication-card-row::after {
            content: "";
            display: table;
            clear: both;
        }
    }
    
    /* Tablet and Desktop Styles */
    @media (min-width: 768px) {
        .publication-title-mobile {
            display: none !important;
        }
        
        .publication-title-desktop {
            display: block;
        }
    }
</style>

 @php
     $i = 0;
     $federatedPublications = $federatedPublications ?? collect();
 @endphp

 @if($federatedPublications->count() > 0)
     @if(request()->filled('term'))
         <div class="mb-3">
             <h5 style="color:var(--theme-color-primary,#119A48);font-size:1rem;">
                 <i class="fa fa-globe-africa me-2"></i>From partner country hubs
             </h5>
         </div>
     @endif
     @foreach($federatedPublications as $row)
         @include('partials.federation.publication_card', ['row' => $row])
     @endforeach
     <p class="mb-3">
         <a href="{{ route('federation.browse', ['term' => request('term')]) }}" class="btn btn-sm theme-bg text-white">
             <i class="fa fa-globe-africa me-1"></i>{{ __('frontend_nav.partner_hubs') }}
         </a>
     </p>
 @endif

 @foreach ($publications as $row)
     @php
         $i++;
         $likes = count($row->favourited);
     @endphp

    <div class="card col-lg-12 single-border mb-2 publication-list-card pub-card-file-type-corner-wrap" data-aos="{{ $i > 2 ? 'zoom-in' : '' }}" data-aos-delay="100">
        <div class="card-body text-left">
            @include('partials.publications.file_type_corner_badge', ['row' => $row])
            <!-- Title for Mobile (shown only on mobile, above image) -->
            <h5 class="text-bold text-lg publication-title-mobile" style="display: none;">
                <a href="{{ publication_url($row)}}">
                    {!! truncate(clean_unicode($row->title), 500) !!}
                </a>
            </h5>
            
            <div class="row publication-card-row" style="display: flex; flex-wrap: nowrap; align-items: stretch;">
                 @php
                     // Get raw cover value before accessor processes it
                     $raw_cover = $row->getRawOriginal('cover');
                     $cover_is_external = $row->cover_is_exteranl ?? false;
                     
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
                     <!-- Title for Desktop/Tablet (shown on tablets and desktops, hidden on mobile) -->
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
                         @php
                             $auGold = settings()->au_gold ?? '#B4A269';
                             $goldTextColor = '#5a4d2e';
                         @endphp
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
                         @auth
                             <a href="{{ publication_url($row)}}" class="btn btn-sm btn-primary" style="background-color: var(--theme-color-primary, #119A48); border-color: var(--theme-color-primary, #119A48); color: white; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500;">
                                 <i class="fa-solid fa-microchip"></i> Khub AI
                             </a>
                         @else
                             <a href="{{ url('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" class="btn btn-sm btn-outline-primary" style="border-color: var(--theme-color-primary, #119A48); color: var(--theme-color-primary, #119A48); text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem;">
                                 <i class="fa-solid fa-microchip"></i> Khub AI <small>(login)</small>
                             </a>
                         @endauth
                     </div>

                 </div>
             </div>
         </div>
     </div>
 @endforeach

 <div class="py-4"> {{ $publications->links() }}</div>

 @if (count($publications) == 0 && ($federatedPublications ?? collect())->count() == 0)
     <div class="row justify-content-center py-5">
         <i class="fa fa-info-circle fa-2x text-muted"></i>
         <h4 class="text-muted">No matching records found</h4>
     </div>

    <div class="row justify-content-center">
        <a href="{{ url('publications/request-content') }}" class="btn btn-dark mt-2">Request Content</a>
    </div>
@endif

{{-- Favourite script is in layouts/app.blade.php for both default and theme1 --}}
