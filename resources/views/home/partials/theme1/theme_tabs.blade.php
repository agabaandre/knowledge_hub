@php
    $primary = settings()->primary_color ?? '#119A48';
    $healthThemesTitle = \App\Support\UiLocaleLabels::homeSection('health_themes');
    $themeCardOpacity = settings()->theme_card_opacity ?? '1';
    $themeCardOpacity = is_numeric($themeCardOpacity) ? max(0.5, min(1, (float)$themeCardOpacity)) : 1;
    $themeCardsPerRow = (int) (settings()->theme_cards_per_row ?? 4);
    $themeCardsPerRow = max(2, min(8, $themeCardsPerRow));
@endphp
<style>
.theme1-themes-section {
    padding: 0.5rem 0 0.75rem;
    width: 100%;
}
.theme1-themes-section .theme1-themes-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-color-primary, #1e293b);
    margin: 0 0 0.65rem;
    text-align: center;
    letter-spacing: -0.01em;
}
.theme1-themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem;
    max-width: 100%;
    margin: 0 auto;
}
.theme1-theme-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.25rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    transition: box-shadow 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    text-decoration: none;
    color: inherit;
    min-height: 110px;
    opacity: {{ $themeCardOpacity }};
}
.theme1-theme-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border-color: {{ $primary }};
    color: {{ $primary }};
    transform: translateY(-2px);
}
.theme1-theme-card .theme1-theme-icon {
    font-size: 1.75rem;
    color: {{ $primary }};
    margin-bottom: 0.5rem;
    transition: color 0.2s ease, transform 0.2s ease;
}
.theme1-theme-card:hover .theme1-theme-icon {
    transform: scale(1.08);
}
.theme1-theme-card .theme1-theme-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #334155;
    text-align: center;
    line-height: 1.35;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}
.theme1-theme-card:hover .theme1-theme-title {
    color: {{ $primary }};
}
@media (min-width: 576px) {
    .theme1-themes-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 768px) {
    .theme1-themes-grid { grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
}
@media (min-width: 992px) {
    .theme1-themes-grid { grid-template-columns: repeat({{ $themeCardsPerRow }}, 1fr); }
}
</style>
<section class="theme1-themes-section" id="themes">
    <div class="container">
        <h3 class="theme1-themes-title notranslate" data-khub-i18n="home_sections.health_themes">{{ $healthThemesTitle }}</h3>
        <div class="theme1-themes-grid">
            @foreach ($themes as $theme)
                <a href="{{ url('records/subtheme') }}?subtheme={{ $theme->id }}" class="theme1-theme-card" title="{{ $theme->description }}">
                    <i class="fa {{ $theme->icon }} theme1-theme-icon" aria-hidden="true"></i>
                    <p class="theme1-theme-title">{{ truncate($theme->description, 24) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
