<style>
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
     $listingInfiniteScroll = (bool) ($listingInfiniteScroll ?? $searchInfiniteScroll ?? false);
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

 <div id="records-search-publications" class="publication-feed-card-scope"
      @if($listingInfiniteScroll)
      data-infinite-scroll="1"
      data-current-page="{{ $publications->currentPage() }}"
      data-last-page="{{ $publications->lastPage() }}"
      data-total="{{ $publications->total() }}"
      data-loaded="{{ $loadedCount }}"
      @endif>
     <div id="records-search-publications-list">
         @include('publications.partials.publications_list_items', ['listOffset' => ($publications->currentPage() - 1) * $publications->perPage()])
     </div>

     @if($listingInfiniteScroll && $publications->total() > 0)
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
