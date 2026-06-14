@php
    $primary = settings()->primary_color ?? '#119A48';
    $primaryRgb = '17, 154, 72';
    if (preg_match('/^#([0-9a-f]{6})$/i', $primary, $m)) {
        $primaryRgb = hexdec(substr($m[1], 0, 2)) . ', ' . hexdec(substr($m[1], 2, 2)) . ', ' . hexdec(substr($m[1], 4, 2));
    }
@endphp

<style>
    .kh-key-sections {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
    }
    .kh-key-sections .sec_title h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }
    .kh-key-sections .sec_title p {
        color: #64748b;
        font-size: 1rem;
        max-width: 36rem;
        margin: 0.75rem auto 0;
    }
    .kh-key-sections-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1.25rem;
        margin-top: 2.25rem;
    }
    @media (min-width: 576px) {
        .kh-key-sections-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 992px) {
        .kh-key-sections-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .kh-key-section-card {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.5rem 1.35rem 1.25rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03);
        text-decoration: none;
        color: inherit;
        overflow: hidden;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }
    .kh-key-section-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, {{ $primary }}, rgba({{ $primaryRgb }}, 0.45));
        opacity: 0;
        transition: opacity 0.22s ease;
    }
    .kh-key-section-card:hover {
        transform: translateY(-4px);
        border-color: rgba({{ $primaryRgb }}, 0.35);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        color: inherit;
        text-decoration: none;
    }
    .kh-key-section-card:hover::before { opacity: 1; }
    .kh-key-section-card__bg-icon {
        position: absolute;
        right: 0.75rem;
        bottom: 0.35rem;
        z-index: 0;
        pointer-events: none;
        line-height: 1;
        color: rgba({{ $primaryRgb }}, 0.1);
        font-size: 4.75rem;
        transition: color 0.22s ease, transform 0.22s ease;
    }
    .kh-key-section-card:hover .kh-key-section-card__bg-icon {
        color: rgba({{ $primaryRgb }}, 0.16);
        transform: scale(1.04);
    }
    .kh-key-section-card__content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 7.5rem;
    }
    .kh-key-section-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .kh-key-section-card__title {
        font-size: 1.0625rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin: 0;
    }
    .kh-key-section-card__badge {
        flex-shrink: 0;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: {{ $primary }};
        background: rgba({{ $primaryRgb }}, 0.1);
        border: 1px solid rgba({{ $primaryRgb }}, 0.15);
    }
    .kh-key-section-card__desc {
        flex: 1;
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.55;
        margin: 0 0 1rem;
        max-width: 88%;
    }
    .kh-key-section-card__cta {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: auto;
        font-size: 0.8125rem;
        font-weight: 600;
        color: {{ $primary }};
    }
</style>

<section class="py-5 kh-key-sections" id="categorization" style="margin-bottom: 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-10">
                <div class="sec_title text-center">
                    <h2 class="notranslate" data-khub-i18n="home_sections.explore_key_sections">{{ __('home_sections.explore_key_sections') }}</h2>
                    <p class="notranslate mb-0" data-khub-i18n="home_sections.explore_key_sections_subtitle">{{ __('home_sections.explore_key_sections_subtitle') }}</p>
                </div>
            </div>
        </div>

        <div class="kh-key-sections-grid">
            @foreach ($categories as $category)
                @php
                    $statCount = isset($category['stats']) ? (int) $category['stats'] : null;
                @endphp
                <a href="{{ url($category['link']) }}" class="kh-key-section-card">
                    <div class="kh-key-section-card__bg-icon" aria-hidden="true">
                        <i class="{{ $category['icon'] }}"></i>
                    </div>
                    <div class="kh-key-section-card__content">
                        <div class="kh-key-section-card__head">
                            <h3 class="kh-key-section-card__title">{{ $category['title'] }}</h3>
                            @if ($statCount !== null)
                                <span class="kh-key-section-card__badge">{{ number_format($statCount) }}</span>
                            @endif
                        </div>
                        @if (! empty($category['description']))
                            <p class="kh-key-section-card__desc">{{ $category['description'] }}</p>
                        @endif
                        <span class="kh-key-section-card__cta">
                            <span class="notranslate" data-khub-i18n="home_sections.explore">{{ __('home_sections.explore') }}</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
