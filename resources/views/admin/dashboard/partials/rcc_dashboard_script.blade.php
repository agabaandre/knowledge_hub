<script>
(function () {
    var colors = window.__rccColors || {};
    var mapChart = null;
    var mainChart = null;
    var topologyCache = null;
    var debounceTimer = null;

    function ensureMapModule() {
        return new Promise(function (resolve, reject) {
            if (typeof Highcharts !== 'undefined' && Highcharts.mapChart) {
                resolve();
                return;
            }
            if (typeof Highcharts === 'undefined') {
                reject(new Error('Highcharts not loaded'));
                return;
            }
            var s = document.createElement('script');
            s.src = window.__rccMapModuleUrl;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function loadTopology() {
        if (topologyCache) return Promise.resolve(topologyCache);
        return fetch(window.__rccMapTopologyUrl, { mode: 'cors' })
            .then(function (r) { return r.json(); })
            .then(function (t) { topologyCache = t; return t; });
    }

    function filterParams() {
        var form = document.getElementById('rccFilterForm');
        if (!form) return {};
        var data = {};
        new FormData(form).forEach(function (value, key) {
            if (value !== '') data[key] = value;
        });
        return data;
    }

    function fetchPayload() {
        var params = new URLSearchParams(filterParams());
        return fetch(window.__rccDataUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); });
    }

    function scheduleRefresh() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(refreshDashboard, 350);
    }

    function renderScopeBanner(payload) {
        var el = document.getElementById('rccScopeBanner');
        if (!el) return;
        var map = payload.map || {};
        var regionId = document.getElementById('rccRegion')?.value;
        var scopeName = 'Africa (all member states)';
        if (regionId && window.__rccRegions) {
            var region = window.__rccRegions.find(function (r) { return String(r.id) === String(regionId); });
            if (region) scopeName = region.name;
        }
        var agg = map.aggregate || {};
        el.innerHTML = '<strong>' + (map.kpi_name || 'Indicator') + '</strong> · ' + scopeName
            + '<span style="font-size:1.1rem;font-weight:700;color:' + colors.red + ';margin-left:0.5rem">'
            + (agg.value_with_unit || agg.value || '—') + '</span>'
            + '<span class="text-muted d-block mt-1" style="font-size:0.8rem">'
            + (map.aggregation_label || '') + ' · ' + (map.country_count || 0) + ' countries · Year '
            + (payload.meta?.period_year || '') + '</span>';
    }

    function renderSummaryChips(summaries) {
        var el = document.getElementById('rccSummaryChips');
        if (!el) return;
        if (!summaries || !summaries.length) {
            el.innerHTML = '';
            return;
        }
        el.innerHTML = summaries.slice(0, 14).map(function (item) {
            var d = item.display || {};
            var val = d.type === 'percent' ? (d.value + '%') : (d.value_with_unit || d.value || '—');
            return '<button type="button" class="rcc-summary-chip js-rcc-kpi-chip" data-kpi-id="' + item.kpi_id + '">'
                + item.name.substring(0, 36) + ': <strong>' + val + '</strong></button>';
        }).join('');
        el.querySelectorAll('.js-rcc-kpi-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var kpiId = this.getAttribute('data-kpi-id');
                $('#rccIndicator').val(kpiId).trigger('change');
            });
        });
    }

    function renderKpiCards(groups, year) {
        var el = document.getElementById('rccKpiCards');
        if (!el) return;
        if (!groups || !groups.length) {
            el.innerHTML = '<div class="rcc-empty"><i class="fa fa-info-circle me-1"></i> No published indicator data for the current filters.</div>';
            return;
        }
        var html = '';
        groups.forEach(function (group) {
            html += '<h4 class="rcc-subject-title">' + group.subject_area_name + '</h4><div class="rcc-kpi-grid">';
            (group.items || []).slice(0, 12).forEach(function (item) {
                var d = item.display || {};
                var prev = item.prev_display || {};
                html += '<div class="rcc-kpi-card">'
                    + '<div class="rcc-kpi-card__name">' + item.kpi_name + '</div>'
                    + '<div class="rcc-kpi-card__value">' + (d.value_with_unit || d.value || '—') + '</div>'
                    + (d.unit_plain ? '<div class="rcc-kpi-card__unit">' + d.unit_plain + '</div>' : '')
                    + '<div class="rcc-kpi-card__yoy">vs ' + (year - 1) + ': ' + (prev.value_with_unit || prev.value || '—') + '</div>'
                    + '</div>';
            });
            html += '</div>';
        });
        el.innerHTML = html;
    }

    function renderMap(mapPayload) {
        var container = document.getElementById('rccMapChart');
        if (!container || !mapPayload || !mapPayload.points || !mapPayload.points.length) {
            if (container) container.innerHTML = '<div class="rcc-empty">No map data for this selection.</div>';
            return;
        }
        container.innerHTML = '';
        ensureMapModule().then(function () {
            return loadTopology();
        }).then(function (topology) {
            if (mapChart) { mapChart.destroy(); mapChart = null; }
            var mapData = mapPayload.points.map(function (p) {
                return { 'hc-key': p['hc-key'], value: p.value, name: p.name, display_value: p.display_value, period: p.period };
            });
            mapChart = Highcharts.mapChart('rccMapChart', {
                chart: { map: topology, backgroundColor: 'transparent', height: 460 },
                title: { text: null },
                credits: { enabled: true, text: 'Map © Natural Earth · OWID (CC BY 4.0)', style: { fontSize: '10px', color: '#94a3b8' } },
                mapNavigation: { enabled: true },
                mapView: { zoom: 2.8, center: [20, 2] },
                colorAxis: { min: mapPayload.min, max: mapPayload.max, minColor: '#f0f7f4', maxColor: colors.green },
                legend: { enabled: false },
                series: [{
                    type: 'map', data: mapData, joinBy: 'hc-key',
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            var p = this.point;
                            return '<b>' + p.name + '</b><br/>' + (p.display_value || p.value);
                        }
                    }
                }]
            });
        }).catch(function () {
            container.innerHTML = '<div class="rcc-empty">Map could not be loaded.</div>';
        });
    }

    function renderMainChart(chartPayload) {
        var container = document.getElementById('rccMainChart');
        if (!container) return;
        if (!chartPayload || !chartPayload.data || !chartPayload.data.length) {
            container.innerHTML = '<div class="rcc-empty">No chart data for the current filters.</div>';
            return;
        }
        container.innerHTML = '';
        var chartType = document.getElementById('rccChartType')?.value || 'column';
        if (chartType === 'bar' && chartPayload.mode === 'countries') chartType = 'bar';
        if (chartPayload.mode === 'timeline') chartType = chartType === 'column' ? 'line' : chartType;

        var series = chartPayload.data.map(function (s, i) {
            return {
                name: s.name,
                data: s.data,
                color: colors.palette[i % colors.palette.length]
            };
        });

        if (mainChart) { mainChart.destroy(); mainChart = null; }
        mainChart = Highcharts.chart('rccMainChart', {
            chart: { type: chartType, backgroundColor: 'transparent', height: 420 },
            title: { text: chartPayload.title || 'Indicator analysis', style: { fontSize: '14px', color: '#64748b' } },
            credits: { enabled: false },
            colors: colors.palette,
            xAxis: {
                categories: chartPayload.labels,
                labels: { style: { fontSize: '10px', color: '#64748b' }, rotation: chartPayload.labels.length > 12 ? -45 : 0 }
            },
            yAxis: { title: { text: null }, gridLineColor: '#e2e8f0' },
            legend: { enabled: series.length > 1 },
            tooltip: { shared: true, valueDecimals: 2 },
            plotOptions: {
                column: { borderRadius: 4, dataLabels: { enabled: false } },
                series: { animation: { duration: 500 } }
            },
            series: series
        });
    }

    function renderTable(rows) {
        var body = document.getElementById('rccDataTableBody');
        if (!body) return;
        if (!rows || !rows.length) {
            body.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No data rows match the current filters.</td></tr>';
            return;
        }
        body.innerHTML = rows.map(function (row) {
            return '<tr>'
                + '<td>' + row.country_name + '</td>'
                + '<td>' + row.kpi_name + '</td>'
                + '<td>' + row.period + '</td>'
                + '<td><strong>' + row.display_value + '</strong></td>'
                + '</tr>';
        }).join('');
    }

    function refreshDashboard() {
        var overview = document.getElementById('rccTabOverview');
        if (overview) {
            var mapEl = document.getElementById('rccMapChart');
            if (mapEl) mapEl.innerHTML = '<div class="rcc-empty"><i class="fa fa-spinner fa-spin"></i> Loading…</div>';
        }
        return fetchPayload().then(function (payload) {
            renderScopeBanner(payload);
            renderSummaryChips(payload.indicator_summaries);
            renderKpiCards(payload.subject_groups, payload.meta?.period_year || new Date().getFullYear());
            renderMap(payload.map);
            renderMainChart(payload.chart);
            renderTable(payload.table);
        }).catch(function () {
            var mapEl = document.getElementById('rccMapChart');
            if (mapEl) mapEl.innerHTML = '<div class="rcc-empty text-danger">Failed to load dashboard data.</div>';
        });
    }

    function filterCountryOptions() {
        var regionId = document.getElementById('rccRegion')?.value;
        var countrySelect = document.getElementById('rccCountry');
        if (!countrySelect) return;
        Array.from(countrySelect.options).forEach(function (opt, idx) {
            if (idx === 0) return;
            var show = !regionId || String(opt.getAttribute('data-region')) === String(regionId);
            opt.hidden = !show;
            opt.disabled = !show;
        });
        if (regionId && countrySelect.value) {
            var selected = countrySelect.options[countrySelect.selectedIndex];
            if (selected && selected.disabled) {
                countrySelect.value = '';
                if (window.jQuery) jQuery(countrySelect).trigger('change.select2');
            }
        }
    }

    function filterIndicatorOptions() {
        var subjectId = document.getElementById('rccSubject')?.value;
        var indicatorSelect = document.getElementById('rccIndicator');
        if (!indicatorSelect) return;
        Array.from(indicatorSelect.options).forEach(function (opt, idx) {
            if (idx === 0) return;
            var show = !subjectId || String(opt.getAttribute('data-subject')) === String(subjectId);
            opt.hidden = !show;
            opt.disabled = !show;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('.select2').select2({ width: '100%' });
        }

        var form = document.getElementById('rccFilterForm');
        if (form) {
            form.querySelectorAll('select').forEach(function (sel) {
                sel.addEventListener('change', function () {
                    if (sel.id === 'rccRegion') filterCountryOptions();
                    if (sel.id === 'rccSubject') filterIndicatorOptions();
                    scheduleRefresh();
                });
            });
        }

        filterCountryOptions();
        filterIndicatorOptions();

        if (window.__rccInitialPayload) {
            var p = window.__rccInitialPayload;
            renderScopeBanner(p);
            renderSummaryChips(p.indicator_summaries);
            renderKpiCards(p.subject_groups, p.meta?.period_year || new Date().getFullYear());
            renderMap(p.map);
            renderMainChart(p.chart);
            renderTable(p.table);
        } else {
            refreshDashboard();
        }
    });
})();
</script>
