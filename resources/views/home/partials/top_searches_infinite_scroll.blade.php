<script>
(function () {
    var PAGE_URL = @json(route('home.top-searches-page'));
    var LOADING_TEXT = @json(__('publications.search.loading_more'));
    var COMPLETE_TEXT = @json(__('publications.search.all_results_loaded'));
    var ERROR_TEXT = @json(__('publications.search.load_more_error'));
    var observer = null;
    var loading = false;

    function formatStatus(loaded, total) {
        return 'Showing ' + Number(loaded).toLocaleString() + ' of ' + Number(total).toLocaleString() + ' resources';
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

        var status = document.getElementById('home-top-searches-status');
        if (status) {
            status.textContent = formatStatus(data.loaded_count || 0, data.total || 0);
        }

        var loader = document.getElementById('home-top-searches-loader');
        if (loader) {
            loader.classList.add('d-none');
        }

        var footer = document.getElementById('home-top-searches-footer');
        if (!data.has_more) {
            disconnectObserver();
            if (footer) {
                footer.remove();
            }
            if (!document.getElementById('home-top-searches-complete')) {
                var complete = document.createElement('p');
                complete.id = 'home-top-searches-complete';
                complete.className = 'text-muted small text-center py-2 mb-0';
                complete.textContent = COMPLETE_TEXT;
                root.appendChild(complete);
            }
        }
    }

    function loadMore(root) {
        if (loading || !root) {
            return;
        }

        var loaded = parseInt(root.getAttribute('data-loaded') || '0', 10);
        var total = parseInt(root.getAttribute('data-total') || '0', 10);
        var pageSize = parseInt(root.getAttribute('data-page-size') || '10', 10);
        if (loaded >= total) {
            return;
        }

        var list = document.getElementById('home-top-searches-list');
        var loader = document.getElementById('home-top-searches-loader');
        if (!list) {
            return;
        }

        loading = true;
        if (loader) {
            loader.classList.remove('d-none');
        }

        var url = PAGE_URL + '?offset=' + encodeURIComponent(String(loaded)) + '&limit=' + encodeURIComponent(String(pageSize));

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

                list.insertAdjacentHTML('beforeend', data.html);
                updateFooter(root, data);

                if (data.has_more) {
                    window.initHomeTopSearchesInfiniteScroll();
                }
            })
            .catch(function () {
                var footer = document.getElementById('home-top-searches-footer');
                if (footer && !document.getElementById('home-top-searches-retry')) {
                    var retry = document.createElement('button');
                    retry.type = 'button';
                    retry.id = 'home-top-searches-retry';
                    retry.className = 'btn btn-sm btn-outline-secondary mt-2';
                    retry.textContent = ERROR_TEXT;
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

    window.initHomeTopSearchesInfiniteScroll = function () {
        disconnectObserver();

        var root = document.getElementById('home-top-searches');
        var sentinel = document.getElementById('home-top-searches-sentinel');
        if (!root || !sentinel) {
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
        window.initHomeTopSearchesInfiniteScroll();
    });
})();
</script>
