@if(!empty($continental_indicators))
<div class="continental-indicators mt-4">
    <h3 class="continental-indicators__title">
        <i class="fa fa-chart-bar me-2"></i>Continental indicators
    </h3>
    <p class="continental-indicators__subtitle text-muted">
        Africa-wide averages or totals from published indicators. Click any card to explore it on the map.
    </p>
    <div class="continental-indicators__list">
        @foreach($continental_indicators as $indicator)
            @php
                $display = $indicator['display'] ?? [];
                $valueLine = ($display['type'] ?? '') === 'percent'
                    ? rtrim($display['value'] ?? '').'%'
                    : ($display['value'] ?? '—');
                $denomination = $display['unit_plain'] ?? '';
            @endphp
            <button type="button"
                    class="continental-indicator-card js-map-indicator-select"
                    data-kpi-id="{{ $indicator['kpi_id'] }}"
                    data-kpi-name="{{ $indicator['name'] }}">
                <div class="continental-indicator-card__head">
                    <span class="continental-indicator-card__subject">{{ $indicator['subject_area'] }}</span>
                    <span class="continental-indicator-card__agg">{{ $indicator['aggregation_label'] ?? '' }}</span>
                </div>
                <div class="continental-indicator-card__name">{{ $indicator['name'] }}</div>
                <div class="continental-indicator-card__value">{{ $valueLine }}</div>
                @if($denomination !== '')
                    <div class="continental-indicator-card__denom">{{ $denomination }}</div>
                @endif
                <div class="continental-indicator-card__meta text-muted">
                    {{ $indicator['country_count'] }} member states with data
                </div>
            </button>
        @endforeach
    </div>
    @include('common.owid_attribution', ['compact' => true])
</div>
@endif
