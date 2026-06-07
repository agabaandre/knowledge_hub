@include('partials.maps.africa_map_config', ['mapContext' => 'frontend_countries'])
@include('partials.maps.africa_map_loader')
<script>
(function () {
    var mapDataUrl = @json(route('countries.map-data'));
    var indicatorSummariesUrl = @json(route('countries.indicator-summaries'));
    var countryDetailUrls = @json(
        collect($countries ?? [])->mapWithKeys(fn ($country) => [(string) $country->id => country_detail_url($country)])->all()
    );
    var regions = @json($regions_json ?? []);
    var auColors = {
        green: '{{ settings()->au_corporate_green ?? '#1A5632' }}',
        gold: '{{ settings()->au_gold ?? '#B4A269' }}',
        red: '{{ settings()->au_red ?? '#9F2241' }}',
        light: '#f0f7f4',
        grey: '#94a3b8'
    };

    var mapChart = null;
    var currentRegionId = null;
    var currentKpiId = parseInt(document.getElementById('mapIndicatorSelect')?.value || '0', 10);

    function formatLegendValue(value, mapPayload) {
        if (value === null || value === undefined || !isFinite(value)) {
            return '—';
        }
        var agg = mapPayload && mapPayload.aggregate;
        if (agg && agg.value_with_unit) {
            return agg.value_with_unit;
        }
        return Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 });
    }

    function updateScopeBanner(mapPayload) {
        var el = document.getElementById('mapScopeSummary');
        if (!el || !mapPayload) return;

        var scopeName = 'Africa (all member states)';
        if (currentRegionId) {
            var region = regions.find(function (r) { return r.id === currentRegionId; });
            if (region) scopeName = region.name;
        }

        var agg = mapPayload.aggregate || {};
        var valueLine = agg.value || '—';
        var denom = agg.unit_plain || '';
        var aggLabel = mapPayload.aggregation_label || '';

        el.innerHTML = '<strong>' + scopeName + '</strong>'
            + '<span class="map-scope-summary__value">' + valueLine + (denom ? ' <em>' + denom + '</em>' : '') + '</span>'
            + '<span class="map-scope-summary__meta">' + aggLabel + ' · ' + mapPayload.country_count + ' countries</span>';
    }

    function updateLegend(mapPayload) {
        var minEl = document.getElementById('mapLegendMin');
        var maxEl = document.getElementById('mapLegendMax');
        var titleEl = document.getElementById('mapLegendTitle');
        var barEl = document.getElementById('mapLegendBar');
        if (!minEl || !maxEl) return;

        var min = mapPayload.min;
        var max = mapPayload.max;
        var agg = mapPayload.aggregate || {};

        if (titleEl) {
            titleEl.textContent = mapPayload.kpi_name || 'Indicator';
        }

        if (min === null || max === null) {
            minEl.textContent = 'No data';
            maxEl.textContent = '';
            if (barEl) barEl.style.opacity = '0.35';
            return;
        }

        if (barEl) barEl.style.opacity = '1';

        var sampleDisplay = function (val) {
            var fake = { value: String(val), unit_plain: agg.unit_plain || '', type: agg.type || 'other' };
            if (fake.type === 'percent') {
                return Number(val).toLocaleString(undefined, { maximumFractionDigits: 1 }) + '%';
            }
            if (agg.unit_plain) {
                return Number(val).toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' ' + agg.unit_plain;
            }
            return Number(val).toLocaleString(undefined, { maximumFractionDigits: 2 });
        };

        minEl.textContent = sampleDisplay(min);
        maxEl.textContent = sampleDisplay(max);
    }

    function setActiveIndicatorButton(kpiId) {
        document.querySelectorAll('.js-map-indicator-select').forEach(function (btn) {
            btn.classList.toggle('is-active', parseInt(btn.getAttribute('data-kpi-id'), 10) === kpiId);
        });
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function formatIndicatorValue(display) {
        display = display || {};
        if (display.type === 'percent') {
            return escapeHtml(String(display.value || '').replace(/%$/, '')) + '%';
        }
        return escapeHtml(display.value || '—');
    }

    function buildIndicatorCardHtml(indicator) {
        var display = indicator.display || {};
        var denom = display.unit_plain ? '<div class="continental-indicator-card__denom">' + escapeHtml(display.unit_plain) + '</div>' : '';
        return '<button type="button" class="continental-indicator-card js-map-indicator-select"'
            + ' data-kpi-id="' + escapeHtml(indicator.kpi_id) + '"'
            + ' data-kpi-name="' + escapeHtml(indicator.name) + '">'
            + '<div class="continental-indicator-card__head">'
            + '<span class="continental-indicator-card__subject">' + escapeHtml(indicator.subject_area || 'Other indicators') + '</span>'
            + '<span class="continental-indicator-card__agg">' + escapeHtml(indicator.aggregation_label || '') + '</span>'
            + '</div>'
            + '<div class="continental-indicator-card__name">' + escapeHtml(indicator.name) + '</div>'
            + '<div class="continental-indicator-card__value">' + formatIndicatorValue(display) + '</div>'
            + denom
            + '<div class="continental-indicator-card__meta text-muted">'
            + escapeHtml(indicator.country_count) + ' member states with data'
            + '</div></button>';
    }

    function bindIndicatorCardClicks() {
        document.querySelectorAll('.js-map-indicator-select').forEach(function (btn) {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', function () {
                setIndicator(this.getAttribute('data-kpi-id'));
                var mapEl = document.getElementById('countriesMapChart');
                if (mapEl) {
                    mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
    }

    function renderIndicatorSummaries(summaries, scope) {
        var listEl = document.getElementById('indicatorSummariesList');
        var titleTextEl = document.getElementById('indicatorSummariesTitleText');
        var subtitleEl = document.getElementById('indicatorSummariesSubtitle');
        if (!listEl) return;

        if (scope) {
            if (titleTextEl) titleTextEl.textContent = scope.title || 'Continental indicators';
            if (subtitleEl) subtitleEl.textContent = scope.subtitle || '';
        }

        if (!summaries || !summaries.length) {
            listEl.innerHTML = '<div class="text-muted text-center py-4" style="font-size:0.85rem;">No indicator data for this region.</div>';
            return;
        }

        listEl.innerHTML = summaries.map(buildIndicatorCardHtml).join('');
        bindIndicatorCardClicks();
        setActiveIndicatorButton(currentKpiId);
    }

    function fetchScopeSummaries(regionId) {
        var url = indicatorSummariesUrl + (regionId ? '?region_id=' + encodeURIComponent(regionId) : '');
        var listEl = document.getElementById('indicatorSummariesList');
        if (listEl) {
            listEl.innerHTML = '<div class="text-muted text-center py-4"><i class="fa fa-spinner fa-spin me-1"></i> Updating indicators…</div>';
        }
        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                renderIndicatorSummaries(json.summaries || [], json.scope || null);
            })
            .catch(function () {
                if (listEl) {
                    listEl.innerHTML = '<div class="text-muted text-center py-4">Could not load indicators for this region.</div>';
                }
            });
    }

    function setActiveRegionCard(regionId) {
        document.querySelectorAll('.region-card').forEach(function (card) {
            var id = parseInt(card.getAttribute('data-region-id'), 10);
            card.classList.toggle('map-region-active', regionId && id === regionId);
        });
    }

    function renderMap(mapPayload) {
        var container = document.getElementById('countriesMapChart');
        if (!container || typeof Highcharts === 'undefined' || !Highcharts.mapChart || !window.KhAfricaMap) {
            return;
        }

        KhAfricaMap.load().then(function (mapAsset) {
            if (mapChart) {
                mapChart.destroy();
                mapChart = null;
            }

            var joinBy = KhAfricaMap.joinKey();
            var seriesData = (mapPayload.points || []).map(function (p) {
                return KhAfricaMap.mapPoint({
                    'iso-a3': p['iso-a3'],
                    'hc-key': p['hc-key'],
                    value: p.value,
                    name: p.name,
                    country_id: p.country_id,
                    display_value: p.display_value,
                    period: p.period,
                    detail_url: p.detail_url
                });
            });

            mapChart = Highcharts.mapChart('countriesMapChart', {
                chart: {
                    map: KhAfricaMap.chartMapOption(mapAsset),
                    backgroundColor: '#f8f9fa',
                    height: 520,
                    style: { fontFamily: 'inherit' }
                },
                title: { text: null },
                credits: {
                    enabled: true,
                    text: KhAfricaMap.creditsPrefix() + ' · Data: Our World in Data (CC BY 4.0)',
                    style: { fontSize: '10px', color: '#94a3b8' }
                },
                mapNavigation: {
                    enabled: true,
                    buttonOptions: { verticalAlign: 'bottom', align: 'right' }
                },
                colorAxis: {
                    min: mapPayload.min,
                    max: mapPayload.max,
                    minColor: auColors.light,
                    maxColor: auColors.green,
                    labels: { style: { color: '#475569', fontSize: '10px' } }
                },
                legend: { enabled: false },
                plotOptions: {
                    map: {
                        nullColor: '#f1f5f9',
                        borderColor: '#ffffff',
                        borderWidth: 0.5,
                        states: {
                            hover: {
                                color: auColors.gold,
                                borderColor: auColors.red,
                                borderWidth: 1.2
                            }
                        }
                    }
                },
                series: [{
                    type: 'map',
                    name: mapPayload.kpi_name || 'Indicator',
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
                                + '<br/><span style="color:#64748b">Period: ' + (p.period || '—') + '</span>'
                                + '<br/><span style="color:#1A5632;font-size:11px">Click for country profile</span>';
                        }
                    },
                    point: {
                        events: {
                            click: function () {
                                var url = this.detail_url
                                    || countryDetailUrls[String(this.country_id)]
                                    || null;
                                if (url) window.location.href = url;
                            }
                        }
                    }
                }]
            });

            updateLegend(mapPayload);
            updateScopeBanner(mapPayload);
        }).catch(function () {
            container.innerHTML = '<div class="text-muted text-center p-4">Map could not be loaded. Please refresh and try again.</div>';
        });
    }

    function fetchMapData(kpiId, regionId) {
        var container = document.getElementById('countriesMapChart');
        if (container) {
            container.innerHTML = '<div class="map-loading text-center p-5 text-muted"><i class="fa fa-spinner fa-spin me-2"></i>Loading map…</div>';
        }

        var url = mapDataUrl + '?kpi_id=' + encodeURIComponent(kpiId)
            + (regionId ? '&region_id=' + encodeURIComponent(regionId) : '');

        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                var mapPayload = json.map || json;
                renderMap(mapPayload);
                return json;
            });
    }

    function setRegion(regionId) {
        currentRegionId = regionId || null;
        setActiveRegionCard(currentRegionId);
        var scopeLabel = document.getElementById('mapScopeLabel');
        if (scopeLabel) {
            if (!currentRegionId) {
                scopeLabel.textContent = 'All Africa';
            } else {
                var region = regions.find(function (r) { return r.id === currentRegionId; });
                scopeLabel.textContent = region ? region.name : 'Region';
            }
        }
        fetchScopeSummaries(currentRegionId);
        if (currentKpiId > 0) {
            fetchMapData(currentKpiId, currentRegionId);
        }
    }

    function setIndicator(kpiId) {
        currentKpiId = parseInt(kpiId, 10);
        var select = document.getElementById('mapIndicatorSelect');
        if (select) select.value = String(currentKpiId);
        setActiveIndicatorButton(currentKpiId);
        if (currentKpiId > 0) {
            fetchMapData(currentKpiId, currentRegionId);
        }
    }

    window.CountriesMap = { setRegion: setRegion, setIndicator: setIndicator };

    function init() {
        var select = document.getElementById('mapIndicatorSelect');
        if (select) {
            select.addEventListener('change', function () {
                setIndicator(this.value);
            });
        }

        bindIndicatorCardClicks();

        document.querySelectorAll('.js-map-region-filter').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var regionId = parseInt(this.getAttribute('data-region-id'), 10);
                setRegion(regionId);
            });
        });

        var resetBtn = document.getElementById('mapResetRegion');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                setRegion(null);
            });
        }

        @if(!empty($initial_map_data))
        renderMap(@json($initial_map_data));
        setActiveIndicatorButton({{ (int) ($initial_map_data['kpi_id'] ?? 0) }});
        @endif
    }

    function loadHighchartsMaps(done) {
        if (typeof Highcharts !== 'undefined' && Highcharts.mapChart) {
            done();
            return;
        }
        var sources = [
            '{{ asset('assets/plugins/highcharts/highmaps.js') }}',
            'https://code.highcharts.com/maps/highmaps.js'
        ];
        var i = 0;
        function next() {
            if (typeof Highcharts !== 'undefined' && Highcharts.mapChart) {
                done();
                return;
            }
            if (i >= sources.length) {
                done();
                return;
            }
            var s = document.createElement('script');
            s.src = sources[i++];
            s.onload = next;
            s.onerror = next;
            document.head.appendChild(s);
        }
        next();
    }

    loadHighchartsMaps(init);
})();
</script>
