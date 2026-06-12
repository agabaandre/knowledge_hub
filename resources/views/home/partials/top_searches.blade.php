   <!-- ======================= Top Searches List ======================== -->
  <section class="middle gray" style="padding-top: 0; padding-bottom: 20px; margin-top: -1rem;">
       <style>
           /* Mobile Styles - Dropcap images */
           @media (max-width: 767.98px) {
               .top-searches-card {
                   display: block !important;
                   flex-direction: unset !important;
                   flex-wrap: unset !important;
               }
               
               .top-searches-card .cats-box {
                   display: block !important;
               }
               
               .top-searches-image {
                   width: 120px !important;
                   height: 120px !important;
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
                   padding: 4px !important;
                   shape-outside: margin-box !important;
               }
               
               .top-searches-image img {
                   object-fit: contain !important;
                   object-position: center !important;
                   background: transparent !important;
               }
               
               .top-searches-content {
                   display: block !important;
                   overflow: visible !important;
                   text-align: justify !important;
                   width: auto !important;
               }
               
               .top-searches-content h4,
               .top-searches-content .text-truncate,
               .top-searches-content span {
                   text-overflow: unset !important;
                   white-space: normal !important;
                   overflow: visible !important;
               }
               
               /* Clear float after content on mobile */
               .top-searches-card::after {
                   content: "";
                   display: table;
                   clear: both;
               }
           }

           .records-search-infinite-sentinel { height: 1px; width: 100%; }
           .records-search-infinite-loader { color: #64748b; font-size: 0.875rem; padding: 0.5rem 0; }
       </style>
       <div class="container">

           <div class="row justify-content-center" data-aos="fade-in">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                  <div class="sec_title position-relative text-center mb-3" style="margin-bottom: 1rem !important;">
                       <h2 class="ft-bold notranslate" data-khub-i18n="home_sections.top_searches">{{ \App\Support\UiLocaleLabels::homeSection('top_searches') }}</h2>
                   </div>
               </div>
           </div>

           <!-- row -->
          @php
              $topSearchesTotal = (int) ($topSearchesTotal ?? count($recent));
              $topSearchesLoaded = count($recent);
          @endphp
          <div id="home-top-searches"
               data-initial="{{ (int) ($topSearchesInitial ?? 6) }}"
               data-page-size="{{ (int) ($topSearchesPageSize ?? 10) }}"
               data-loaded="{{ $topSearchesLoaded }}"
               data-total="{{ $topSearchesTotal }}">
              <div class="row align-items-center" id="home-top-searches-list" style="margin-top: 0;">
                  @include('home.partials.top_searches_list_items', ['listOffset' => 0])
              </div>

              @if($topSearchesLoaded < $topSearchesTotal)
                  <div class="records-search-infinite-footer py-3 text-center" id="home-top-searches-footer">
                      <p class="text-muted small mb-2" id="home-top-searches-status">
                          Showing {{ number_format($topSearchesLoaded) }} of {{ number_format($topSearchesTotal) }} resources
                      </p>
                      <div id="home-top-searches-sentinel" class="records-search-infinite-sentinel" aria-hidden="true"></div>
                      <div id="home-top-searches-loader" class="records-search-infinite-loader d-none" aria-live="polite">
                          <i class="fa fa-spinner fa-spin me-1"></i>{{ __('publications.search.loading_more') }}
                      </div>
                  </div>
              @else
                  <p class="text-muted small text-center py-2 mb-0" id="home-top-searches-complete">{{ __('publications.search.all_results_loaded') }}</p>
              @endif
          </div>
           <!-- row -->

           <div class="row justify-content-center">
               <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                   <div class="position-relative text-center">
                       <a id="explore" href="{{ url('records') }}"
                           class="btn btn-md theme-bg rounded text-light hover-theme notranslate" data-khub-i18n="home_sections.explore_more_resources">{{ __('home_sections.explore_more_resources') }}<i
                               class="lni lni-arrow-right-circle ml-2"></i></a>
                   </div>
               </div>
           </div>

       </div>
   </section>
   <!-- ======================= Top Searches ======================== -->
