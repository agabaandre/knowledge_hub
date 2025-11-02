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
   
   // Check if AI Search is enabled
   $aiSearchEnabled = settings()->enable_ai_search ?? true;
   
   // Get primary color and create a shade for AI Search button
   $primaryColor = settings()->primary_color ?? '#119A48';
   // Create a darker shade (reduce lightness by ~15%)
   $primaryColorRgb = sscanf($primaryColor, "#%02x%02x%02x");
   if ($primaryColorRgb) {
       $aiSearchColor = sprintf("#%02x%02x%02x", 
           max(0, min(255, (int)($primaryColorRgb[0] * 0.85))),
           max(0, min(255, (int)($primaryColorRgb[1] * 0.85))),
           max(0, min(255, (int)($primaryColorRgb[2] * 0.85)))
       );
   } else {
       $aiSearchColor = '#0e7a3a'; // Fallback darker green
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
                         <div class="search-buttons-container" style="display: flex; align-items: stretch; width: 100%;">
                             <input class="px-3 py-0 main_search" style="font-size: 12pt; flex: 1; border-radius: 0; min-height: 60px; border: none; outline: none; margin: 0;" value="{{ @$search->term }}"
                                 type="search" name="term" placeholder="What are you looking for?">

                             <button type="submit" class="search-btn-primary bg-show" style="
                                 background: {{ $primaryColor }};
                                 border: none;
                                 border-right: {{ $aiSearchEnabled ? '1px solid rgba(255,255,255,0.3)' : 'none' }};
                                 white-space: nowrap;
                                 flex-shrink: 0;
                                 padding: 0.5rem 0.5rem;
                                 min-height: 40px;
                                 display: flex;
                                 align-items: center;
                                 justify-content: center;
                                 gap: 0.5rem;
                                 color: white;
                             "><i class="ti-search"></i> Search</button>
                             
                             @if($aiSearchEnabled)
                                 <button type="button" class="ai-search-btn bg-show" style="
                                     background: {{ $aiSearchColor }};
                                     border: none;
                                     border-left: 1px solid rgba(255,255,255,0.3);
                                     white-space: nowrap;
                                     flex-shrink: 0;
                                     padding: 0.5rem 0.5rem;
                                     min-height: 40px;
                                     display: flex;
                                     align-items: center;
                                     justify-content: center;
                                     gap: 0.5rem;
                                     color: white;
                                 "><i class="fa fa-robot"></i> AI Search</button>
                             @endif
                         </div>

                         @if ($show_types)
                             @include('partials.search.advanced_search', ['text_color' => 'white'])
                         @endif

                         <div class="row sm-show ">
                             <div class="col-lg-12 col-md-12 mt-1">
                                 <input class="btn btn-warning text-white full-width" type="submit" value="Explore" />
                             </div>
                         </div>

                     </form>
                 </div>
                 
                 @if($aiSearchEnabled)
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
                     .search-buttons-container .search-btn-primary,
                     .search-buttons-container .ai-search-btn {
                         /* Override any conflicting styles */
                         position: relative !important;
                         margin: 0 !important;
                         float: none !important;
                         border-radius: 0 !important;
                         height: 100% !important;
                     }
                     .search-btn-primary:hover {
                         opacity: 0.9;
                         filter: brightness(0.95);
                     }
                     .ai-search-btn:hover {
                         opacity: 0.9;
                         filter: brightness(0.95);
                     }
                 </style>
                 <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
                 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />
                 <script>
                 document.addEventListener('DOMContentLoaded', function() {
                     const aiSearchBtn = document.querySelector('.ai-search-btn');
                     if (aiSearchBtn) {
                         aiSearchBtn.addEventListener('click', function(e) {
                             e.preventDefault();
                             if (typeof Lobibox !== 'undefined') {
                                 Lobibox.notify('info', {
                                     title: 'AI Search',
                                     msg: 'Coming Soon',
                                     sound: false,
                                     delay: 3000,
                                     position: 'top right'
                                 });
                             } else {
                                 alert('AI Search - Coming Soon');
                             }
                         });
                     }
                 });
                 </script>
                 @endif


             </div>


         </div>
     </div>
 </div>
