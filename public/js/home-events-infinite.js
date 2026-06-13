/**
 * Infinite scroll for homepage events section (6 events per batch).
 */
(function () {
    var observer = null;
    var loading = false;

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' events';
    }

    function disconnectObserver() {
        if (observer) {
            observer.disconnect();
            observer = null;
        }
        loading = false;
    }

    function updateFooter(root, data) {
        root.setAttribute('data-loaded', String(data.loaded_count || 0));
        root.setAttribute('data-total', String(data.total || 0));

        var status = document.getElementById('home-events-infinite-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var loader = document.getElementById('home-events-infinite-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var complete = document.getElementById('home-events-infinite-complete');
        var sentinel = document.getElementById('home-events-infinite-sentinel');

        if (data.has_more) {
            if (complete) {
                complete.remove();
            }
            if (!sentinel) {
                var footer = document.getElementById('home-events-infinite-footer');
                if (footer) {
                    sentinel = document.createElement('div');
                    sentinel.id = 'home-events-infinite-sentinel';
                    sentinel.className = 'home-events-infinite-sentinel';
                    sentinel.setAttribute('aria-hidden', 'true');
                    footer.insertBefore(sentinel, footer.firstChild);
                }
            }
        } else {
            if (sentinel) {
                sentinel.remove();
            }
            var footerEl = document.getElementById('home-events-infinite-footer');
            if (footerEl && !document.getElementById('home-events-infinite-complete')) {
                complete = document.createElement('p');
                complete.id = 'home-events-infinite-complete';
                complete.className = 'text-muted small text-center mb-0';
                complete.textContent = window.HOME_EVENTS_INFINITE_STATUS_COMPLETE || 'All events loaded';
                footerEl.appendChild(complete);
            }
            disconnectObserver();
        }
    }

    function loadMore(root) {
        var config = window.homeEventsInfiniteScrollConfig || {};
        if (!config.enabled || loading || !root) {
            return;
        }

        var loaded = parseInt(root.getAttribute('data-loaded') || '0', 10);
        var total = parseInt(root.getAttribute('data-total') || '0', 10);
        var pageSize = parseInt(root.getAttribute('data-page-size') || '6', 10);
        if (loaded >= total) {
            return;
        }

        var track = document.getElementById('eventsTrack');
        var loader = document.getElementById('home-events-infinite-loader');
        if (!track || !config.pageUrl) {
            return;
        }

        loading = true;
        if (loader) {
            loader.classList.remove('d-none');
        }

        var url = config.pageUrl + '?offset=' + encodeURIComponent(String(loaded)) + '&limit=' + encodeURIComponent(String(pageSize));

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('request failed');
                }
                return response.json();
            })
            .then(function (data) {
                if (!data.ok || !data.html) {
                    throw new Error('invalid payload');
                }

                track.insertAdjacentHTML('beforeend', data.html);
                updateFooter(root, data);

                if (data.has_more) {
                    window.initHomeEventsInfiniteScroll();
                }
            })
            .catch(function () {
                var footer = document.getElementById('home-events-infinite-footer');
                if (footer && !document.getElementById('home-events-infinite-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'home-events-infinite-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = window.HOME_EVENTS_INFINITE_STATUS_ERROR || 'Could not load more. Tap to retry.';
                    retry.addEventListener('click', function () {
                        retry.remove();
                        loadMore(root);
                    });
                    footer.appendChild(retry);
                }
            })
            .finally(function () {
                loading = false;
                if (loader) {
                    loader.classList.add('d-none');
                }
            });
    }

    window.initHomeEventsInfiniteScroll = function () {
        disconnectObserver();

        var root = document.getElementById('home-events-wrap');
        var sentinel = document.getElementById('home-events-infinite-sentinel');
        if (!root || root.getAttribute('data-infinite-scroll') !== '1' || !sentinel) {
            return;
        }

        var loaded = parseInt(root.getAttribute('data-loaded') || '0', 10);
        var total = parseInt(root.getAttribute('data-total') || '0', 10);
        if (loaded >= total) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            sentinel.addEventListener('click', function () {
                loadMore(root);
            });
            sentinel.style.height = '24px';
            sentinel.style.cursor = 'pointer';
            return;
        }

        observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadMore(root);
                }
            });
        }, { root: null, rootMargin: '240px 0px', threshold: 0.01 });

        observer.observe(sentinel);
    };

    document.addEventListener('DOMContentLoaded', function () {
        if ((window.homeEventsInfiniteScrollConfig || {}).enabled) {
            window.initHomeEventsInfiniteScroll();
        }
    });
})();
