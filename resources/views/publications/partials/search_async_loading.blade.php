<div class="records-search-async-loading" aria-busy="true" aria-live="polite">
    <style>
        .records-search-async-loading .async-skeleton {
            background: linear-gradient(90deg, #f1f5f9 0%, #e2e8f0 45%, #f1f5f9 90%);
            background-size: 200% 100%;
            animation: records-search-shimmer 1.2s ease-in-out infinite;
            border-radius: 0.35rem;
        }
        @keyframes records-search-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .records-search-async-loading__banner {
            border: 1px solid #dbeafe;
            border-radius: 0.5rem;
            background: linear-gradient(180deg, #f8fbff 0%, #fff 100%);
            padding: 0.85rem 1rem;
            margin-bottom: 1rem;
        }
        .records-search-async-loading__card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.85rem;
        }
    </style>

    <p class="text-muted small mb-3">
        <i class="fa fa-spinner fa-spin me-1" aria-hidden="true"></i>
        {{ __('publications.search.loading_results') }}
    </p>

    <div class="records-search-async-loading__card">
        <div class="async-skeleton mb-2" style="height:0.85rem;width:35%;" aria-hidden="true"></div>
        <div class="async-skeleton" style="height:0.75rem;width:22%;" aria-hidden="true"></div>
    </div>

    @for ($i = 0; $i < 4; $i++)
    <div class="records-search-async-loading__card">
        <div class="d-flex gap-3">
            <span class="async-skeleton" style="width:96px;height:96px;flex-shrink:0;" aria-hidden="true"></span>
            <div class="flex-grow-1">
                <div class="async-skeleton mb-2" style="height:0.95rem;width:88%;" aria-hidden="true"></div>
                <div class="async-skeleton mb-2" style="height:0.75rem;width:100%;" aria-hidden="true"></div>
                <div class="async-skeleton" style="height:0.75rem;width:72%;" aria-hidden="true"></div>
            </div>
        </div>
    </div>
    @endfor
</div>
