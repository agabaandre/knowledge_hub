/**
 * Client-side filter engine for the forums listing page.
 * Used by the React navigation bar and legacy handlers.
 */
(function () {
    var state = {
        forumsList: null,
        forumCards: [],
        initialOrder: [],
        searchInput: null,
        onChange: null
    };

    function cardMatchesSearch(card, searchTerm) {
        if (!searchTerm) {
            return true;
        }
        var titleEl = card.querySelector('.forum-title');
        var descEl = card.querySelector('.forum-description');
        var title = titleEl ? titleEl.textContent.toLowerCase() : '';
        var description = descEl ? descEl.textContent.toLowerCase() : '';
        var tags = Array.from(card.querySelectorAll('.tag')).map(function (t) {
            return t.textContent.toLowerCase();
        }).join(' ');

        return title.indexOf(searchTerm) !== -1
            || description.indexOf(searchTerm) !== -1
            || tags.indexOf(searchTerm) !== -1;
    }

    function restoreDefaultOrder() {
        if (!state.forumsList || !state.initialOrder.length) {
            return;
        }
        state.initialOrder.forEach(function (node) {
            state.forumsList.appendChild(node);
        });
    }

    function sortCardsInDom(compareFn) {
        if (!state.forumsList || !state.forumCards.length) {
            return;
        }
        state.forumCards.slice().sort(compareFn).forEach(function (card) {
            state.forumsList.appendChild(card);
        });
    }

    function countVisible() {
        var visible = 0;
        state.forumCards.forEach(function (card) {
            if (card.style.display !== 'none') {
                visible++;
            }
        });

        return visible;
    }

    function applyFilter(filter, searchTerm) {
        filter = filter || 'all';
        searchTerm = (searchTerm || '').trim().toLowerCase();

        restoreDefaultOrder();

        if (filter === 'recent') {
            sortCardsInDom(function (a, b) {
                var da = new Date(a.dataset.date || 0).getTime();
                var db = new Date(b.dataset.date || 0).getTime();
                return db - da;
            });
        } else if (filter === 'popular') {
            sortCardsInDom(function (a, b) {
                var ca = parseInt(a.dataset.comments, 10) || 0;
                var cb = parseInt(b.dataset.comments, 10) || 0;
                if (cb !== ca) {
                    return cb - ca;
                }
                var da = new Date(a.dataset.date || 0).getTime();
                var db = new Date(b.dataset.date || 0).getTime();
                return db - da;
            });
        }

        state.forumCards.forEach(function (card) {
            var shouldShow = true;

            if (filter === 'joined') {
                shouldShow = card.dataset.joined === 'true';
            }

            if (shouldShow && searchTerm) {
                shouldShow = cardMatchesSearch(card, searchTerm);
            }

            card.style.display = shouldShow ? 'block' : 'none';
        });

        var stats = {
            filter: filter,
            searchTerm: searchTerm,
            visible: countVisible(),
            total: state.forumCards.length
        };

        if (typeof state.onChange === 'function') {
            state.onChange(stats);
        }

        return stats;
    }

    function handlePopState() {
        window.dispatchEvent(new CustomEvent('forumsIndexFiltersChanged', { detail: readUrlState() }));
    }

    window.addEventListener('popstate', handlePopState);

    function readUrlState() {
        var params = new URLSearchParams(window.location.search);

        return {
            filter: params.get('filter') || 'all',
            searchTerm: params.get('q') || ''
        };
    }

    function writeUrlState(filter, searchTerm) {
        var params = new URLSearchParams(window.location.search);
        var normalizedFilter = filter && filter !== 'all' ? filter : '';
        var normalizedSearch = (searchTerm || '').trim();

        if (normalizedFilter) {
            params.set('filter', normalizedFilter);
        } else {
            params.delete('filter');
        }

        if (normalizedSearch) {
            params.set('q', normalizedSearch);
        } else {
            params.delete('q');
        }

        var qs = params.toString();
        var next = window.location.pathname + (qs ? '?' + qs : '');
        history.replaceState({ forumsNav: 1 }, '', next);
    }

    window.ForumsIndexFilters = {
        init: function (opts) {
            opts = opts || {};
            state.forumsList = document.getElementById('forums-list');
            state.forumCards = state.forumsList
                ? Array.from(state.forumsList.querySelectorAll('.forum-card'))
                : [];
            state.initialOrder = state.forumCards.slice();
            state.searchInput = document.getElementById('forum-search');
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
        writeUrlState: writeUrlState,
        refreshCards: function () {
            if (!state.forumsList) {
                state.forumsList = document.getElementById('forums-list');
            }
            state.forumCards = state.forumsList
                ? Array.from(state.forumsList.querySelectorAll('.forum-card'))
                : [];
            state.initialOrder = state.forumCards.slice();

            var urlState = readUrlState();
            return applyFilter(urlState.filter || 'all', urlState.searchTerm);
        }
    };
})();
