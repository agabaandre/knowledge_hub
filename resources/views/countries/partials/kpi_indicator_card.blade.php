@php
    $kpiId = (int) ($kpi->kpi_id ?? 0);
    $value = (float) ($kpi->kpi_value ?? 0);
    $decimals = abs($value) >= 100 ? 0 : (abs($value) >= 10 ? 1 : 2);
    $formattedValue = number_format($value, $decimals);
    $periodLabel = substr((string) ($kpi->period ?? ''), 0, 4);
    $hasDrilldown = ! empty($kpi->has_drilldown);
    $unit = trim((string) ($kpi->unit_label ?? ''));
    $kpiName = trim((string) ($kpi->kpi_name ?? ''));
    $showUnit = $unit !== '' && strcasecmp($unit, $kpiName) !== 0;
@endphp
<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mt-2">
    <div class="dro_140 country-kpi-tile {{ $hasDrilldown ? 'country-kpi-tile--drilldown kpi-drilldown-trigger' : '' }}"
         @if($hasDrilldown)
             role="button"
             tabindex="0"
             data-kpi-id="{{ $kpiId }}"
             aria-label="View details for {{ $kpiName }}"
             title="View details"
         @endif>
        <div class="dro_141 de">
            <img src="{{ asset('assets/img/common/stats.png') }}" style="max-width:35px;" alt=""/>
        </div>
        <div class="dro_142 country-kpi-tile__body">
            <h6 class="country-kpi-tile__title">{{ $kpiName }}</h6>
            <p class="color-red text-bold country-kpi-tile__value mb-0">
                {{ $formattedValue }}@if($showUnit)<span class="country-kpi-tile__unit">{{ $unit }}</span>@endif
            </p>
            @if($periodLabel !== '')
                <small class="text-muted country-kpi-tile__period">Latest: {{ $periodLabel }}</small>
            @endif
        </div>
        @if($hasDrilldown)
            <span class="country-kpi-tile__drill-badge" aria-hidden="true">
                <i class="fa fa-chevron-right"></i>
            </span>
        @endif
    </div>
</div>
