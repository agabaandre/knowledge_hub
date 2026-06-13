@if(($aiSearchEnabled ?? false) && mb_strlen(trim((string) request('term', ''))) >= 2)
<div class="records-search-ai-async-loading records-search-async-loading__banner" aria-busy="true" aria-live="polite">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="async-skeleton" style="width:2.25rem;height:2.25rem;border-radius:0.45rem;display:block;background:linear-gradient(90deg,#f1f5f9 0%,#e2e8f0 45%,#f1f5f9 90%);background-size:200% 100%;animation:records-search-shimmer 1.2s ease-in-out infinite;" aria-hidden="true"></span>
        <div class="flex-grow-1">
            <div class="async-skeleton mb-2" style="height:0.9rem;width:42%;background:linear-gradient(90deg,#f1f5f9 0%,#e2e8f0 45%,#f1f5f9 90%);background-size:200% 100%;animation:records-search-shimmer 1.2s ease-in-out infinite;" aria-hidden="true"></div>
            <div class="async-skeleton" style="height:0.7rem;width:78%;background:linear-gradient(90deg,#f1f5f9 0%,#e2e8f0 45%,#f1f5f9 90%);background-size:200% 100%;animation:records-search-shimmer 1.2s ease-in-out infinite;" aria-hidden="true"></div>
        </div>
    </div>
    <p class="text-muted small mb-0">
        <i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i>
        {{ __('publications.search.loading_ai') }}
    </p>
</div>
@endif