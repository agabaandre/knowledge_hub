/**
 * Async records search bootstrap (React 18 from CDN).
 * Fetches search results and AI insights in parallel after the shell renders.
 */
(function () {
    if (!window.React || !window.ReactDOM) {
        return;
    }

    var useEffect = React.useEffect;
    var useRef = React.useRef;

    function RecordsSearchAsyncApp(props) {
        var config = props.config || {};
        var started = useRef(false);

        useEffect(function () {
            if (started.current) {
                return;
            }
            started.current = true;

            var mainEl = document.getElementById('records-search-main');
            var configEl = document.getElementById('records-search-async-config');
            if (!mainEl || !configEl) {
                return;
            }

            var params = new URLSearchParams(window.location.search);
            if (typeof window.normalizeRecordsSearchFacetParams === 'function') {
                window.normalizeRecordsSearchFacetParams(params);
            }

            var qs = params.toString();
            var fragmentUrl = (config.fragmentUrl || '') + (qs ? '?' + qs : '');
            var aiMount = document.getElementById('khub-search-ai-mount');

            mainEl.classList.add('is-loading');

            var fragmentPromise = fetch(fragmentUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).then(function (r) {
                if (!r.ok) {
                    throw new Error('fragment failed');
                }
                return r.json();
            });

            var aiPromise = (config.aiEnabled && config.aiInsightsUrl && typeof window.loadAiInsightsFragment === 'function')
                ? window.loadAiInsightsFragment(params, { aiMount: aiMount })
                : Promise.resolve(null);

            Promise.all([fragmentPromise, aiPromise])
                .then(function (results) {
                    if (typeof window.applyRecordsSearchFragment === 'function') {
                        window.applyRecordsSearchFragment(results[0], { mainEl: mainEl });
                    }
                })
                .catch(function () {
                    window.location.reload();
                })
                .finally(function () {
                    mainEl.classList.remove('is-loading');
                });
        }, [config]);

        return null;
    }

    function mountRecordsSearchAsync() {
        var rootEl = document.getElementById('records-search-async-root');
        var configEl = document.getElementById('records-search-async-config');
        if (!rootEl || !configEl) {
            return;
        }

        var config = {};
        try {
            config = JSON.parse(configEl.textContent || '{}');
        } catch (e) {
            return;
        }

        var root = ReactDOM.createRoot(rootEl);
        root.render(React.createElement(RecordsSearchAsyncApp, { config: config }));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountRecordsSearchAsync);
    } else {
        mountRecordsSearchAsync();
    }
})();
