@php
    $compact = $compact ?? false;
    $chart = $chart ?? null;
    $chartUrl = $chartUrl ?? owid_chart_url($chart);
    $class = trim('owid-attribution '.($compact ? 'small text-muted mb-0' : 'text-muted mb-0'));
@endphp
<p class="{{ $class }}" @if($compact) style="font-size:0.8rem;" @endif>
    Data from
    <a href="{{ owid_site_url() }}/" target="_blank" rel="noopener noreferrer">Our World in Data</a>
    (<a href="{{ owid_license_url() }}" target="_blank" rel="noopener noreferrer">CC BY 4.0</a>).
    @if($chartUrl)
        <a href="{{ $chartUrl }}" target="_blank" rel="noopener noreferrer">View chart details</a>.
    @else
        <a href="{{ owid_site_url() }}/" target="_blank" rel="noopener noreferrer">Explore data</a>.
    @endif
</p>
