@if(isset($healthEmergencies) && $healthEmergencies->isNotEmpty())
@php
    $primary = settings()->primary_color ?? '#119A48';
@endphp
<style>
    .home-health-emergencies {
        padding: 1.25rem 0 0.5rem;
    }
    .home-health-emergencies h2 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.75rem;
        text-align: center;
    }
    .home-health-emergencies-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.5rem 0.65rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .home-health-emergencies-list a {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #fff;
        color: #334155;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }
    .home-health-emergencies-list a:hover {
        border-color: {{ $primary }};
        color: {{ $primary }};
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
</style>
<section class="home-health-emergencies" aria-labelledby="home-health-emergencies-heading">
    <div class="container">
        <h2 id="home-health-emergencies-heading" class="notranslate" data-khub-i18n="home_sections.health_emergencies">{{ __('home_sections.health_emergencies') }}</h2>
        <ul class="home-health-emergencies-list">
            @foreach($healthEmergencies as $tag)
                <li>
                    <a href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
