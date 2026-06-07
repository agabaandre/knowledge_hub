@include('partials.maps.africa_map_config', ['mapContext' => 'admin_rcc'])
@include('partials.maps.africa_map_loader')
<script>
(function () {
    var colors = window.__rccColors || {};
    var mapChart = null;
    var mainChart = null;
    var subjectCharts = [];
    var debounceTimer = null;
    var lastPayload = null;
    var chartsTabRendered = false;

    function ensureMapModule() {
        if (!window.KhAfricaMap || typeof KhAfricaMap.ensureMapModule !== 'function') {
            return Promise.reject(new Error('Map loader unavailable'));
        }
        return KhAfricaMap.ensureMapModule(window.__rccMapModuleUrl);
    }

    function isPublicationsMap(payload) {
        return !payload || parseInt(payload.kpi_id, 10) === 0;
    }

    function renderMap(mapPayload) {
        var container = document.getElementById('rccMapChart');
        if (!container || !mapPayload || !mapPayload.points || !mapPayload.points.length) {
            if (container) container.innerHTML = '<div class="rcc-empty">No map data for this selection.</div>';
            return;
        }
        if (!window.KhAfricaMap) {
            container.innerHTML = '<div class="rcc-empty">Map loader unavailable.</div>';
            return;
        }
        container.innerHTML = '';
        ensureMapModule().then(function () {
            return KhAfricaMap.load();
        }).then(function (mapAsset) {
            if (mapChart) { mapChart.destroy(); mapChart = null; }
            var mapData = mapPayload.points.map(function (p) {
                return KhAfricaMap.mapPoint(p);
            });
            var mapSeriesName = mapPayload.kpi_name || 'Indicator value';
            mapChart = KhAfricaMap.renderHighcharts('rccMapChart', mapAsset, mapData, {
                min: mapPayload.min,
                max: mapPayload.max,
                height: 460,
                title: mapSeriesName,
                seriesName: mapSeriesName,
                credits: isPublicationsMap(mapPayload)
                    ? KhAfricaMap.creditsPrefix() + ' · Knowledge Hub publications'
                    : KhAfricaMap.creditsPrefix() + ' · OWID (CC BY 4.0)',
                tooltip: {
                    useHTML: true,
                    formatter: function () {
                        var p = this.point;
                        var html = '<b>' + escapeHtml(p.country_name || p.name) + '</b><br/>' + escapeHtml(p.display_value || p.value);
                        if (!isPublicationsMap(mapPayload) && p.period) {
                            html += '<br/><span style="color:#64748b">Period: ' + escapeHtml(p.period) + '</span>';
                        }
                        return html;
                    }
                }
            });
        }).catch(function () {
            container.innerHTML = '<div class="rcc-empty">Map could not be loaded.</div>';
        });
    }

    function filterParams() {
        var form = document.getElementById('rccFilterForm');
        if (!form) return {};
        var data = {};
        if (window.jQuery) {
            jQuery(form).find('select, input').each(function () {
                var name = this.name;
                if (!name) return;
                var val = jQuery(this).val();
                if (val !== null && val !== '' && val !== undefined) {
                    data[name] = val;
                }
            });
            return data;
        }
        new FormData(form).forEach(function (value, key) {
            if (value !== '') data[key] = value;
        });
        return data;
    }

    function syncFilterUrl() {
        var params = new URLSearchParams(filterParams());
        var qs = params.toString();
        var url = window.location.pathname + (qs ? '?' + qs : '');
        window.history.replaceState({}, '', url);
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

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function reflowVisibleCharts(targetId) {
        setTimeout(function () {
            if (targetId === '#rccTabCharts') {
                if (mainChart && mainChart.reflow) mainChart.reflow();
                subjectCharts.forEach(function (c) {
                    if (c && c.reflow) c.reflow();
                });
            }
            if (targetId === '#rccTabOverview' && mapChart && mapChart.reflow) {
                mapChart.reflow();
            }
        }, 60);
    }

    function initTabs() {
        var tabList = document.getElementById('rccTabs');
        if (!tabList) return;

        function activateTab(link) {
            var targetId = link.getAttribute('href');
            if (!targetId || targetId.charAt(0) !== '#') return;

            tabList.querySelectorAll('.nav-link').forEach(function (el) {
                el.classList.remove('active');
                el.setAttribute('aria-selected', 'false');
            });
            document.querySelectorAll('#rccTabs + .tab-content .tab-pane, .rcc-panel__body > .tab-content .tab-pane').forEach(function (pane) {
                pane.classList.remove('show', 'active');
            });

            link.classList.add('active');
            link.setAttribute('aria-selected', 'true');
            var pane = document.querySelector(targetId);
            if (pane) {
                pane.classList.add('show', 'active');
            }

            if (targetId === '#rccTabCharts' && lastPayload) {
                renderChartsTab(lastPayload, true);
            }
            reflowVisibleCharts(targetId);
        }

        tabList.querySelectorAll('a[role="tab"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                activateTab(link);
            });
        });

        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            tabList.querySelectorAll('a[role="tab"]').forEach(function (link) {
                link.addEventListener('shown.bs.tab', function () {
                    var targetId = link.getAttribute('href');
                    if (targetId === '#rccTabCharts' && lastPayload) {
                        renderChartsTab(lastPayload, true);
                    }
                    reflowVisibleCharts(targetId);
                });
            });
        }
    }

    function renderScopeBanner(payload) {
        var el = document.getElementById('rccScopeBanner');
        if (!el) return;
        var map = payload.map || {};
        var meta = payload.meta || {};
        var scopeName = 'Africa (all member states)';
        if (meta.country_name) {
            scopeName = meta.country_name;
        } else if (meta.region_name) {
            scopeName = meta.region_name;
        } else if (meta.region_id && window.__rccRegions) {
            var region = window.__rccRegions.find(function (r) { return String(r.id) === String(meta.region_id); });
            if (region) scopeName = region.name;
        }
        var agg = map.aggregate || {};
        el.innerHTML = '<strong>' + escapeHtml(map.kpi_name || 'Indicator') + '</strong> · ' + escapeHtml(scopeName)
            + '<span style="font-size:1.1rem;font-weight:700;color:' + colors.red + ';margin-left:0.5rem">'
            + escapeHtml(agg.value_with_unit || agg.value || '—') + '</span>'
            + '<span class="text-muted d-block mt-1" style="font-size:0.8rem">'
            + escapeHtml(map.aggregation_label || '') + ' · ' + (map.country_count || 0) + ' countries · Year '
            + escapeHtml(meta.period_year || '') + '</span>';
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
                + escapeHtml(item.name.substring(0, 36)) + ': <strong>' + escapeHtml(val) + '</strong></button>';
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
            html += '<h4 class="rcc-subject-title">' + escapeHtml(group.subject_area_name) + '</h4><div class="rcc-kpi-grid">';
            (group.items || []).slice(0, 12).forEach(function (item) {
                var d = item.display || {};
                var prev = item.prev_display || {};
                html += '<div class="rcc-kpi-card">'
                    + '<div class="rcc-kpi-card__name">' + escapeHtml(item.kpi_name) + '</div>'
                    + '<div class="rcc-kpi-card__value">' + escapeHtml(d.value_with_unit || d.value || '—') + '</div>'
                    + (d.unit_plain ? '<div class="rcc-kpi-card__unit">' + escapeHtml(d.unit_plain) + '</div>' : '')
                    + '<div class="rcc-kpi-card__yoy">vs ' + (year - 1) + ': ' + escapeHtml(prev.value_with_unit || prev.value || '—') + '</div>'
                    + '</div>';
            });
            html += '</div>';
        });
        el.innerHTML = html;
    }

    function buildChartSeries(chartPayload) {
        return (chartPayload.data || []).map(function (s, i) {
            return {
                name: s.name || ('Indicator ' + (i + 1)),
                data: s.data || [],
                color: colors.palette[i % colors.palette.length]
            };
        });
    }

    function renderMainChart(chartPayload, deferReflow) {
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

        var series = buildChartSeries(chartPayload);
        if (mainChart) { mainChart.destroy(); mainChart = null; }
        mainChart = Highcharts.chart('rccMainChart', {
            chart: { type: chartType, backgroundColor: 'transparent', height: 420 },
            title: { text: chartPayload.title || 'Indicator analysis', style: { fontSize: '14px', color: '#64748b' } },
            credits: { enabled: false },
            colors: colors.palette,
            xAxis: {
                categories: chartPayload.labels || [],
                labels: { style: { fontSize: '10px', color: '#64748b' }, rotation: (chartPayload.labels || []).length > 12 ? -45 : 0 }
            },
            yAxis: {
                title: { text: chartPayload.y_axis_title || 'Value', style: { color: '#64748b' } },
                gridLineColor: '#e2e8f0'
            },
            legend: { enabled: series.length > 1, itemStyle: { fontSize: '11px' } },
            tooltip: {
                shared: true,
                valueDecimals: 2,
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>'
            },
            plotOptions: {
                column: { borderRadius: 4, dataLabels: { enabled: false } },
                bar: { borderRadius: 4, dataLabels: { enabled: false } },
                series: { animation: { duration: 500 } }
            },
            series: series
        });
        if (!deferReflow && mainChart.reflow) mainChart.reflow();
    }

    function destroySubjectCharts() {
        subjectCharts.forEach(function (c) {
            if (c && c.destroy) c.destroy();
        });
        subjectCharts = [];
    }

    function renderSubjectCharts(subjectChartsPayload) {
        var container = document.getElementById('rccSubjectCharts');
        if (!container) return;
        destroySubjectCharts();
        if (!subjectChartsPayload || !subjectChartsPayload.length) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = subjectChartsPayload.map(function (chart, idx) {
            return '<div class="rcc-subject-chart" id="rccSubjectChart' + idx + '"></div>';
        }).join('');

        subjectChartsPayload.forEach(function (chartDef, idx) {
            var chart = Highcharts.chart('rccSubjectChart' + idx, {
                chart: { type: 'column', backgroundColor: 'transparent', height: 300 },
                title: { text: chartDef.title || chartDef.subject_area_name, style: { fontSize: '12px', color: '#64748b' } },
                credits: { enabled: false },
                colors: [colors.green],
                xAxis: {
                    categories: chartDef.labels || [],
                    labels: { style: { fontSize: '9px', color: '#64748b' }, rotation: (chartDef.labels || []).length > 6 ? -35 : 0 }
                },
                yAxis: { title: { text: chartDef.series_name || 'Value', style: { fontSize: '10px', color: '#94a3b8' } }, gridLineColor: '#e2e8f0' },
                legend: { enabled: false },
                tooltip: { valueDecimals: 2 },
                plotOptions: { column: { borderRadius: 4 } },
                series: [{ name: chartDef.series_name || 'Latest value', data: chartDef.data || [] }]
            });
            subjectCharts.push(chart);
        });
    }

    function isChartsTabActive() {
        var pane = document.getElementById('rccTabCharts');
        return pane && pane.classList.contains('active');
    }

    function renderChartsTab(payload, force) {
        if (!force && !isChartsTabActive()) {
            chartsTabRendered = false;
            return;
        }
        renderMainChart(payload.chart, false);
        renderSubjectCharts(payload.subject_charts);
        chartsTabRendered = true;
        reflowVisibleCharts('#rccTabCharts');
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
                + '<td>' + escapeHtml(row.country_name) + '</td>'
                + '<td>' + escapeHtml(row.kpi_name) + '</td>'
                + '<td>' + escapeHtml(row.period) + '</td>'
                + '<td><strong>' + escapeHtml(row.display_value) + '</strong></td>'
                + '</tr>';
        }).join('');
    }

    function syncYearSelectFromPayload(payload) {
        var meta = payload && payload.meta ? payload.meta : {};
        if (!meta.period_year) return;
        var yearSelect = document.getElementById('rccYear');
        if (!yearSelect) return;
        var year = String(meta.period_year);
        if (yearSelect.value !== year) {
            yearSelect.value = year;
            if (window.jQuery) {
                jQuery(yearSelect).trigger('change.select2');
            }
        }
    }

    function applyPayload(payload) {
        lastPayload = payload;
        syncYearSelectFromPayload(payload);
        syncFilterUrl();
        renderScopeBanner(payload);
        renderSummaryChips(payload.indicator_summaries);
        renderKpiCards(payload.subject_groups, payload.meta?.period_year || new Date().getFullYear());
        renderMap(payload.map);
        renderChartsTab(payload, isChartsTabActive() || chartsTabRendered);
        renderTable(payload.table);
    }

    function refreshDashboard() {
        chartsTabRendered = false;
        var mapEl = document.getElementById('rccMapChart');
        if (mapEl) mapEl.innerHTML = '<div class="rcc-empty"><i class="fa fa-spinner fa-spin"></i> Loading…</div>';
        return fetchPayload().then(function (payload) {
            applyPayload(payload);
        }).catch(function () {
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

    function bindFilterHandlers() {
        var form = document.getElementById('rccFilterForm');
        if (!form) return;
        var onFilterChange = function (sel) {
            if (sel.id === 'rccRegion') filterCountryOptions();
            if (sel.id === 'rccSubject') filterIndicatorOptions();
            scheduleRefresh();
        };
        if (window.jQuery) {
            jQuery(form).find('select').off('change.rcc').on('change.rcc', function () {
                onFilterChange(this);
            });
            return;
        }
        form.querySelectorAll('select').forEach(function (sel) {
            sel.addEventListener('change', function () { onFilterChange(sel); });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        bindFilterHandlers();

        filterCountryOptions();
        filterIndicatorOptions();

        var mainChartEl = document.getElementById('rccMainChart');
        if (mainChartEl) {
            mainChartEl.innerHTML = '<div class="rcc-empty"><i class="fa fa-spinner fa-spin me-2"></i>Loading chart…</div>';
        }

        if (window.__rccInitialPayload) {
            applyPayload(window.__rccInitialPayload);
        } else {
            refreshDashboard();
        }
    });
})();
</script>
