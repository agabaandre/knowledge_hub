<style>
    /* Mobile Styles (phones) */
    @media (max-width: 767.98px) {
        .publication-card-row {
            flex-direction: column !important;
            flex-wrap: wrap !important;
        }
        
        .publication-title-mobile {
            display: block;
            order: 1;
            width: 100% !important;
            margin-bottom: 1rem;
        }
        
        .publication-image-col {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
            margin-bottom: 1rem;
            order: 2;
        }
        
        .publication-image-link {
            width: 100%;
        }
        
        .publication-image {
            min-height: 200px !important;
            height: 200px !important;
            object-position: top center !important;
        }
        
        .publication-content-col {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            order: 3;
        }
        
        .publication-title-desktop {
            display: none !important;
        }
        
        /* Keep buttons side-by-side on mobile */
        .publication-card-row .d-flex.align-items-center {
            flex-wrap: wrap;
            gap: 4px;
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
 @endphp

 @foreach ($publications as $row)
     @php
         $i++;
         $likes = count($row->favourited);
     @endphp

    <div class="card col-lg-12 single-border mb-2" data-aos="{{ $i > 2 ? 'zoom-in' : '' }}" data-aos-delay="100">
        <div class="card-body text-left">
            <!-- Title for Mobile (shown only on mobile, above image) -->
            <h5 class="text-bold text-lg publication-title-mobile" style="display: none;">
                <a href="{{ url('records/resource') }}?id={{ $row->id }}">
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
                     <a href="{{ url('records/resource') }}?id={{ $row->id }}" class="publication-image-link" style="display: block; width: 100%; height: 100%; cursor: pointer;">
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
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! truncate(clean_unicode($row->title), 500) !!}</a>
                     </h5>
                     <p class="text-nothern p-0 pt-2">
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! Str::words(strip_tags(clean_unicode($row->description ?? '')), 15, '...') !!}
                         </a>
                     </p>
                     <a href="{{ $row->publication }}" class="text-blue"
                         target="_blank"><small>{{ truncate(clean_unicode($row->publication), 100) }}</small></a>

                     <span class="muted medium ml-2 theme-cl"><br>
                         <i class="lni lni-briefcase mr-1"></i>Theme: {!! clean_unicode($row->theme->description ?? '') !!}</span>
                     <span class="muted medium ml-1 theme-cl"><br>
                         <i class="lni lni-archive mr-1"></i>Sub Theme: {!! clean_unicode($row->sub_theme->description ?? '') !!}</span>
                     @if(!empty($row->associated_authors))
                     <span class="muted medium ml-1 theme-cl"><br>
                         <i class="fa fa-users mr-1"></i>Associated Authors: {{ clean_unicode($row->associated_authors) }}</span>
                     @endif
                     @if ($likes > 0)
                         <span><i class="lni lni-heart mr-1"></i> {{ $likes }} Like{{ $likes > 1 ? 's' : '' }}
                         </span>
                     @endif
                     <span class="muted medium ml-1 text-muted mt-1 "><br>
                         <i class="lni lni-empty-file mr-1"></i>Category:
                         {{ @$row->data_category->category_name }}</span>

                     <span class="text-muted medium d-block mt-1">
                         <span class=" mr-2"><i class="lni lni-calendar mr-1"></i>Last updated:
                             {{ time_ago($row->updated_at) }} </span>
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             <span class=" mr-2"><i class="fa fa-eye mr-1"></i>{{ $row->visits }} Views </span>
                             <span class=" mr-1 ml-2 comments{{ $i }}" data-bs-toggle="popover"
                                 data-bs-placement="bottom"><i class="fa fa-comments"></i>
                                 {{ count($row->comments) }} Comments</span>
                         </a>
                         @include ('home.partials.comments')
                     </span>
                     
                     <div class="d-flex align-items-center mt-2" style="flex-wrap: wrap; gap: 4px;">
                         @php
                             $auGold = settings()->au_gold ?? '#B4A269';
                             $goldTextColor = '#5a4d2e';
                         @endphp
                         @auth
                             @if(!$row->is_favourite)
                                 <a href="{{ url('publications/add_favourite') }}?id={{ $row->id }}" 
                                    class="btn btn-sm btn-outline-warning" 
                                    style="border-color: {{ $auGold }}; color: {{ $goldTextColor }}; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; background-color: transparent;">
                                     <i class="fa fa-star mr-1"></i> Add to Favourites
                                 </a>
                             @else
                                 <a href="{{ url('publications/remove_favourite') }}?id={{ $row->id }}" 
                                    class="btn btn-sm btn-warning" 
                                    style="background-color: {{ $auGold }}; border-color: {{ $auGold }}; color: {{ $goldTextColor }}; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                                     <i class="fa fa-star mr-1"></i> Remove favorite
                                 </a>
                             @endif
                         @else
                             <a href="{{ url('login') }}" 
                                class="btn btn-sm btn-outline-warning" 
                                style="border-color: {{ $auGold }}; color: {{ $goldTextColor }}; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; background-color: transparent;">
                                 <i class="fa fa-star mr-1"></i> Add to Favourites
                             </a>
                         @endauth
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}" 
                            class="btn btn-sm btn-primary" 
                            style="background-color: var(--theme-color-primary, #119A48); border-color: var(--theme-color-primary, #119A48); color: white; text-decoration: none; padding: 0.375rem 0.75rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                             <i class="fa fa-eye mr-1"></i> Browse Resource
                         </a>
                     </div>

                 </div>
             </div>
         </div>
     </div>
 @endforeach

 <div class="py-4"> {{ $publications->links() }}</div>

 @if (count($publications) == 0)
     <div class="row justify-content-center py-5">
         <i class="fa fa-info-circle fa-2x text-muted"></i>
         <h4 class="text-muted">No matching records found</h4>
     </div>

    <div class="row justify-content-center">
        <a href="{{ url('publications/request-content') }}" class="btn btn-dark mt-2">Request Content</a>
    </div>
@endif
