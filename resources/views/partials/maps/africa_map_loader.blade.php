{{-- Shared Africa map loader for Highcharts choropleths. Requires Highcharts map module. --}}
<script>
(function (global) {
    if (global.KhAfricaMap && global.KhAfricaMap._ready) {
        return;
    }

    var settings = global.__khAfricaMapSettings || {};
    var loadPromise = null;
    var topologyCache = null;

    function dataLabels() {
        return {
            enabled: true,
            format: '{point.name}',
            style: {
                fontSize: '9px',
                fontWeight: 'bold',
                color: '#1e293b',
                textOutline: '2px #ffffff'
            },
            backgroundColor: 'rgba(255,255,255,0.82)',
            borderRadius: 3,
            padding: 2
        };
    }

    function waitForScriptMap(timeoutMs) {
        timeoutMs = timeoutMs || 10000;
        return new Promise(function (resolve, reject) {
            var deadline = Date.now() + timeoutMs;
            (function poll() {
                if (global.Highcharts && global.Highcharts.maps && settings.key && global.Highcharts.maps[settings.key]) {
                    resolve(global.Highcharts.maps[settings.key]);
                    return;
                }
                if (Date.now() > deadline) {
                    reject(new Error('Africa map data unavailable'));
                    return;
                }
                setTimeout(poll, 50);
            })();
        });
    }

    function loadScriptMap() {
        if (global.Highcharts && global.Highcharts.maps && settings.key && global.Highcharts.maps[settings.key]) {
            return Promise.resolve(global.Highcharts.maps[settings.key]);
        }
        if (loadPromise) {
            return loadPromise;
        }
        loadPromise = new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[data-kh-africa-map="1"]');
            if (existing) {
                waitForScriptMap().then(resolve).catch(reject);
                return;
            }
            var s = document.createElement('script');
            s.src = settings.scriptUrl;
            s.setAttribute('data-kh-africa-map', '1');
            s.onload = function () { waitForScriptMap().then(resolve).catch(reject); };
            s.onerror = function () {
                loadPromise = null;
                reject(new Error('Africa map script failed to load'));
            };
            document.head.appendChild(s);
        });
        return loadPromise;
    }

    function loadTopologyMap() {
        if (topologyCache) {
            return Promise.resolve(topologyCache);
        }
        loadPromise = fetch(settings.topologyUrl, { mode: 'cors' })
            .then(function (r) {
                if (!r.ok) throw new Error('Map topology unavailable');
                return r.json();
            })
            .then(function (topology) {
                topologyCache = topology;
                return topology;
            })
            .catch(function (err) {
                loadPromise = null;
                throw err;
            });
        return loadPromise;
    }

    function load() {
        if (settings.type === 'topojson_url') {
            return loadTopologyMap();
        }
        return loadScriptMap();
    }

    function joinKey() {
        return settings.joinBy || 'iso-a3';
    }

    function mapPoint(row) {
        row = row || {};
        var point = { value: row.value };
        if (row.name) point.country_name = row.name;
        if (row.country_name) point.country_name = row.country_name;
        if (row.display_value) point.display_value = row.display_value;
        if (row.period) point.period = row.period;
        if (row.detail_url) point.detail_url = row.detail_url;
        if (row.country_id) point.country_id = row.country_id;
        if (joinKey() === 'hc-key' && row['hc-key']) {
            point['hc-key'] = row['hc-key'];
        } else if (row['iso-a3']) {
            point['iso-a3'] = row['iso-a3'];
        } else if (row['hc-key']) {
            point['hc-key'] = row['hc-key'];
        }
        return point;
    }

    function chartMapOption(mapData) {
        if (settings.type === 'topojson_url') {
            return mapData;
        }
        return settings.key;
    }

    function seriesMapData(mapData) {
        if (settings.type === 'topojson_url') {
            return undefined;
        }
        return mapData;
    }

    function creditsPrefix() {
        return 'Map © Natural Earth (v' + (settings.version || '1.0') + ')';
    }

    global.KhAfricaMap = {
        _ready: true,
        settings: settings,
        load: load,
        dataLabels: dataLabels,
        joinKey: joinKey,
        mapPoint: mapPoint,
        chartMapOption: chartMapOption,
        seriesMapData: seriesMapData,
        creditsPrefix: creditsPrefix
    };
})(window);
</script>
