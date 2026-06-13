/**
 * Infinite scroll for health topics (6 topics per batch, letter groupings preserved).
 */
(function () {
    var infiniteObserver = null;
    var infiniteLoading = false;

    function disconnectInfiniteScroll() {
        if (infiniteObserver) {
            infiniteObserver.disconnect();
            infiniteObserver = null;
        }
        infiniteLoading = false;
    }

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' topics';
    }

    function appendTopicsHtml(list, html, mergeLetter) {
        if (mergeLetter) {
            var grid = document.querySelector('#letter-' + mergeLetter + ' .topics-grid');
            if (grid) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                temp.querySelectorAll('.topic-card').forEach(function (card) {
                    grid.appendChild(card);
                });
                return;
            }
        }
        list.insertAdjacentHTML('beforeend', html);
    }

    function updateFooter(wrap, data) {
        var footer = document.getElementById('health-topics-infinite-footer');
        if (!wrap || !footer) {
            return;
        }

        wrap.setAttribute('data-current-page', String(data.current_page || 1));
        wrap.setAttribute('data-last-page', String(data.last_page || 1));
        wrap.setAttribute('data-total', String(data.total || 0));
        wrap.setAttribute('data-loaded', String(data.loaded_count || 0));

        var status = footer.querySelector('#health-topics-infinite-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var loader = footer.querySelector('#health-topics-infinite-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var complete = footer.querySelector('#health-topics-infinite-complete');
        var sentinel = footer.querySelector('#health-topics-infinite-sentinel');

        if (data.has_more) {
            if (complete) {
                complete.remove();
            }
            if (!sentinel) {
                sentinel = document.createElement('div');
                sentinel.id = 'health-topics-infinite-sentinel';
                sentinel.className = 'health-topics-infinite-sentinel';
                sentinel.setAttribute('aria-hidden', 'true');
                footer.appendChild(sentinel);
            }
        } else {
            if (sentinel) {
                sentinel.remove();
            }
            if (!complete) {
                complete = document.createElement('p');
                complete.id = 'health-topics-infinite-complete';
                complete.className = 'text-muted small mb-0 text-center';
                complete.textContent = window.HEALTH_TOPICS_INFINITE_STATUS_COMPLETE || 'All topics loaded';
                footer.appendChild(complete);
            }
        }
    }

    function loadNextPage() {
        var config = window.healthTopicsInfiniteScrollConfig || {};
        if (!config.enabled || infiniteLoading) {
            return;
        }

        var wrap = document.getElementById('health-topics-list-wrap');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1') {
            return;
        }

        var currentPage = parseInt(wrap.getAttribute('data-current-page') || '1', 10);
        var lastPage = parseInt(wrap.getAttribute('data-last-page') || '1', 10);
        if (currentPage >= lastPage) {
            return;
        }

        var footer = document.getElementById('health-topics-infinite-footer');
        var loader = footer ? footer.querySelector('#health-topics-infinite-loader') : null;
        var list = document.getElementById('health-topics-content');
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

                appendTopicsHtml(list, data.html, data.merge_letter || '');
                updateFooter(wrap, data);

                if (data.has_more) {
                    window.initHealthTopicsInfiniteScroll();
                } else {
                    disconnectInfiniteScroll();
                }
            })
            .catch(function () {
                if (footer && !footer.querySelector('#health-topics-infinite-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'health-topics-infinite-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = window.HEALTH_TOPICS_INFINITE_STATUS_ERROR || 'Could not load more. Tap to retry.';
                    retry.addEventListener('click', function () {
                        retry.remove();
                        loadNextPage();
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

    window.initHealthTopicsInfiniteScroll = function () {
        var config = window.healthTopicsInfiniteScrollConfig || {};
        if (!config.enabled) {
            disconnectInfiniteScroll();
            return;
        }

        disconnectInfiniteScroll();

        var wrap = document.getElementById('health-topics-list-wrap');
        var sentinel = document.getElementById('health-topics-infinite-sentinel');
        if (!wrap || wrap.getAttribute('data-infinite-scroll') !== '1' || !sentinel) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            sentinel.addEventListener('click', loadNextPage);
            sentinel.style.height = '24px';
            sentinel.style.cursor = 'pointer';
            return;
        }

        infiniteObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadNextPage();
                }
            });
        }, { root: null, rootMargin: '240px 0px', threshold: 0.01 });

        infiniteObserver.observe(sentinel);
    };

    document.addEventListener('DOMContentLoaded', function () {
        if ((window.healthTopicsInfiniteScrollConfig || {}).enabled) {
            window.initHealthTopicsInfiniteScroll();
        }
    });
})();
