@php
    $listOffset = (int) ($listOffset ?? 0);
@endphp
@foreach ($faqs as $faq)
    @php $faqNumber = $listOffset + $loop->iteration; @endphp
    <div class="faq-item" data-faq-index="{{ $faqNumber }}">
        <div class="faq-question" onclick="toggleFaq(this)">
            <div style="display: flex; align-items: center; flex: 1;">
                <span class="faq-question-number">{{ $faqNumber }}.</span>
                <span class="faq-question-text">{{ $faq->question }}</span>
            </div>
            <i class="fa fa-chevron-down faq-toggle-icon"></i>
        </div>
        <div class="faq-answer">
            <div class="faq-answer-content">
                {!! $faq->answer !!}
            </div>
        </div>
    </div>
@endforeach
