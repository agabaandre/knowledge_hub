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
             <div class="row"></a>
                 <div class="col-md-2"
                     style=" background-image:url({{ $row->image_url }}); background-size:cover; background-position:center;">

                 </div>
                 <div class="col-md-10">
                     <h5>
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! truncate($row->title, 500) !!}</a>
                     </h5>
                     <p class="text-nothern p-0">
                         <a href="{{ url('records/resource') }}?id={{ $row->id }}">
                             {!! truncate($row->description, 300) !!}
                         </a>
                     </p>
                     <a href="{{ $row->publication }}" class="text-blue"
                         target="_blank"><small>{{ truncate($row->publication, 100) }}</small></a>

                     <span class="muted medium ml-2 theme-cl"><br>
                         <i class="lni lni-briefcase mr-1"></i>Theme: {!! $row->theme->description !!}</span>
                     <span class="muted medium ml-1 theme-cl"><br>
                         <i class="lni lni-archive mr-1"></i>Sub Theme: {!! $row->sub_theme->description !!}</span>
                     @if ($likes > 0)
                         <span><i class="lni lni-heart mr-1"></i> {{ $likes }} Like{{ $likes > 1 ? 's' : '' }}
                         </span>
                     @endif
                     <span class="muted medium ml-1 text-muted mt-1 "><br>
                         <i class="lni lni-empty-file mr-1"></i>Category:
                         {{ @$row->category->category_name }}</span>

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
