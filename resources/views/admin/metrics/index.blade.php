@extends(admin_layout())

@section('content')
<div class="row charts">
    @include('admin.metrics.graphs_html', array_merge([
        'from' => request('from'),
        'to' => request('to'),
        'country' => request('country'),
    ], [
        'map_indicators' => $map_indicators ?? collect(),
        'map_regions' => $map_regions ?? collect(),
        'initial_kpi_map' => $initial_kpi_map ?? null,
        'continental_indicators' => $continental_indicators ?? [],
        'map_data_url' => $map_data_url ?? (\Illuminate\Support\Facades\Route::has('countries.map-data') ? route('countries.map-data') : url('/countries/map-data')),
    ]))
</div>
@endsection

@section('scripts')
@include('admin.metrics.charts_script')
<script>
    $(function() {
        if (typeof window.renderMetricsCharts === 'function') {
            window.renderMetricsCharts(@json($chart_data), {
                visitCountries: @json(app(\App\Repositories\MetricsRepository::class)->listVisitCountries(request('from'), request('to'))),
                selectedCountry: @json(request('country')),
                summary: @json($summary ?? null),
                cache: { redis: @json(\App\Support\MetricsCache::redisAvailable()) },
                liveParams: {
                    from: @json(request('from')) || undefined,
                    to: @json(request('to')) || undefined,
                    country: @json(request('country')) || undefined
                }
            });
        }
        $('#applyFilters').on('click', function() {
            var params = {
                from: $('#fromDate').val() || '',
                to: $('#toDate').val() || '',
                country: $('#countryFilter').val() || ''
            };
            window.location.href = '{{ url('admin/metrics') }}?' + $.param(params);
        });
        $('.metrics-preset').on('click', function() {
            if (typeof window.applyMetricsDatePreset === 'function') {
                window.applyMetricsDatePreset($(this).data('preset'));
            }
            $('#applyFilters').trigger('click');
        });
    });
</script>
@endsection
