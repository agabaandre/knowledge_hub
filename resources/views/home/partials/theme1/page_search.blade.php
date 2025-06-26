 <!-- ======================= Searchbar Banner ======================== -->

 @php
     $show_types = strpos(current_url(), 'record') > -1 || strpos(current_url(), 'publication') > -1 ? true : false;
 @endphp

 <div class="pt-5 pt-0 custom-bg">
     <div class="container">
         <div class="row justify-content-between align-items-center">

             <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


                 <div class="single_widgets widget_search px-0 py-0"
                     style="background-color: transparent!important; border:none!important;">
                     <form
                         action="{{ strpos(current_url(), 'record') > -1 ? url('records') : (strpos(current_url(), 'thread') > -1 ? url('forums') : '') }}"
                         class="sidebar-search-form px-0 py-0 filter">
                         <input class="px-3 py-0 main_search" style="font-size: 12pt;" value="{{ @$search->term }}"
                             type="search" name="term" placeholder="What are you looking for?">

                         <button type="submit" class="bg-show"><i class="ti-search"></i></button>

                         @if ($show_types)
                             @include('partials.search.advanced_search', ['text_color' => 'white'])
                         @endif

                         <div class="row sm-show d-lg-none">
                             <div class="col-lg-12 col-md-12 mt-1">
                                 <input class="btn btn-warning text-white full-width" type="submit" value="Explore" />
                             </div>
                         </div>

                     </form>
                 </div>


             </div>


         </div>
     </div>
 </div>
