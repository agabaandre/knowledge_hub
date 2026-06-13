/**
 * Client-side filter engine for the communities listing page.
 */
(function () {
    var state = {
        listRoot: null,
        items: [],
        initialOrder: [],
        onChange: null
    };

    function itemMatchesSearch(item, searchTerm) {
        if (!searchTerm) {
            return true;
        }
        var haystack = (item.getAttribute('data-search') || item.textContent || '').toLowerCase();
        return haystack.indexOf(searchTerm) !== -1;
    }

    function restoreDefaultOrder() {
        if (!state.listRoot || !state.initialOrder.length) {
            return;
        }
        state.initialOrder.forEach(function (node) {
            state.listRoot.appendChild(node);
        });
    }

    function countVisible() {
        var visible = 0;
        state.items.forEach(function (item) {
            if (item.style.display !== 'none') {
                visible++;
            }
        });
        return visible;
    }

    function applyFilter(filter, searchTerm) {
        filter = filter || 'all';
        searchTerm = (searchTerm || '').trim().toLowerCase();

        restoreDefaultOrder();

        state.items.forEach(function (item) {
            var shouldShow = true;

            if (filter === 'joined') {
                shouldShow = item.getAttribute('data-joined') === 'true';
            } else if (filter === 'open') {
                shouldShow = item.getAttribute('data-public') === 'true';
            }

            if (shouldShow && searchTerm) {
                shouldShow = itemMatchesSearch(item, searchTerm);
            }

            item.style.display = shouldShow ? '' : 'none';
        });

        var stats = {
            filter: filter,
            searchTerm: searchTerm,
            visible: countVisible(),
            total: state.items.length
        };

        if (typeof state.onChange === 'function') {
            state.onChange(stats);
        }

        return stats;
    }

    function readUrlState() {
        var params = new URLSearchParams(window.location.search);
        return {
            filter: params.get('cfilter') || 'all',
            searchTerm: params.get('cq') || ''
        };
    }

    function writeUrlState(filter, searchTerm) {
        var params = new URLSearchParams(window.location.search);
        var normalizedFilter = filter && filter !== 'all' ? filter : '';
        var normalizedSearch = (searchTerm || '').trim();

        if (normalizedFilter) {
            params.set('cfilter', normalizedFilter);
        } else {
            params.delete('cfilter');
        }

        if (normalizedSearch) {
            params.set('cq', normalizedSearch);
        } else {
            params.delete('cq');
        }

        var qs = params.toString();
        history.replaceState({ communitiesNav: 1 }, '', window.location.pathname + (qs ? '?' + qs : ''));
    }

    function handlePopState() {
        window.dispatchEvent(new CustomEvent('communitiesIndexFiltersChanged', { detail: readUrlState() }));
    }

    window.addEventListener('popstate', handlePopState);

    window.CommunitiesIndexFilters = {
        init: function (opts) {
            opts = opts || {};
            state.listRoot = document.getElementById('communities-list');
            state.items = state.listRoot
                ? Array.from(state.listRoot.querySelectorAll('.communities-list-item'))
                : [];
            state.initialOrder = state.items.slice();
            state.onChange = opts.onChange || null;

            var urlState = readUrlState();
            var filter = opts.initialFilter || urlState.filter || 'all';
            var searchTerm = opts.initialSearch !== undefined ? opts.initialSearch : urlState.searchTerm;

            return applyFilter(filter, searchTerm);
        },
        apply: function (filter, searchTerm, syncUrl) {
            if (syncUrl !== false) {
                writeUrlState(filter, searchTerm);
            }
            return applyFilter(filter, searchTerm);
        },
        readUrlState: readUrlState,
        refreshItems: function () {
            if (!state.listRoot) {
                state.listRoot = document.getElementById('communities-list');
            }
            state.items = state.listRoot
                ? Array.from(state.listRoot.querySelectorAll('.communities-list-item'))
                : [];
            state.initialOrder = state.items.slice();

            var urlState = readUrlState();
            return applyFilter(urlState.filter || 'all', urlState.searchTerm);
        }
    };
})();
