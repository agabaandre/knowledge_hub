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
