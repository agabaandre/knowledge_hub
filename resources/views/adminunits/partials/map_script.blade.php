@if(!empty($show_admin_units_map) && !empty($admin_units_map_settings))
<script>
window.__khAfricaMapSettings = @json($admin_units_map_settings);
</script>
@include('partials.maps.africa_map_loader')
<script>
(function () {
    var mapDataUrl = @json(route('adminunits.map-data'));
    var auColors = {
        green: '{{ settings()->au_corporate_green ?? '#1A5632' }}',
        gold: '{{ settings()->au_gold ?? '#B4A269' }}',
        red: '{{ settings()->au_red ?? '#9F2241' }}',
        light: '#f0f7f4'
    };
    var mapChart = null;

    function renderMap(mapPayload) {
        var container = document.getElementById('adminUnitsMapChart');
        if (!container || !window.KhAfricaMap || typeof Highcharts === 'undefined' || !Highcharts.mapChart) {
            return;
        }

        KhAfricaMap.load().then(function (mapAsset) {
            if (mapChart) {
                mapChart.destroy();
                mapChart = null;
            }

            var joinBy = KhAfricaMap.joinByPairs();
            var seriesData = (mapPayload.points || []).map(function (p) {
                return KhAfricaMap.mapPoint(p);
            });

            mapChart = Highcharts.mapChart('adminUnitsMapChart', {
                chart: {
                    map: KhAfricaMap.chartMapOption(mapAsset),
                    backgroundColor: '#f8f9fa',
                    height: 460
                },
                title: {
                    text: @json($admin_units_map_settings['countryName'] ?? 'Administrative units'),
                    style: { fontSize: '14px', color: '#64748b' }
                },
                credits: {
                    enabled: true,
                    text: KhAfricaMap.creditsPrefix(),
                    style: { fontSize: '10px', color: '#94a3b8' }
                },
                mapNavigation: { enabled: true },
                colorAxis: {
                    min: mapPayload.min,
                    max: mapPayload.max,
                    minColor: auColors.light,
                    maxColor: auColors.green
                },
                legend: { enabled: false },
                series: [{
                    type: 'map',
                    name: 'Members & localities',
                    mapData: KhAfricaMap.seriesMapData(mapAsset),
                    data: seriesData,
                    joinBy: joinBy,
                    dataLabels: KhAfricaMap.dataLabels(),
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            var p = this.point;
                            return '<b>' + (p.country_name || p.name || '') + '</b><br/>'
                                + (p.display_value || p.value)
                                + '<br/><span style="color:#1A5632;font-size:11px">Click for unit profile</span>';
                        }
                    },
                    point: {
                        events: {
                            click: function () {
                                if (this.detail_url) {
                                    window.location.href = this.detail_url;
                                }
                            }
                        }
                    }
                }]
            });
        }).catch(function () {
            container.innerHTML = '<div class="text-muted text-center p-4">Map could not be loaded.</div>';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('adminUnitsMapChart');
        if (!container) return;

        fetch(mapDataUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (payload) {
                if (!payload.points || !payload.points.length) {
                    container.innerHTML = '<div class="text-muted text-center p-4">Add ISO alpha-3 codes to administrative units to display the map.</div>';
                    return;
                }
                renderMap(payload);
            })
            .catch(function () {
                container.innerHTML = '<div class="text-muted text-center p-4">Map data unavailable.</div>';
            });
    });
})();
</script>
@endif
