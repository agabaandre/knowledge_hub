@php
    $hide_search = true;
@endphp

@extends('layouts.app')

@section('styles')
<style>

    .faq-search-container {
        margin-bottom: 2rem;
    }

    .faq-search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .faq-search-input:focus {
        border-color: var(--theme-color-primary, #119A48);
        outline: none;
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
    }

    .faqs-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: var(--theme-color-primary, #119A48);
    }

    .faq-item.active {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.15);
    }

    .faq-question {
        padding: 1.25rem 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        transition: background 0.3s ease;
        user-select: none;
    }

    .faq-item.active .faq-question {
        background: rgba(17, 154, 72, 0.05);
        border-bottom: 1px solid #e2e8f0;
    }

    .faq-question:hover {
        background: rgba(17, 154, 72, 0.08);
    }

    .faq-question-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        flex: 1;
        padding-right: 1rem;
        line-height: 1.5;
    }

    .faq-question-number {
        color: var(--theme-color-primary, #119A48);
        font-weight: 700;
        margin-right: 0.75rem;
        min-width: 2rem;
    }

    .faq-toggle-icon {
        color: var(--theme-color-primary, #119A48);
        font-size: 1.25rem;
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }

    .faq-item.active .faq-toggle-icon {
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        padding: 0 1.5rem;
    }

    .faq-item.active .faq-answer {
        max-height: 1000px;
        padding: 1.5rem;
    }

    .faq-answer-content {
        color: #4a5568;
        font-size: 1rem;
        line-height: 1.7;
        margin: 0;
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

    .faqs-stats {
        text-align: center;
        padding: 1rem 0;
        color: #718096;
        font-size: 0.9375rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .custom-bg h1 {
            font-size: 1.75rem;
        }
        
        .custom-bg p {
            font-size: 0.9rem;
        }

        .faq-question {
            padding: 1rem;
        }

        .faq-question-text {
            font-size: 1rem;
        }

        .faq-item.active .faq-answer {
            padding: 1rem;
        }
    }

    /* Dark mode: FAQs page */
    html[data-bs-theme="dark"] .faq-search-input {
        background: #2d3136 !important;
        border-color: #3e4348 !important;
        color: #e4e6eb !important;
    }
    html[data-bs-theme="dark"] .faq-search-input::placeholder { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .faq-search-input:focus { border-color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .faq-item {
        background: #242628 !important;
        border-color: #3e4348 !important;
    }
    html[data-bs-theme="dark"] .faq-item:hover { border-color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .faq-item.active { border-color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .faq-question {
        background: #2d3136 !important;
    }
    html[data-bs-theme="dark"] .faq-item.active .faq-question {
        background: rgba(17, 154, 72, 0.15) !important;
        border-bottom-color: #3e4348 !important;
    }
    html[data-bs-theme="dark"] .faq-question:hover { background: rgba(17, 154, 72, 0.12) !important; }
    html[data-bs-theme="dark"] .faq-question-text { color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .faq-answer-content { color: #d1d5db !important; }
    html[data-bs-theme="dark"] .faq-answer-content a { color: var(--theme-color-primary, #119A48) !important; }
    html[data-bs-theme="dark"] .no-results { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .no-results h3,
    html[data-bs-theme="dark"] .no-results p { color: #d1d5db !important; }
    html[data-bs-theme="dark"] .no-results i { color: #6b7280 !important; }
    html[data-bs-theme="dark"] .faqs-stats { color: #9ca3af !important; }
    html[data-bs-theme="dark"] .faqs-container .pagination,
    html[data-bs-theme="dark"] .faqs-container .page-link { background: #242628 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    html[data-bs-theme="dark"] .faqs-container .page-item.active .page-link { background: var(--theme-color-primary, #119A48) !important; border-color: var(--theme-color-primary, #119A48) !important; color: #fff !important; }
    html[data-bs-theme="dark"] .faqs-container .page-link:hover { background: #3e4348 !important; border-color: #4b5262 !important; color: #e4e6eb !important; }
    .faqs-infinite-sentinel { height: 1px; width: 100%; }
    .faqs-infinite-loader { color: #64748b; font-size: 0.875rem; padding: 0.5rem 0; }
</style>
@endsection

@section('content')
{{-- Custom Header Section (replaces search bar) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;">
                        <i class="fa fa-question-circle me-2"></i>Frequently Asked Questions
                    </h1>
                    <p style="margin: 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        Find answers to common questions about the Africa CDC Knowledge Hub
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<!-- FAQs Content -->
<div class="container">
    <div class="faqs-container">
        <!-- Search Box -->
        <div class="faq-search-container">
            <input type="text" 
                   id="faqSearch" 
                   class="faq-search-input" 
                   placeholder="Search FAQs... (e.g., registration, publications, download)"
                   autocomplete="off">
        </div>

        <!-- FAQs Stats -->
        <div class="faqs-stats">
            <span id="faqCount">{{ $faqs->total() }}</span> Frequently Asked Questions
        </div>

        <!-- FAQs List -->
        <div id="faqs-list-wrap"
             @if(($faqsInfiniteScroll ?? false) && $faqs instanceof \Illuminate\Pagination\AbstractPaginator)
             data-infinite-scroll="1"
             data-current-page="{{ $faqs->currentPage() }}"
             data-last-page="{{ $faqs->lastPage() }}"
             data-total="{{ $faqs->total() }}"
             data-loaded="{{ (($faqs->currentPage() - 1) * $faqs->perPage()) + $faqs->count() }}"
             @endif>
        <div id="faqsList">
            @if($faqs->count() > 0)
                @include('faqs.partials.faq_list_items', [
                    'faqs' => $faqs,
                    'listOffset' => ($faqs instanceof \Illuminate\Pagination\AbstractPaginator)
                        ? (($faqs->currentPage() - 1) * $faqs->perPage())
                        : 0,
                ])
            @else
                <div class="no-results">
                    <i class="fa fa-inbox"></i>
                    <h3>No FAQs Found</h3>
                    <p>There are currently no frequently asked questions available.</p>
                </div>
            @endif
        </div>

        @if(($faqsInfiniteScroll ?? false) && $faqs instanceof \Illuminate\Pagination\AbstractPaginator && $faqs->total() > 0)
            @php $loadedFaqCount = (($faqs->currentPage() - 1) * $faqs->perPage()) + $faqs->count(); @endphp
            <div class="faqs-infinite-footer py-3 text-center" id="faqs-infinite-footer">
                <p class="text-muted small mb-2" id="faqs-infinite-status">
                    Showing {{ number_format($loadedFaqCount) }} of {{ number_format($faqs->total()) }} FAQs
                </p>
                @if($faqs->hasMorePages())
                    <div id="faqs-infinite-sentinel" class="faqs-infinite-sentinel" aria-hidden="true"></div>
                    <div id="faqs-infinite-loader" class="faqs-infinite-loader d-none" aria-live="polite">
                        <i class="fa fa-spinner fa-spin me-1"></i>Loading more FAQs…
                    </div>
                @else
                    <p class="text-muted small mb-0" id="faqs-infinite-complete">All FAQs loaded</p>
                @endif
            </div>
        @elseif($faqs instanceof \Illuminate\Pagination\AbstractPaginator && $faqs->hasPages())
            <div class="mt-4">
                {{ $faqs->links() }}
            </div>
        @endif
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fa fa-search"></i>
            <h3>No Results Found</h3>
            <p>Try adjusting your search terms or browse all FAQs.</p>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    window.faqsInfiniteScrollConfig = {
        enabled: @json((bool) ($faqsInfiniteScroll ?? false)),
        pageUrl: @json(route('faqs.page'))
    };
    window.FAQS_INFINITE_STATUS_COMPLETE = 'All FAQs loaded';
    window.FAQS_INFINITE_STATUS_ERROR = 'Could not load more FAQs. Tap to retry.';
</script>
<script src="{{ asset('js/faqs-index-infinite.js') }}?v={{ @filemtime(public_path('js/faqs-index-infinite.js')) }}"></script>
<script>
    // Toggle FAQ accordion
    function toggleFaq(element) {
        const faqItem = element.closest('.faq-item');
        const isActive = faqItem.classList.contains('active');
        
        // Close all FAQs
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Open clicked FAQ if it wasn't active
        if (!isActive) {
            faqItem.classList.add('active');
            // Scroll to FAQ with smooth behavior
            setTimeout(() => {
                faqItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }
    }

    // FAQ Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('faqSearch');
        const faqsList = document.getElementById('faqsList');
        const noResults = document.getElementById('noResults');
        const faqCount = document.getElementById('faqCount');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const faqItems = faqsList.querySelectorAll('.faq-item');
                let visibleCount = 0;
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question-text').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                    const matches = question.includes(searchTerm) || answer.includes(searchTerm);
                    
                    if (matches) {
                        item.style.display = '';
                        visibleCount++;
                        // Auto-expand matching FAQs
                        if (searchTerm && !item.classList.contains('active')) {
                            item.classList.add('active');
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide no results message
                if (visibleCount === 0 && searchTerm) {
                    faqsList.style.display = 'none';
                    noResults.style.display = 'block';
                } else {
                    faqsList.style.display = '';
                    noResults.style.display = 'none';
                }
                
                // Update count
                faqCount.textContent = visibleCount || faqItems.length;
            });
        }
    });
</script>
@endsection
