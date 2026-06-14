/**
 * React navigation for /communities — unified search + membership filters (CDN, no build).
 */
(function () {
    if (!window.React || !window.ReactDOM || !window.CommunitiesIndexFilters) {
        return;
    }

    var useState = React.useState;
    var useEffect = React.useEffect;
    var useRef = React.useRef;
    var useCallback = React.useCallback;

    var FILTERS = [
        { id: 'all', label: 'All communities' },
        { id: 'joined', label: 'My communities' },
        { id: 'open', label: 'Open to all' }
    ];

    function scrollToId(id) {
        var el = document.getElementById(id);
        if (!el) {
            return;
        }
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function CommunitiesNavApp(props) {
        var config = props.config || {};
        var urlState = CommunitiesIndexFilters.readUrlState();
        var debounceRef = useRef(null);
        var skipSearchNavRef = useRef(true);
        var minChars = config.searchMinChars || 4;

        var initialFilter = ['all', 'joined', 'open'].indexOf(urlState.filter) !== -1
            ? urlState.filter
            : 'all';

        var filterState = useState(initialFilter);
        var activeFilter = filterState[0];
        var setActiveFilter = filterState[1];

        var initialTerm = config.initialSearchTerm !== undefined
            ? config.initialSearchTerm
            : (urlState.searchTerm || '');

        var searchState = useState(initialTerm);
        var searchTerm = searchState[0];
        var setSearchTerm = searchState[1];

        var statsState = useState({ visible: 0, total: config.totalCommunities || 0 });
        var stats = statsState[0];
        var setStats = statsState[1];

        var jumps = config.jumps || [];

        var applyMembershipFilter = useCallback(function (filter) {
            var result = CommunitiesIndexFilters.apply(filter, '', true);
            setStats({ visible: result.visible, total: result.total });
        }, []);

        useEffect(function () {
            CommunitiesIndexFilters.init({
                initialFilter: activeFilter,
                onChange: function (result) {
                    setStats({ visible: result.visible, total: result.total });
                }
            });

            function onExternalChange(e) {
                var detail = (e && e.detail) || {};
                setActiveFilter(detail.filter || 'all');
                setSearchTerm(detail.searchTerm || '');
            }

            window.addEventListener('communitiesIndexFiltersChanged', onExternalChange);

            return function () {
                window.removeEventListener('communitiesIndexFiltersChanged', onExternalChange);
            };
        }, []);

        useEffect(function () {
            applyMembershipFilter(activeFilter);
        }, [activeFilter, applyMembershipFilter]);

        useEffect(function () {
            if (skipSearchNavRef.current) {
                skipSearchNavRef.current = false;
                return;
            }

            if (debounceRef.current) {
                clearTimeout(debounceRef.current);
            }

            debounceRef.current = setTimeout(function () {
                var term = (searchTerm || '').trim();
                var params = new URLSearchParams(window.location.search);
                var currentTerm = (params.get('term') || '').trim();

                if (term.length > 0 && term.length < minChars) {
                    return;
                }

                if (term === currentTerm) {
                    return;
                }

                var nextUrl = CommunitiesIndexFilters.buildSearchUrl(term, activeFilter);
                if (nextUrl !== window.location.pathname + window.location.search) {
                    window.location.assign(nextUrl);
                }
            }, config.searchDebounceMs || 400);

            return function () {
                if (debounceRef.current) {
                    clearTimeout(debounceRef.current);
                }
            };
        }, [searchTerm, activeFilter, minChars]);

        function statusText() {
            var trimmed = (searchTerm || '').trim();
            if (trimmed.length > 0 && trimmed.length < minChars) {
                return 'Type at least ' + minChars + ' characters to search across name, region, country, organisation, and department';
            }
            if (stats.total === 0) {
                return 'No communities on this page';
            }
            if (stats.visible === stats.total) {
                return 'Showing all ' + stats.total + ' communit' + (stats.total === 1 ? 'y' : 'ies') + ' on this page';
            }
            if (stats.visible === 0) {
                return 'No communities match your filter on this page';
            }
            return 'Showing ' + stats.visible + ' of ' + stats.total + ' communities on this page';
        }

        return React.createElement(
            'div',
            { className: 'communities-nav-react' },
            jumps.length
                ? React.createElement(
                    'nav',
                    { className: 'communities-nav-jumps', 'aria-label': 'Communities page sections' },
                    jumps.map(function (jump) {
                        return React.createElement(
                            'button',
                            {
                                key: jump.id,
                                type: 'button',
                                className: 'communities-nav-jump-btn',
                                onClick: function () { scrollToId(jump.id); }
                            },
                            jump.label
                        );
                    })
                )
                : null,
            React.createElement(
                'div',
                { className: 'communities-filters communities-filters--react' },
                React.createElement(
                    'div',
                    { className: 'communities-search-bar' },
                    React.createElement('i', { className: 'fa fa-search communities-search-icon', 'aria-hidden': 'true' }),
                    React.createElement('input', {
                        type: 'search',
                        id: 'communities-search',
                        value: searchTerm,
                        placeholder: config.searchPlaceholder || 'Search by name, region, country, organisation, department…',
                        'aria-label': 'Search communities',
                        autoComplete: 'off',
                        onChange: function (e) { setSearchTerm(e.target.value); }
                    })
                ),
                React.createElement(
                    'div',
                    { className: 'communities-filter-buttons', role: 'tablist', 'aria-label': 'Community filters' },
                    FILTERS.map(function (filter) {
                        var isActive = activeFilter === filter.id;
                        return React.createElement(
                            'button',
                            {
                                key: filter.id,
                                type: 'button',
                                role: 'tab',
                                className: 'communities-filter-btn' + (isActive ? ' active' : ''),
                                'aria-selected': isActive ? 'true' : 'false',
                                onClick: function () { setActiveFilter(filter.id); }
                            },
                            filter.label
                        );
                    })
                ),
                React.createElement(
                    'p',
                    {
                        className: 'communities-nav-status',
                        'aria-live': 'polite',
                        'aria-atomic': 'true'
                    },
                    statusText()
                )
            )
        );
    }

    function mountCommunitiesNav() {
        var rootEl = document.getElementById('communities-nav-root');
        var configEl = document.getElementById('communities-nav-config');
        if (!rootEl || !configEl) {
            return;
        }

        var config = {};
        try {
            config = JSON.parse(configEl.textContent || '{}');
        } catch (e) {
            config = {};
        }

        ReactDOM.createRoot(rootEl).render(React.createElement(CommunitiesNavApp, { config: config }));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountCommunitiesNav);
    } else {
        mountCommunitiesNav();
    }
})();
