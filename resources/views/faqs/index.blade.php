@extends('layouts.app')

@section('styles')
<style>
    .faqs-header {
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
        padding: 3rem 0;
        color: white;
        margin-bottom: 3rem;
    }

    .faqs-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: white;
    }

    .faqs-header p {
        font-size: 1.125rem;
        opacity: 0.95;
        margin: 0;
    }

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
        .faqs-header h1 {
            font-size: 2rem;
        }

        .faqs-header {
            padding: 2rem 0;
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
</style>
@endsection

@section('content')
<!-- FAQs Header -->
<div class="faqs-header">
    <div class="container">
        <div class="text-center">
            <h1><i class="fa fa-question-circle mr-2"></i>Frequently Asked Questions</h1>
            <p>Find answers to common questions about the Africa CDC Knowledge Hub</p>
        </div>
    </div>
</div>

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
        <div id="faqsList">
            @php $index = 1; @endphp
            @forelse($faqs as $faq)
                <div class="faq-item" data-faq-index="{{ $index }}">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <div style="display: flex; align-items: center; flex: 1;">
                            <span class="faq-question-number">{{ $index++ }}.</span>
                            <span class="faq-question-text">{{ $faq->question }}</span>
                        </div>
                        <i class="fa fa-chevron-down faq-toggle-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-results">
                    <i class="fa fa-inbox"></i>
                    <h3>No FAQs Found</h3>
                    <p>There are currently no frequently asked questions available.</p>
                </div>
            @endforelse
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fa fa-search"></i>
            <h3>No Results Found</h3>
            <p>Try adjusting your search terms or browse all FAQs.</p>
        </div>

        <!-- Pagination -->
        @if($faqs->hasPages())
            <div class="mt-4">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
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
