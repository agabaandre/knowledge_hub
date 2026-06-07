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
   
   $aiSearchEnabled = (bool) (settings()->enable_ai_search ?? false);
   $primaryColor = settings()->primary_color ?? '#119A48';
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
                         <div class="search-buttons-container" style="display: flex; align-items: stretch; width: 100%;">
                             <input class="px-3 py-0 main_search notranslate" style="font-size: 12pt; flex: 1; border-radius: 0; min-height: 60px; border: none; outline: none; margin: 0;" value="{{ @$search->term }}"
                                 type="search" name="term" placeholder="{{ __('home_sections.search_placeholder') }}" data-khub-i18n="home_sections.search_placeholder">

                             <button type="submit" class="search-btn-primary bg-show" style="
                                 background: {{ $primaryColor }};
                                 border: none;
                                 border-radius: 0 4px 4px 0;
                                 white-space: nowrap;
                                 flex-shrink: 0;
                                 padding: 0.5rem 1rem;
                                 min-height: 40px;
                                 display: flex;
                                 align-items: center;
                                 justify-content: center;
                                 gap: 0.5rem;
                                 color: white;
                             "><i class="ti-search"></i> <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</span>@if($aiSearchEnabled)<span class="d-none d-md-inline" style="opacity:0.85;font-size:0.78rem;">+ AI</span>@endif</button>
                         </div>

                         @if ($show_types)
                             @include('partials.search.advanced_search', ['text_color' => 'white'])
                         @endif

                         <div class="row sm-show ">
                             <div class="col-lg-12 col-md-12 mt-1">
                                 <input class="btn btn-warning text-white full-width notranslate" type="submit" value="{{ __('home_sections.explore') }}" data-khub-i18n="home_sections.explore" />
                             </div>
                         </div>

                     </form>
                 </div>
                 
                 <style>
                     .search-buttons-container {
                         display: flex !important;
                         align-items: stretch !important;
                         width: 100% !important;
                     }
                     .search-buttons-container .main_search {
                         border-radius: 0 !important;
                         height: auto !important;
                         line-height: normal !important;
                         margin: 0 !important;
                         height: 60px !important;
                         margin-top: 8px !important;
                         padding: 0.8rem 1rem !important;
                     }
                     .search-buttons-container .search-btn-primary {
                         position: relative !important;
                         margin: 0 !important;
                         float: none !important;
                         border-radius: 0 4px 4px 0 !important;
                         height: 60px !important;
                         margin-top: 3px !important;
                         padding: 0.8rem 1rem !important;
                     }
                     .search-btn-primary:hover {
                         opacity: 0.9;
                         filter: brightness(0.95);
                     }
                 </style>


             </div>


         </div>
     </div>
 </div>
