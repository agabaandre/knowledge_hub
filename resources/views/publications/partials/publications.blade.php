<style>
    /* Ensure card and all links are clickable (override any parent pointer-events) */
    .publication-list-card,
    .publication-list-card a,
    .publication-list-card .publication-image-link { pointer-events: auto !important; }
    .publication-list-card a { cursor: pointer; }
    /* Publication listing: links look like content, not default blue/underlined */
    .publication-list-card .publication-content-col a:not(.btn) {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-content-col a:not(.btn):hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    .publication-list-card .publication-title-desktop a,
    .publication-list-card .publication-title-mobile a {
        color: var(--default-font-color, #212529) !important;
        text-decoration: none !important;
    }
    .publication-list-card .publication-title-desktop a:hover,
    .publication-list-card .publication-title-mobile a:hover {
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: underline !important;
    }
    /* Mobile Styles (phones) - Dropcap images */
    @media (max-width: 767.98px) {
        .publication-card-row {
            display: block !important;
            flex-direction: unset !important;
            flex-wrap: unset !important;
        }
        
        .publication-title-mobile {
            display: none !important;
        }
        
        .publication-image-col {
            width: 120px !important;
            height: 120px !important;
            flex: none !important;
            max-width: 120px !important;
            padding-right: 12px !important;
            padding-left: 0 !important;
            padding-bottom: 0 !important;
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
            position: relative !important;
            order: unset !important;
            shape-outside: margin-box !important;
        }
        
        .publication-image-link {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
        }
        
        .publication-image {
            min-height: 120px !important;
            height: 120px !important;
            width: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            background: transparent !important;
        }
        
        .publication-content-col {
            width: auto !important;
            flex: none !important;
            max-width: 100% !important;
            padding-left: 0 !important;
            display: block !important;
            overflow: visible !important;
            text-align: justify !important;
            order: unset !important;
        }
        
        .publication-title-desktop {
            display: block !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            hyphens: auto !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            font-size: 1rem !important;
            margin-bottom: 0.5rem !important;
        }
        
        .publication-title-desktop a {
            display: block !important;
            text-align: left !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            hyphens: auto !important;
            white-space: normal !important;
            line-height: 1.4 !important;
            text-decoration: none !important;
        }
        
        .publication-content-col h5,
        .publication-content-col p,
        .publication-content-col span {
            text-overflow: unset !important;
            white-space: normal !important;
            overflow: visible !important;
            overflow-wrap: break-word !important;
            word-wrap: break-word !important;
        }
        
        /* Extra small devices (phones in portrait, less than 360px) */
        @media (max-width: 359.98px) {
            .publication-title-desktop {
                font-size: 0.9rem !important;
                line-height: 1.3 !important;
            }
            
            .publication-image-col {
                width: 100px !important;
                height: 100px !important;
                max-width: 100px !important;
            }
            
            .publication-image {
                min-height: 100px !important;
                height: 100px !important;
            }
        }
        
        /* Keep buttons side-by-side on mobile */
        .publication-card-row .d-flex.align-items-center {
            flex-wrap: wrap;
            gap: 4px;
            clear: left;
            margin-top: 1rem;
        }
        
        /* Clear float after content */
        .publication-card-row::after {
            content: "";
            display: table;
            clear: both;
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

    .records-search-infinite-sentinel {
        height: 1px;
        width: 100%;
    }
    .records-search-infinite-loader {
        color: #64748b;
        font-size: 0.875rem;
        padding: 0.5rem 0;
    }
</style>

 @php
     $federatedPublications = $federatedPublications ?? collect();
     $searchInfiniteScroll = (bool) ($searchInfiniteScroll ?? false);
     $loadedCount = (($publications->currentPage() - 1) * $publications->perPage()) + $publications->count();
 @endphp

 @if($federatedPublications->count() > 0)
     @if(request()->filled('term'))
         <div class="mb-3">
             <h5 style="color:var(--theme-color-primary,#119A48);font-size:1rem;">
                 <i class="fa fa-globe-africa me-2"></i>From partner country hubs
             </h5>
         </div>
     @endif
     @foreach($federatedPublications as $row)
         @include('partials.federation.publication_card', ['row' => $row])
     @endforeach
     <p class="mb-3">
         <a href="{{ route('federation.browse', ['term' => request('term')]) }}" class="btn btn-sm theme-bg text-white">
             <i class="fa fa-globe-africa me-1"></i>{{ __('frontend_nav.partner_hubs') }}
         </a>
     </p>
 @endif

 @endif

 <div id="records-search-publications"
      @if($searchInfiniteScroll)
      data-infinite-scroll="1"
      data-current-page="{{ $publications->currentPage() }}"
      data-last-page="{{ $publications->lastPage() }}"
      data-total="{{ $publications->total() }}"
      data-loaded="{{ $loadedCount }}"
      @endif>
     <div id="records-search-publications-list">
         @include('publications.partials.publications_list_items', ['listOffset' => ($publications->currentPage() - 1) * $publications->perPage()])
     </div>

     @if($searchInfiniteScroll && $publications->total() > 0)
         <div class="records-search-infinite-footer py-3 text-center" id="records-search-infinite-footer">
             <p class="text-muted small mb-2" id="records-search-infinite-status">
                 @if($publications->total() > 0)
                     Showing {{ number_format($loadedCount) }} of {{ number_format($publications->total()) }} publications
                 @endif
             </p>
             @if($publications->hasMorePages())
                 <div id="records-search-infinite-sentinel" class="records-search-infinite-sentinel" aria-hidden="true"></div>
                 <div id="records-search-infinite-loader" class="records-search-infinite-loader d-none" aria-live="polite">
                     <i class="fa fa-spinner fa-spin me-1"></i>{{ __('publications.search.loading_more') }}
                 </div>
             @else
                 <p class="text-muted small mb-0" id="records-search-infinite-complete">{{ __('publications.search.all_results_loaded') }}</p>
             @endif
         </div>
     @else
         <div class="py-4 records-search-classic-pagination">{{ $publications->links() }}</div>
     @endif
 </div>

 @if (count($publications) == 0 && ($federatedPublications ?? collect())->count() == 0)
     <div class="row justify-content-center py-5">
         <i class="fa fa-info-circle fa-2x text-muted"></i>
         <h4 class="text-muted">No matching records found</h4>
     </div>

    <div class="row justify-content-center">
        <a href="{{ url('publications/request-content') }}" class="btn btn-dark mt-2">Request Content</a>
    </div>
@endif

{{-- Favourite script is in layouts/app.blade.php for both default and theme1 --}}
