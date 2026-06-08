@php
    $healthThemesTitleDefault = \App\Support\UiLocaleLabels::homeSection('health_themes');
    $themeCardOpacityDefault = settings()->theme_card_opacity ?? '1';
    $themeCardOpacityDefault = is_numeric($themeCardOpacityDefault) ? max(0.5, min(1, (float)$themeCardOpacityDefault)) : 1;
    $themeCardsPerRow = (int) (settings()->theme_cards_per_row ?? 4);
    $themeCardsPerRow = max(2, min(8, $themeCardsPerRow));
@endphp
<style>
    .theme-grid {
        padding: 0.65rem 0 0.35rem;
        display: flex;
        align-items: center;
        min-width: 100%;
        z-index: 800;
    }

    .themes-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.65rem;
        margin-top: 0;
        text-align: center;
        position: relative;
    }


    /* Fixed grid container */
    .themes-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .themes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.5rem;
        width: 100%;
    }

    .theme-item {
        width: 100%;
        /* Removed max-width that was causing the single column issue */
    }

    .theme-card {
        background: #ffffff;
        border-radius: 0.25rem;
        padding: 1.25rem 1rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        width: 100%;
        opacity: {{ $themeCardOpacityDefault }};
    }

    .theme-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #119A48, #16c653);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .theme-card:hover {
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.15), 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-color: rgba(17, 154, 72, 0.1);
    }

    .theme-card:hover::before {
        transform: scaleX(1);
    }

    .theme-card:hover .theme-icon {
        transform: scale(1.1);
        color: #16c653;
    }

    .theme-icon {
        font-size: 1.5rem;
        color: #119A48;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        display: block;
    }

    .theme-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        line-height: 1.4;
        margin: 0;
        transition: color 0.3s ease;
    }

    .theme-card:hover .theme-title {
        color: #2d3748;
    }

    .theme-link {
        text-decoration: none;
        display: block;
        height: 100%;
        width: 100%;
    }

    .theme-link:hover {
        text-decoration: none;
    }

    /* Responsive adjustments */
    @media (min-width: 1200px) {
        .themes-grid {
            grid-template-columns: repeat({{ $themeCardsPerRow }}, 1fr);
        }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
        .themes-grid {
            grid-template-columns: repeat({{ $themeCardsPerRow }}, 1fr);
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {
        .themes-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 576px) and (max-width: 767px) {
        .themes-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575px) {
        .theme-grid {
            padding: 1rem 0;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .themes-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .theme-card {
            min-height: 100px;
            padding: 1rem 0.75rem;
        }
        
        .theme-icon {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        
        .theme-title {
            font-size: 0.8rem;
        }
    }

    /* Loading animation */
    .theme-card {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    @keyframes fadeInUp {
        to {
            opacity: {{ $themeCardOpacityDefault }};
            transform: translateY(0);
        }
    }

    /* Stagger animation delay */
    .theme-item:nth-child(1) .theme-card { animation-delay: 0.1s; }
    .theme-item:nth-child(2) .theme-card { animation-delay: 0.2s; }
    .theme-item:nth-child(3) .theme-card { animation-delay: 0.3s; }
    .theme-item:nth-child(4) .theme-card { animation-delay: 0.4s; }
    .theme-item:nth-child(5) .theme-card { animation-delay: 0.5s; }
    .theme-item:nth-child(6) .theme-card { animation-delay: 0.6s; }
    .theme-item:nth-child(7) .theme-card { animation-delay: 0.7s; }
    .theme-item:nth-child(8) .theme-card { animation-delay: 0.8s; }
</style>

<section class="theme-grid">
    <div class="themes-container">
        <h3 class="themes-section-title notranslate" data-khub-i18n="home_sections.health_themes">
            {{ $healthThemesTitleDefault }}
        </h3>
        
        <div class="themes-grid">
            @foreach ($themes as $index => $theme)
                <div class="theme-item">
                    <a href="{{ thematic_area_records_url($theme) }}" class="theme-link">
                        <div class="theme-card">
                            <i class="fa {{ $theme->icon }} theme-icon" aria-hidden="true"></i>
                            <p class="theme-title" title="{{ $theme->description }}">
                                {{ truncate($theme->description, 24) }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>