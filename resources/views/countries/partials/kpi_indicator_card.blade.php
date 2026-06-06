@php
    $kpiId = (int) ($kpi->kpi_id ?? 0);
    $value = (float) ($kpi->kpi_value ?? 0);
    $decimals = abs($value) >= 100 ? 0 : (abs($value) >= 10 ? 1 : 2);
    $formattedValue = number_format($value, $decimals);
    $periodLabel = substr((string) ($kpi->period ?? ''), 0, 4);
    $hasChart = ! empty($kpi->has_chart);
    $hasDrilldown = ! empty($kpi->has_drilldown);
    $unit = trim((string) ($kpi->unit_label ?? ''));
@endphp
<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
    <div class="country-kpi-tile dro_140 h-100 {{ $hasDrilldown ? 'country-kpi-tile--drilldown kpi-drilldown-trigger' : '' }}"
         @if($hasDrilldown)
             role="button"
             tabindex="0"
             data-kpi-id="{{ $kpiId }}"
             aria-label="View details for {{ $kpi->kpi_name }}"
         @endif>
        <div class="dro_141 de country-kpi-tile__icon">
            <img src="{{ asset('assets/img/common/stats.png') }}" style="max-width:35px;" alt=""/>
        </div>
        <div class="dro_142 country-kpi-tile__body">
            <div class="country-kpi-tile__head">
                <h6 class="country-kpi-tile__title">{{ $kpi->kpi_name }}</h6>
                @if($hasDrilldown)
                    <span class="country-kpi-tile__drill-badge" title="More details available">
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </span>
                @endif
            </div>
            <p class="color-red text-bold country-kpi-tile__value mb-0">
                {{ $formattedValue }}
                @if($unit !== '')
                    <span class="country-kpi-tile__unit">{{ $unit }}</span>
                @endif
            </p>
            <small class="text-muted country-kpi-tile__period">Latest: {{ $periodLabel ?: '—' }}</small>
            @if($hasChart)
                <div class="country-kpi-sparkline kpi-sparkline" id="kpi-sparkline-{{ $kpiId }}" data-kpi-id="{{ $kpiId }}" aria-hidden="true"></div>
            @endif
            @if($hasDrilldown)
                <span class="country-kpi-tile__cta">
                    View details
                    <i class="fa fa-external-link-alt ml-1" style="font-size:0.7rem;" aria-hidden="true"></i>
                </span>
            @endif
        </div>
    </div>
</div>
