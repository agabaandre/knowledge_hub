@extends(admin_layout())

@section('content')
<div class="row charts">
    @include('admin.metrics.graphs_html', [
        'from' => request('from'),
        'to' => request('to'),
        'country' => request('country'),
    ])
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('assets/plugins/highcharts/highmaps.js') }}"></script>
@include('admin.metrics.charts_script')
<script>
    $(function() {
        if (typeof window.renderMetricsCharts === 'function') {
            window.renderMetricsCharts(@json($chart_data), {
                visitCountries: @json(app(\App\Repositories\MetricsRepository::class)->listVisitCountries(request('from'), request('to'))),
                selectedCountry: @json(request('country'))
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
