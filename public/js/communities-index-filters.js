/**
 * Client-side filter engine for the communities listing page.
 * Text search is server-side via the `term` query param (min 4 characters).
 */
(function () {
    var state = {
        listRoot: null,
        items: [],
        initialOrder: [],
        onChange: null
    };

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

    function applyFilter(filter) {
        filter = filter || 'all';

        restoreDefaultOrder();

        state.items.forEach(function (item) {
            var shouldShow = true;

            if (filter === 'joined') {
                shouldShow = item.getAttribute('data-joined') === 'true';
            } else if (filter === 'open') {
                shouldShow = item.getAttribute('data-public') === 'true';
            }

            item.style.display = shouldShow ? '' : 'none';
        });

        var stats = {
            filter: filter,
            searchTerm: readUrlState().searchTerm || '',
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
        var legacyTerm = params.get('cq') || '';
        var term = params.get('term') || legacyTerm;

        return {
            filter: params.get('cfilter') || 'all',
            searchTerm: term
        };
    }

    function writeUrlState(filter) {
        var params = new URLSearchParams(window.location.search);
        var normalizedFilter = filter && filter !== 'all' ? filter : '';

        if (normalizedFilter) {
            params.set('cfilter', normalizedFilter);
        } else {
            params.delete('cfilter');
        }

        params.delete('cq');

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

            return applyFilter(filter);
        },
        apply: function (filter, _searchTerm, syncUrl) {
            if (syncUrl !== false) {
                writeUrlState(filter);
            }
            return applyFilter(filter);
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
            return applyFilter(urlState.filter || 'all');
        },
        buildSearchUrl: function (term, filter) {
            var params = new URLSearchParams(window.location.search);
            var normalized = (term || '').trim();

            params.delete('page');
            params.delete('cq');
            params.delete('coverage');
            params.delete('region_id');
            params.delete('country_id');
            params.delete('organisation');
            params.delete('department');

            if (normalized.length >= 4) {
                params.set('term', normalized);
            } else {
                params.delete('term');
            }

            if (filter && filter !== 'all') {
                params.set('cfilter', filter);
            } else {
                params.delete('cfilter');
            }

            var qs = params.toString();
            return window.location.pathname + (qs ? '?' + qs : '');
        }
    };
})();
