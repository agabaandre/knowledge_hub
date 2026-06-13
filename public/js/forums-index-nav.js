/**
 * React navigation for /forums — search, filters, counts, jump links (CDN, no build).
 */
(function () {
    if (!window.React || !window.ReactDOM || !window.ForumsIndexFilters) {
        return;
    }

    var useState = React.useState;
    var useEffect = React.useEffect;
    var useRef = React.useRef;
    var useCallback = React.useCallback;

    var FILTERS = [
        { id: 'all', label: 'All Discussions' },
        { id: 'joined', label: 'My Discussions' },
        { id: 'recent', label: 'Most Recent' },
        { id: 'popular', label: 'Most Active' }
    ];

    var JUMPS = [
        { id: 'khub-forums-ai-banner', label: 'Khub AI' },
        { id: 'forums-list', label: 'Discussions' }
    ];

    function scrollToId(id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function ForumsNavApp(props) {
        var config = props.config || {};
        var urlState = ForumsIndexFilters.readUrlState();
        var debounceRef = useRef(null);

        var initialFilter = ['all', 'joined', 'recent', 'popular'].indexOf(urlState.filter) !== -1
            ? urlState.filter
            : 'all';

        var filterState = useState(initialFilter);
        var activeFilter = filterState[0];
        var setActiveFilter = filterState[1];

        var searchState = useState(urlState.searchTerm || '');
        var searchTerm = searchState[0];
        var setSearchTerm = searchState[1];

        var statsState = useState({ visible: 0, total: config.totalForums || 0 });
        var stats = statsState[0];
        var setStats = statsState[1];

        var applyNow = useCallback(function (filter, term) {
            var result = ForumsIndexFilters.apply(filter, term, true);
            setStats({ visible: result.visible, total: result.total });
        }, []);

        useEffect(function () {
            ForumsIndexFilters.init({
                initialFilter: activeFilter,
                initialSearch: searchTerm,
                onChange: function (result) {
                    setStats({ visible: result.visible, total: result.total });
                }
            });

            function onExternalChange(e) {
                var detail = (e && e.detail) || {};
                var nextFilter = detail.filter || 'all';
                var nextSearch = detail.searchTerm || '';
                setActiveFilter(nextFilter);
                setSearchTerm(nextSearch);
            }

            window.addEventListener('forumsIndexFiltersChanged', onExternalChange);

            return function () {
                window.removeEventListener('forumsIndexFiltersChanged', onExternalChange);
            };
        }, []);

        useEffect(function () {
            if (debounceRef.current) {
                clearTimeout(debounceRef.current);
            }
            debounceRef.current = setTimeout(function () {
                applyNow(activeFilter, searchTerm);
            }, config.searchDebounceMs || 220);

            return function () {
                if (debounceRef.current) {
                    clearTimeout(debounceRef.current);
                }
            };
        }, [searchTerm, activeFilter, applyNow]);

        function onFilterClick(filterId) {
            setActiveFilter(filterId);
        }

        function statusText() {
            if (stats.total === 0) {
                return 'No discussions on this page';
            }
            if (stats.visible === stats.total) {
                return 'Showing all ' + stats.total + ' discussion' + (stats.total === 1 ? '' : 's') + ' on this page';
            }
            if (stats.visible === 0) {
                return 'No discussions match your search or filter';
            }
            return 'Showing ' + stats.visible + ' of ' + stats.total + ' discussions on this page';
        }

        return React.createElement(
            'div',
            { className: 'forums-nav-react' },
            React.createElement(
                'nav',
                {
                    className: 'forums-nav-jumps',
                    'aria-label': 'Forums page sections'
                },
                JUMPS.map(function (jump) {
                    return React.createElement(
                        'button',
                        {
                            key: jump.id,
                            type: 'button',
                            className: 'forums-nav-jump-btn',
                            onClick: function () { scrollToId(jump.id); }
                        },
                        jump.label
                    );
                })
            ),
            React.createElement(
                'div',
                { className: 'forums-filters forums-filters--react' },
                React.createElement(
                    'div',
                    { className: 'search-bar' },
                    React.createElement('i', { className: 'fa fa-search search-icon', 'aria-hidden': 'true' }),
                    React.createElement('input', {
                        type: 'search',
                        id: 'forum-search',
                        value: searchTerm,
                        placeholder: config.searchPlaceholder || 'Search discussions by title, description, or tags...',
                        'aria-label': 'Search discussions',
                        autoComplete: 'off',
                        onChange: function (e) { setSearchTerm(e.target.value); }
                    })
                ),
                React.createElement(
                    'div',
                    { className: 'filter-buttons', role: 'tablist', 'aria-label': 'Discussion filters' },
                    FILTERS.map(function (filter) {
                        var isActive = activeFilter === filter.id;
                        return React.createElement(
                            'button',
                            {
                                key: filter.id,
                                type: 'button',
                                role: 'tab',
                                className: 'filter-btn' + (isActive ? ' active' : ''),
                                'aria-selected': isActive ? 'true' : 'false',
                                onClick: function () { onFilterClick(filter.id); }
                            },
                            filter.label
                        );
                    })
                ),
                React.createElement(
                    'p',
                    {
                        className: 'forums-nav-status',
                        'aria-live': 'polite',
                        'aria-atomic': 'true'
                    },
                    statusText()
                )
            )
        );
    }

    function mountForumsNav() {
        var rootEl = document.getElementById('forums-nav-root');
        var configEl = document.getElementById('forums-nav-config');
        if (!rootEl || !configEl) {
            return;
        }

        var config = {};
        try {
            config = JSON.parse(configEl.textContent || '{}');
        } catch (e) {
            config = {};
        }

        var root = ReactDOM.createRoot(rootEl);
        root.render(React.createElement(ForumsNavApp, { config: config }));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountForumsNav);
    } else {
        mountForumsNav();
    }
})();
