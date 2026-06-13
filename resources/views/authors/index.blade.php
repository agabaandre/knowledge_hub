@php
    $hide_search = false;
@endphp

@extends('layouts.app')

@section('structured_data')
@if(!empty($authorsIndexJsonLd))
<script type="application/ld+json">{!! json_encode($authorsIndexJsonLd, $jsonLdFlags ?? (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !!}</script>
@endif
@endsection

@section('styles')
<style>
    .authors-wrapper {
        padding: 2rem 0;
    }

    .authors-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .authors-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .authors-header .authors-lead {
        color: #64748b;
        font-size: 1rem;
        margin: 0 auto;
        max-width: 42rem;
        line-height: 1.6;
    }

    .author-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .author-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--theme-color-primary, #119A48);
        transform: translateY(-2px);
    }

    .author-header {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .author-avatar {
        width: 70px;
        height: 70px;
        border-radius: 4px;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .author-avatar-icon {
        font-size: 2rem;
        color: var(--theme-color-primary, #119A48);
    }

    .author-info {
        flex: 1;
        min-width: 0;
    }

    .author-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        word-wrap: break-word;
    }

    .author-name a {
        color: inherit;
        text-decoration: none;
    }

    .author-name a:hover {
        color: var(--theme-color-primary, #119A48);
        text-decoration: none;
    }

    .author-title {
        font-size: 0.875rem;
        color: #4a5568;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .author-organization {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .author-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .author-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
        border-radius: 4px;
        white-space: nowrap;
    }

    .author-stats {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .author-resources {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .author-resources i {
        color: var(--theme-color-primary, #119A48);
        margin-right: 0.25rem;
    }

    .view-link {
        color: var(--theme-color-primary, #119A48);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
    }

    .view-link:hover {
        text-decoration: underline;
    }

    .no-results {
        text-align: center;
        padding: 3rem 2rem;
        color: #718096;
    }

    .no-results i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e0;
    }

    @media (max-width: 768px) {
        .author-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .author-info {
            text-align: center;
        }

        .author-badges {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="authors-wrapper">
<div class="container">
        <header class="authors-header">
            <h1>
                <i class="fa fa-users me-2" aria-hidden="true"></i>Contributors &amp; Authors
                @if(isset($authorsTotal) && $authorsTotal > 0)
                    <small class="text-muted">({{ number_format($authorsTotal) }} total)</small>
                @elseif(isset($authors) && method_exists($authors, 'total'))
                    <small class="text-muted">({{ number_format($authors->total()) }} total)</small>
                @endif
            </h1>
            <p class="authors-lead">
                @if(!empty($authorsSearchTerm))
                    Showing contributors matching <strong class="notranslate">{{ $authorsSearchTerm }}</strong>.
                @else
                    Discover researchers, clinicians, ministries, and institutions sharing verified public health publications, resources, and forum expertise across Africa.
                @endif
            </p>
        </header>
					
        @if(isset($authors) && $authors->count() > 0)
        <div id="authors-list-wrap"
             @if(($authorsInfiniteScroll ?? false) && $authors instanceof \Illuminate\Pagination\AbstractPaginator)
             data-infinite-scroll="1"
             data-current-page="{{ $authors->currentPage() }}"
             data-last-page="{{ $authors->lastPage() }}"
             data-total="{{ $authors->total() }}"
             data-loaded="{{ (($authors->currentPage() - 1) * $authors->perPage()) + $authors->count() }}"
             @endif>
        <div class="row" id="authors-list">
            @include('authors.partials.author_list_items', ['authors' => $authors])
        </div>

        @if(($authorsInfiniteScroll ?? false) && $authors instanceof \Illuminate\Pagination\AbstractPaginator && $authors->total() > 0)
            @php $loadedAuthorCount = (($authors->currentPage() - 1) * $authors->perPage()) + $authors->count(); @endphp
            <div class="authors-infinite-footer py-3 text-center" id="authors-infinite-footer">
                <p class="text-muted small mb-2" id="authors-infinite-status">
                    Showing {{ number_format($loadedAuthorCount) }} of {{ number_format($authors->total()) }} contributors
                </p>
                @if($authors->hasMorePages())
                    <div id="authors-infinite-sentinel" class="authors-infinite-sentinel" aria-hidden="true" style="height:1px;"></div>
                    <div id="authors-infinite-loader" class="authors-infinite-loader d-none" aria-live="polite">
                        <i class="fa fa-spinner fa-spin me-1"></i>Loading more contributors…
                    </div>
                @else
                    <p class="text-muted small mb-0" id="authors-infinite-complete">All contributors loaded</p>
                @endif
            </div>
        @elseif($authors instanceof \Illuminate\Pagination\AbstractPaginator && $authors->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    {{ $authors->links() }}
                </div>
            </div>
        </div>
        @endif
        </div>
        @else
        <div class="no-results">
            <i class="fa fa-users"></i>
            <h3>No Contributors Found</h3>
            <p>No contributors match your search criteria.</p>
        </div>
        @endif
</div>
</div>
@endsection

@section('scripts')
<script>
    window.authorsInfiniteScrollConfig = {
        enabled: @json((bool) ($authorsInfiniteScroll ?? false)),
        pageUrl: @json(route('browse.authors.page'))
    };
    window.AUTHORS_INFINITE_STATUS_COMPLETE = 'All contributors loaded';
    window.AUTHORS_INFINITE_STATUS_ERROR = 'Could not load more contributors. Tap to retry.';
</script>
<script src="{{ asset('js/authors-index-infinite.js') }}?v={{ @filemtime(public_path('js/authors-index-infinite.js')) }}"></script>
@endsection