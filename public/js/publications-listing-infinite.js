/**
 * Infinite scroll for publication listing blocks (country pages, etc.).
 */
(function () {
    var infiniteObserver = null;
    var infiniteLoading = false;

    function getConfig() {
        return window.publicationsListingInfiniteScrollConfig || {};
    }

    function disconnectInfiniteScroll() {
        if (infiniteObserver) {
            infiniteObserver.disconnect();
            infiniteObserver = null;
        }
        infiniteLoading = false;
    }

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' publications';
    }

    function updateFooter(scope, data) {
        var wrap = scope.querySelector('#records-search-publications');
        var footer = scope.querySelector('#records-search-infinite-footer');
        if (!wrap || !footer) {
            return;
        }

        wrap.setAttribute('data-current-page', String(data.current_page || 1));
        wrap.setAttribute('data-last-page', String(data.last_page || 1));
        wrap.setAttribute('data-total', String(data.total || 0));
        wrap.setAttribute('data-loaded', String(data.loaded_count || 0));

        var status = footer.querySelector('#records-search-infinite-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var loader = footer.querySelector('#records-search-infinite-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var complete = footer.querySelector('#records-search-infinite-complete');
        var sentinel = footer.querySelector('#records-search-infinite-sentinel');

        if (data.has_more) {
            if (complete) {
                complete.remove();
            }
            if (!sentinel) {
                sentinel = document.createElement('div');
                sentinel.id = 'records-search-infinite-sentinel';
                sentinel.className = 'records-search-infinite-sentinel';
                sentinel.setAttribute('aria-hidden', 'true');
                footer.appendChild(sentinel);
            }
        } else {
            if (sentinel) {
                sentinel.remove();
            }
            if (!complete) {
                complete = document.createElement('p');
                complete.id = 'records-search-infinite-complete';
                complete.className = 'text-muted small mb-0';
                complete.textContent = window.PUBLICATIONS_LISTING_INFINITE_COMPLETE || 'All publications loaded';
                footer.appendChild(complete);
            }
        }
    }

    function loadNextPage(scope) {
        var config = getConfig();
        if (!config.enabled || infiniteLoading) {
            return;
        }

        scope = scope || document;
        var wrap = scope.querySelector('#records-search-publications');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1') {
            return;
        }

        var currentPage = parseInt(wrap.getAttribute('data-current-page') || '1', 10);
        var lastPage = parseInt(wrap.getAttribute('data-last-page') || '1', 10);
        if (currentPage >= lastPage) {
            return;
        }

        var footer = scope.querySelector('#records-search-infinite-footer');
        var loader = footer ? footer.querySelector('#records-search-infinite-loader') : null;
        var list = scope.querySelector('#records-search-publications-list');
        if (!list || !config.pageUrl) {
            return;
        }

        infiniteLoading = true;
        if (loader) {
            loader.classList.remove('d-none');
        }

        var params = new URLSearchParams(window.location.search);
        params.delete('page');
        params.set('page', String(currentPage + 1));

        fetch(config.pageUrl + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('page failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data.ok || !data.html) {
                    throw new Error('invalid payload');
                }

                list.insertAdjacentHTML('beforeend', data.html);
                updateFooter(scope, data);

                if (data.has_more) {
                    window.initPublicationsListingInfiniteScroll(scope);
                } else {
                    disconnectInfiniteScroll();
                }
            })
            .catch(function () {
                if (footer && !footer.querySelector('#records-search-infinite-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'records-search-infinite-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = window.PUBLICATIONS_LISTING_INFINITE_ERROR || 'Could not load more. Tap to retry.';
                    retry.addEventListener('click', function () {
                        retry.remove();
                        loadNextPage(scope);
                    });
                    footer.appendChild(retry);
                }
            })
            .finally(function () {
                infiniteLoading = false;
                if (loader) {
                    loader.classList.add('d-none');
                }
            });
    }

    window.initPublicationsListingInfiniteScroll = function (scope) {
        var config = getConfig();
        if (!config.enabled) {
            disconnectInfiniteScroll();
            return;
        }

        disconnectInfiniteScroll();

        scope = scope || document;
        var wrap = scope.querySelector('#records-search-publications');
        var sentinel = scope.querySelector('#records-search-infinite-sentinel');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1' || !sentinel) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            sentinel.addEventListener('click', function () {
                loadNextPage(scope);
            });
            sentinel.style.height = '24px';
            sentinel.style.cursor = 'pointer';
            return;
        }

        infiniteObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadNextPage(scope);
                }
            });
        }, { root: null, rootMargin: '240px 0px', threshold: 0.01 });

        infiniteObserver.observe(sentinel);
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (getConfig().enabled) {
            window.initPublicationsListingInfiniteScroll(document);
        }
    });
})();
