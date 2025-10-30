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
            <div class="row">
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
                 <div class="col-md-2" style="min-height: 150px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f1f5f9;">
                     <img src="{{ $final_image }}" 
                          alt="{{ $row->title }}" 
                          style="width: 100%; height: 100%; min-height: 150px; object-fit: cover;"
                          onerror="this.onerror=null; this.src='{{ $default_image }}';">
                 </div>
                 <div class="col-md-10">
                     <h5 class="text-bold text-lg">
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! truncate($row->title, 500) !!}</a>
                     </h5>
                     <p class="text-nothern p-0 pt-2">
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! truncate($row->description, 300) !!}
                         </a>
                     </p>
                     <a href="{{ $row->publication }}" class="text-blue"
                         target="_blank"><small>{{ truncate($row->publication, 100) }}</small></a>

                     <span class="muted medium ml-2 theme-cl"><br>
                         <i class="lni lni-briefcase mr-1"></i>Theme: {!! $row->theme->description ?? '' !!}</span>
                     <span class="muted medium ml-1 theme-cl"><br>
                         <i class="lni lni-archive mr-1"></i>Sub Theme: {!! $row->sub_theme->description ?? '' !!}</span>
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
                         @include ('common.favourites_btn')
                     </span>

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
