@php
    $facts = $facts ?? collect();
    $primary = settings()->primary_color ?? '#119A48';
    $sidebarFacts = $facts instanceof \Illuminate\Support\Collection ? $facts->take(5) : collect($facts)->take(5);
@endphp
@if($sidebarFacts->isNotEmpty())
<div class="contributor-sidebar-panel contributor-facts-panel">
    <div class="contributor-sidebar-panel__header">
        <i class="fa fa-lightbulb-o contributor-sidebar-panel__icon" aria-hidden="true"></i>
        <h3 class="contributor-sidebar-panel__title">Did you know?</h3>
    </div>
    <p class="contributor-sidebar-panel__lead">Quick public-health facts from the Africa Health Knowledge Hub.</p>
    <div class="contributor-facts-list">
        @foreach($sidebarFacts as $row)
            <article class="contributor-fact-card">
                <h4 class="contributor-fact-card__title">{{ $row->fact_title }}</h4>
                @if(!empty($row->fact_summary))
                    <p class="contributor-fact-card__summary">{{ Str::limit(strip_tags($row->fact_summary), 140) }}</p>
                @endif
                <a href="{{ url('facts/fact') }}?ref={{ $row->id }}" class="contributor-fact-card__link">
                    Learn more <i class="fa fa-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
        @endforeach
    </div>
</div>
@endif
