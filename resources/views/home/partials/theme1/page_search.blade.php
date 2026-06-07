 <!-- ======================= Searchbar Banner ======================== -->

 @php
     $currentUrl = current_url();
     $show_types = strpos($currentUrl, 'record') > -1 || strpos($currentUrl, 'publication') > -1 || strpos($currentUrl, 'favourites') > -1 ? true : false;
     
     // Determine search action URL based on current page (check specific pages first)
     $searchAction = url('records/search'); // Default to general records search
     
     if (strpos($currentUrl, '/account/publications') > -1) {
         // Search user's own publications (must check before general publication check)
         $searchAction = url('account/publications');
     } elseif (strpos($currentUrl, '/faqs') > -1 || strpos($currentUrl, '/faq') > -1) {
         // Search FAQs
         $searchAction = url('faqs');
     } elseif (strpos($currentUrl, '/communities') > -1) {
         // Search communities
         $searchAction = url('communities');
    } elseif (strpos($currentUrl, '/courses') > -1) {
        // Search courses
        $searchAction = url('courses');
    } elseif (strpos($currentUrl, '/browse/authors') > -1 || strpos($currentUrl, 'authors') > -1) {
        // Search authors
        $searchAction = url('browse/authors');
    } elseif (strpos($currentUrl, 'forums') > -1 || strpos($currentUrl, 'thread') > -1) {
        // Search forums
        $searchAction = url('forums');
    } elseif (strpos($currentUrl, 'record') > -1 || strpos($currentUrl, 'publication') > -1 || strpos($currentUrl, 'favourites') > -1) {
        // Search general records
        $searchAction = url('records/search');
    }
 @endphp

 <div class="pt-5 pt-0 custom-bg">
     <div class="container">
         <div class="row justify-content-between align-items-center">

             <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


                 <div class="single_widgets widget_search px-0 py-0"
                     style="background-color: transparent!important; border:none!important;">
                     <form
                         action="{{ $searchAction }}"
                         class="sidebar-search-form px-0 py-0 filter">
                         <input class="px-3 py-0 main_search notranslate" style="font-size: 12pt;" value="{{ @$search->term }}"
                             type="search" name="term" placeholder="{{ __('home_sections.search_placeholder') }}" data-khub-i18n="home_sections.search_placeholder">

                         <button type="submit" class="bg-show"><i class="ti-search"></i></button>

                         @if ($show_types)
                             @include('partials.search.advanced_search', ['text_color' => 'white'])
                         @endif

                         <div class="row sm-show d-lg-none">
                             <div class="col-lg-12 col-md-12 mt-1">
                                 <input class="btn btn-warning text-white full-width notranslate" type="submit" value="{{ __('home_sections.explore') }}" data-khub-i18n="home_sections.explore" />
                             </div>
                         </div>

                     </form>
                 </div>


             </div>


         </div>
     </div>
 </div>
