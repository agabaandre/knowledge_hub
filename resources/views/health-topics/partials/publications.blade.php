<style>
    /* Publication card content styling */
    .card-body {
        text-align: justify;
        overflow: hidden; /* Clear floats */
    }
    
    /* Ensure dropcap image floats properly */
    .publication-dropcap {
        float: left;
    }
    
    /* Clear float for metadata sections */
    .publication-meta {
        clear: left;
    }
</style>

 @php
     $i = 0;
     $textColor = settings()->links_active_color ?? settings()->primary_text_color ?? settings()->primary_color ?? '#119A48';
 @endphp

 @foreach ($publications as $row)
     @php
         $i++;
     @endphp

    <div class="card col-lg-12 single-border mb-2" data-aos="{{ $i > 2 ? 'zoom-in' : '' }}" data-aos-delay="100">
        <div class="card-body text-left" style="margin: 0 2px;">
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
            
            <!-- Cover Image as Dropcap -->
            <a href="{{ publication_url($row)}}" style="float: left; display: inline-block; cursor: pointer; margin-right: 12px; margin-bottom: 8px; margin-left: 2px; margin-top: 2px;">
                <img src="{{ $final_image }}" 
                     alt="{{ clean_unicode($row->title) }}" 
                     style="width: 96px; height: 96px; object-fit: contain; transition: transform 0.3s ease; background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; display: block;"
                     onerror="this.onerror=null; this.src='{{ $default_image }}';"
                     onmouseover="this.style.transform='scale(1.05)'"
                     onmouseout="this.style.transform='scale(1)'">
            </a>
            
            <!-- Title -->
            <h5 class="text-bold text-lg" style="margin-bottom: 0.75rem; text-align: justify; overflow-wrap: break-word;">
                <a href="{{ publication_url($row)}}" style="color: {{ $textColor }}; text-decoration: none;">
                    {!! truncate(clean_unicode($row->title), 30) !!}
                </a>
            </h5>
            
            <!-- Description (100 words) -->
            <p class="text-nothern p-0 pt-2" style="color: #4a5568; line-height: 1.6; text-align: justify; overflow-wrap: break-word;">
                {!! Str::words(strip_tags(clean_unicode($row->description ?? '')), 100, '...') !!}
            </p>
            
            <!-- Authors -->
            @if($row->author && $row->author->name)
            <p class="mb-1" style="font-size: 0.875rem; color: #64748b; text-align: justify; clear: left; overflow-wrap: break-word;">
                <i class="fa fa-user me-1" style="color: var(--theme-color-primary, #119A48);"></i>
                <strong>Author:</strong> <span class="notranslate" translate="no">{{ clean_unicode($row->author->name) }}</span>
            </p>
            @endif
            
            <!-- URL -->
            @if($row->publication)
            <p class="mb-1" style="font-size: 0.875rem; text-align: justify; overflow-wrap: break-word;">
                <i class="fa fa-link me-1" style="color: var(--theme-color-primary, #119A48);"></i>
                <strong>URL:</strong> 
                <a href="{{ $row->publication }}" class="text-blue" target="_blank" rel="noopener noreferrer" style="color: {{ $textColor }}; text-decoration: underline;">
                    {{ Str::limit($row->publication, 50) }}
                </a>
            </p>
            @endif
            
            <p class="text-muted medium d-block mt-2" style="font-size: 0.875rem; text-align: justify; clear: left; overflow-wrap: break-word;">
                @include('partials.publications.card_timestamps', ['row' => $row])
            </p>
            
            <div class="d-flex align-items-center mt-3" style="flex-wrap: wrap; gap: 8px; clear: left;">
                <a href="{{ publication_url($row)}}" 
                   class="btn btn-sm btn-primary" 
                   style="background-color: var(--theme-color-primary, #119A48); border-color: var(--theme-color-primary, #119A48); color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease;">
                    <i class="fa fa-eye mr-1"></i> Browse Resource
                </a>
            </div>
         </div>
     </div>
 @endforeach

