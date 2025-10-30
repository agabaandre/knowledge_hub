   <!-- ======================= Featured / Recommended ======================== -->
  <section class="middle gray" style="padding-top: 20px; padding-bottom: 20px;">
       <div class="container">

           <div class="row justify-content-center" data-aos="fade-in">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                  <div class="sec_title position-relative text-center mb-3" style="margin-bottom: 1rem !important;">
                       <h2 class="ft-bold">Recommended</span></h2>
                   </div>
               </div>
           </div>

           <!-- row -->
          <div class="row align-items-center" id="featured" style="margin-top: 0;">

               @php
                   $i = 0;
               @endphp

               @foreach ($featured as $row)
                   @php
                       $i++;
                       $likes = count($row->favourited);
                   @endphp

                   <!-- Single -->

                  <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12" data-aos="zoom-in">
                      <div class="jbr-wrap text-left border rounded">
                          <div class="cats-box mlb-res rounded bg-white d-flex align-items-center px-3 py-3">
                               <div class="cats-box rounded bg-white d-flex align-items-center" style="min-width:100%;">
                                   @php
                                       $image_link = $row->cover ?? $row->image_url ?? null;
                                       // Default image is cover.png from public assets/images
                                       $default_image = asset('assets/images/cover.png');
                                       
                                       // Check if image_link is valid URL or path
                                       if (empty($image_link) || $image_link === null) {
                                           $image_link = $default_image;
                                       } elseif (!filter_var($image_link, FILTER_VALIDATE_URL)) {
                                           // If it's a relative path, try to make it full URL
                                           if (strpos($image_link, 'storage/') !== false || strpos($image_link, 'uploads/') !== false) {
                                               $image_link = asset($image_link);
                                           } elseif (strpos($image_link, '/') === 0) {
                                               $image_link = url($image_link);
                                           } else {
                                               $image_link = $default_image;
                                           }
                                       }
                                   @endphp

                                   <!-- Image Section -->
                                   <div class="cats-box-image" style="width: 150px; height: 150px; flex-shrink: 0; margin-right: 1rem; border: 1px solid #e2e8f0; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                       <img src="{{ $image_link }}"
                                            alt="{{ $row->title }}" 
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            onerror="this.onerror=null; this.src='{{ $default_image }}';">
                                   </div>

                                  <div class="cats-box-caption" style="flex: 1;">
                                       <h4 class="fs-md mb-0 ft-medium text-truncate"><a
                                               href="{{ url('records/resource') }}?id={{ $row->id }}"
                                               title="{!! $row->title !!}">{!! truncate($row->title, 40) !!}</a></h4>
                                       <div class="d-block mb-2 position-relative">
                                           <span class="text-muted medium text-truncate">
                                               Source: <i
                                                   class="fa fa-bank mr-1"></i>{{ truncate($row->author->name ?? '', 40) }}</span>

                                           <span class="muted medium ml-2 theme-cl"><br>
                                               <i class="lni lni-briefcase mr-1"></i>Theme:
                                               {!! truncate($row->theme->description ?? '', 40) !!}</span>
                                           <span class="muted medium ml-2 theme-cl"><br>
                                               <i class="lni lni-archive mr-1"></i>Sub Theme:
                                               {!! $row->sub_theme->description ?? '' !!}</span>

                                           <span class="muted medium ml-2 text-muted mt-1 "><br>
                                               <i class="lni lni-empty-file mr-1"></i>Category:
                                               {{ @$row->data_category->category_name }}</span>
                                           @if ($likes > 0)
                                               <br><span><i class="lni lni-heart theme-text mr-1"></i>
                                                   {{ $likes }} Like{{ $likes > 0 ? 's' : '' }} </span>
                                           @endif

                                           <span class="text-muted medium d-block mt-1">
                                               <span class=" mr-2"><i class="lni lni-calendar mr-1"></i>Last updated:
                                                   {{ time_ago($row->updated_at) }} </span>
                                               <span class=" mr-2"><i class="fa fa-eye mr-1"></i>{{ $row->visits }}
                                                   Views </span>

                                               <span><span class="mr-1 ml-2 text-muted d-inline pop{{ $row->id }}"
                                                       data-bs-toggle="popover" data-bs-trigger="hover"
                                                       data-bs-placement="bottom" aria-expanded="false"
                                                       aria-controls="comments{{ $i }}"
                                                       onclick="showComments('{{ $row->id }}')"><i
                                                           class="fa fa-comments"></i> {{ count($row->comments) }}
                                                       Comments</span></span>
                                            <div class="d-flex align-items-center mt-2" style="gap: .5rem;">
                                                @auth()
                                                    <div class="btn btn-outline-dark btn-sm favbtn">
                                                        @include ('common.favourites_btn')
                                                    </div>
                                                @else
                                                    <div class="btn btn-outline-dark btn-sm favbtn">
                                                        @include ('common.favourites_btn')
                                                    </div>
                                                @endauth
                                                <a href="{{ url('records/resource') }}?id={{ $row->id }}"
                                                   class="btn btn-sm theme-bg text-white ft-medium apply-btn fs-sm rounded">Browse
                                                   Resource</a>
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

           </div>
           <!-- row -->

           <div class="row justify-content-center">
               <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                   <div class="position-relative text-center">
                       <a id="explore" href="{{ url('records') }}"
                           class="btn btn-md theme-bg rounded text-light hover-theme">Explore More Resources<i
                               class="lni lni-arrow-right-circle ml-2"></i></a>
                   </div>
               </div>
           </div>

       </div>
   </section>
   <!-- ======================= Featured / Recommended ======================== -->
